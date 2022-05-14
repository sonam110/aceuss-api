<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IpFollowUp;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\IpAssigneToEmployee;
use App\Models\IpTemplate;
use App\Models\PersonalInfoDuringIp;
use App\Models\Question;
use App\Models\EmailTemplate;
use App\Models\FollowupComplete;
use App\Models\UserType;
use Validator;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\WelcomeMail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class FollowUpsController extends Controller
{
     public function __construct()
    {

        $this->middleware('permission:followup-browse',['except' => ['show']]);
        $this->middleware('permission:followup-add', ['only' => ['store']]);
        $this->middleware('permission:followup-edit', ['only' => ['update']]);
        $this->middleware('permission:followup-read', ['only' => ['show']]);
        $this->middleware('permission:followup-delete', ['only' => ['destroy']]);
        
    }
	public function followups(Request $request)
    {
        try {
	        $user = getUser();
            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
	        $whereRaw = $this->getWhereRawFromRequest($request);
            $query = IpFollowUp::with('ActionByUser:id,name,email','PatientImplementationPlan.patient')
                ->where('is_latest_entry', 1);
            if($user->user_type_id =='2'){
                $query = $query->orderBy('id','DESC');
            } else{
                $query =  $query->whereIn('branch_id',$allChilds);
            }
            if($whereRaw != '') { 
                $query =  $query->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = $query->orderBy('id', 'DESC');
            }
            
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Follow ups list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Follow ups list",$query,config('httpcodes.success'));
	    
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    	
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$data = $request->repeat_datetime;
            $validator = Validator::make($request->all(),[
                // 'ip_id' => 'required|exists:patient_implementation_plans,id',   
                'title' => 'required',   
                // 'description' => 'required',         
                "repeat_datetime.*.start_date"  => "required", 
                "repeat_datetime.*.start_time"  => "required",     
            ],
            [
                //'ip_id.required' =>  getLangByLabelGroups('FollowUp','ip_id'),   
                'title.required' =>  getLangByLabelGroups('FollowUp','title'),   
                // 'description.required' =>  getLangByLabelGroups('FollowUp','description'),           
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        	$ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
        	if(!$ipCheck) {
              	return prepareResult(false,getLangByLabelGroups('FollowUp','ip_id'),[], config('httpcodes.not_found')); 
        	}
            if(is_array($request->repeat_datetime)   && sizeof($request->repeat_datetime) > 0){
                $ipfollowupsId = [];
                foreach ($request->repeat_datetime as $key => $followup) {
                    if(!empty($followup['start_date']))
                    {
            	        $ipFollowups = new IpFollowUp;
            		 	$ipFollowups->ip_id = $request->ip_id ;
                        $ipFollowups->branch_id = getBranchId() ;
            		 	$ipFollowups->top_most_parent_id = $user->top_most_parent_id;
            		 	$ipFollowups->title = $request->title;
            		 	$ipFollowups->description = $request->description;
                        $ipFollowups->start_date = $followup['start_date'];
                        $ipFollowups->start_time = $followup['start_time'];
            		 	$ipFollowups->end_date = $followup['end_date'];
            		 	$ipFollowups->end_time = $followup['end_time'];
            		 	$ipFollowups->remarks = $request->remarks;
                        $ipFollowups->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            		 	$ipFollowups->created_by = $user->id;
                        $ipFollowups->documents = !empty($request->documents) ? json_encode($request->documents) : null;
                        $ipFollowups->emp_id = !empty($request->emp_id) ? json_encode($request->emp_id) : null;
                        $ipFollowups->is_latest_entry = 1;
            		 	$ipFollowups->save();
                        $ipfollowupsId[] = $ipFollowups->id;
            		 	
                        /*-----------------Persons Informationn ----------------*/
                        if(is_array($request->persons)  && sizeof($request->persons) > 0 ){
                            foreach ($request->persons as $key => $value) {
                                if(!empty($value['name']))
                                {
                                    $is_user = false;
                                    if(@$value['is_family_member'] == true){
                                        $user_type_id ='8';
                                        $is_user = true;
                                    }
                                    if(@$value['is_caretaker'] == true){
                                        $user_type_id ='7';
                                        $is_user = true;
                                    }
                                    if((@$value['is_caretaker'] == true) && (@$value['is_family_member'] == true )){
                                        $user_type_id ='10';
                                        $is_user = true;
                                    }
                                    if(@$value['is_contact_person'] == true){
                                        $user_type_id ='9';
                                        $is_user = true;
                                    }
                                    if(is_null(@$value['id']) == false){
                                        $personalInfo = PersonalInfoDuringIp::find(@$value['id']);
                                        $getperson = PersonalInfoDuringIp::where('id',@$value['id'])->first();
                                        $getUser = User::where('email',$getperson->email)->first();
                                    } else{
                                        $personalInfo = new PersonalInfoDuringIp;
                                    }
                                    $personalInfo->patient_id =$ipCheck->user_id;
                                    $personalInfo->ip_id = $request->ip_id ;
                                    $personalInfo->follow_up_id = $ipFollowups->id ;
                                    $personalInfo->name = @$value['name'] ;
                                    $personalInfo->email = @$value['email'] ;
                                    $personalInfo->contact_number = @$value['contact_number'];
                                    $personalInfo->country_id = @$value['country_id'];
                                    $personalInfo->city = @$value['city'];
                                    $personalInfo->postal_area = @$value['postal_area'];
                                    $personalInfo->zipcode = @$value['zipcode'];
                                    $personalInfo->full_address = @$value['full_address'] ;
                                    $personalInfo->personal_number = @$value['personal_number'] ;
                                    $personalInfo->is_family_member = (@$value['is_family_member'] == true) ? @$value['is_family_member'] : 0 ;
                                    $personalInfo->is_caretaker = (@$value['is_caretaker'] == true) ? @$value['is_caretaker'] : 0 ;
                                    $personalInfo->is_contact_person = (@$value['is_contact_person'] == true) ? @$value['is_contact_person'] : 0 ;
                                    $personalInfo->is_guardian = (@$value['is_guardian'] == true) ? @$value['is_guardian'] : 0 ;
                                    $personalInfo->is_other = (@$value['is_other'] == true) ? @$value['is_other'] : 0 ;
                                    $personalInfo->is_presented = (@$value['is_presented'] == true) ? @$value['is_presented'] : 0 ;
                                    $personalInfo->is_participated = (@$value['is_participated'] == true) ? @$value['is_participated'] : 0 ;
                                    $personalInfo->how_helped = @$value['how_helped'];
                                    $personalInfo->is_other_name = @$value['is_other_name'];
                                    $personalInfo->save() ;
                                    /*-----Create Account /Entry in user table*/
                                    if($is_user == true) {
                                        $top_most_parent_id = auth()->user()->top_most_parent_id;
                                        $checkAlreadyUser = User::where('email',@$value['email'])->first();
                                        if(empty($checkAlreadyUser)) {
                                            $getUserType = UserType::find($user_type_id);
                                            $roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);

                                            if(!empty($getUser)){
                                                $userSave = User::find($getUser->id);
                                            } else {
                                                $userSave = new User;
                                                $userSave->unique_id = generateRandomNumber();
                                            }
                                            $userSave->user_type_id = $user_type_id;
                                            $userSave->branch_id = getBranchId();
                                            $userSave->role_id =  $roleInfo->id;
                                            $userSave->parent_id = $user->id;
                                            $userSave->top_most_parent_id = $top_most_parent_id;
                                            $userSave->name = @$value['name'] ;
                                            $userSave->email = @$value['email'] ;
                                            $userSave->password = Hash::make('12345678');
                                            $userSave->contact_number = @$value['contact_number'];
                                            $userSave->country_id = @$value['country_id'];
                                            $userSave->city = @$value['city'];
                                            $userSave->postal_area = @$value['postal_area'];
                                            $userSave->zipcode = @$value['zipcode'];
                                            $userSave->full_address = @$value['full_address'] ;
                                            $userSave->save(); 

                                            //update personal_info_during_ips
                                            $personalInfo->user_id =$userSave->id;
                                            $personalInfo->save();

                                            if(!empty($user_type_id))
                                            {
                                               $role = $roleInfo;
                                               $userSave->assignRole($role->name);
                                            }     
                                            if(env('IS_MAIL_ENABLE',false) == true){ 
                                                    $variables = ([
                                                    'name' => $userSave->name,
                                                    'email' => $userSave->email,
                                                    'contact_number' => $userSave->contact_number,
                                                    'city' => $userSave->city,
                                                    'zipcode' => $userSave->zipcode,
                                                    ]);   
                                                $emailTem = EmailTemplate::where('id','2')->first();           
                                                $content = mailTemplateContent($emailTem->content,$variables);
                                                Mail::to($userSave->email)->send(new WelcomeMail($content));
                                            }
                                            
                                        }
                                    }
                                }

                            }
                        }

                        /*----------------Question Data--------------------*/
                        if(is_array($request->questions)  && sizeof($request->questions) > 0 ){
                            foreach ($request->questions as $key => $ques) {
                                if(!empty($ques))
                                {
                                    $getQues = Question::where('id',$ques)->first();
                                    $question = new FollowupComplete;
                                    $question->follow_up_id = $ipFollowups->id;;
                                    $question->question_id = $ques;
                                    $question->question = ($getQues) ? $getQues->question : null;
                                    $question->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                                    $question->save();
                                }
                            }
                        }
                    }
                }
            }
		 	DB::commit();
            
            $data = IpFollowUp::with('ActionByUser:id,name,email','PatientImplementationPlan.patient')
                ->where('is_latest_entry', 1)->whereIn('id', $ipfollowupsId)->get();
	        return prepareResult(true,getLangByLabelGroups('FollowUp','create') ,$data, config('httpcodes.success'));
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
                'ip_id' => 'required|exists:patient_implementation_plans,id',   
                'title' => 'required',   
                'description' => 'required',         
                "repeat_datetime.*.start_date"  => "required", 
                "repeat_datetime.*.start_time"  => "required",        
            ],
            [
                'ip_id.required' =>  getLangByLabelGroups('FollowUp','ip_id'),   
                'title.required' =>  getLangByLabelGroups('FollowUp','title'),   
                'description.required' =>  getLangByLabelGroups('FollowUp','description'),           
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
            if(!$ipCheck) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','ip_id'),[], config('httpcodes.not_found')); 
            }
        	
        	$checkId = IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],config('httpcodes.not_found'));
            }
            if(is_array($request->repeat_datetime)  && sizeof($request->repeat_datetime) > 0 ){
                $ipfollowupsId = [];
                foreach ($request->repeat_datetime as $key => $followup) {
                    if(!empty($followup['start_date']))
                    {
                        $parent_id  = (empty($checkId->parent_id)) ? $id : $checkId->parent_id;
                        $getFollowInfo = IpFollowUp::find($parent_id);
                        if($getFollowInfo)
                        {
                            //new entry create for log
                            $ipFollowups = $getFollowInfo->replicate();
                            $ipFollowups->parent_id = $getFollowInfo->id;
                            $ipFollowups->is_latest_entry = 0;
                            $ipFollowups->created_at = $getFollowInfo->created_at;
                            $ipFollowups->save();

                            //update Existing or current record
                            $getFollowInfo->ip_id = $request->ip_id ;
                            $getFollowInfo->branch_id = getBranchId() ;
                            $getFollowInfo->parent_id = null;
                            $getFollowInfo->title = $request->title;
                            $getFollowInfo->description = $request->description;
                            $getFollowInfo->start_date = $followup['start_date'];
                            $getFollowInfo->start_time = $followup['start_time'];
                            $getFollowInfo->end_date = $followup['end_date'];
                            $getFollowInfo->end_time = $followup['end_time'];
                            $getFollowInfo->remarks = $request->remarks;
                            $getFollowInfo->reason_for_editing = $request->reason_for_editing;
                            $getFollowInfo->edited_by = $user->id;
                            $getFollowInfo->documents = !empty($request->documents) ? json_encode($request->documents) : null;
                            $getFollowInfo->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                            $getFollowInfo->is_latest_entry = 1;
                            $getFollowInfo->created_at = Carbon::now();
                            $getFollowInfo->save();
                        }
                        else
                        {
                            $ipFollowups =  new  IpFollowUp;
                            $ipFollowups->ip_id = $request->ip_id ;
                            $ipFollowups->branch_id = getBranchId() ;
                            $ipFollowups->parent_id = $parent_id;
                            $ipFollowups->top_most_parent_id = $user->top_most_parent_id;
                            $ipFollowups->title = $request->title;
                            $ipFollowups->description = $request->description;
                            $ipFollowups->start_date = $followup['start_date'];
                            $ipFollowups->start_time = $followup['start_time'];
                            $ipFollowups->end_date = $followup['end_date'];
                            $ipFollowups->end_time = $followup['end_time'];
                            $ipFollowups->remarks = $request->remarks;
                            $ipFollowups->reason_for_editing = $request->reason_for_editing;
                            $ipFollowups->edited_by = $user->id;
                            $ipFollowups->documents = !empty($request->documents) ? json_encode($request->documents) : null;
                            $ipFollowups->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                            $ipFollowups->is_latest_entry = 1;
                            $ipFollowups->save();
                        }
                        $ipfollowupsId[] = $ipFollowups->id;
                        
                        /*-----------------Persons Informationn ----------------*/
                        if(is_array($request->persons)  && sizeof($request->persons) > 0 ){
                            foreach ($request->persons as $key => $value) {
                                if(!empty($value['name']))
                                {
                                    $is_user = false;
                                    if(@$value['is_family_member'] == true){
                                        $user_type_id ='8';
                                        $is_user = true;
                                    }
                                    if(@$value['is_caretaker'] == true){
                                        $user_type_id ='7';
                                        $is_user = true;
                                    }
                                    if((@$value['is_caretaker'] == true) && (@$value['is_family_member'] == true )){
                                        $user_type_id ='10';
                                        $is_user = true;
                                    }
                                    if(@$value['is_contact_person'] == true){
                                        $user_type_id ='9';
                                        $is_user = true;
                                    }
                                    if(is_null(@$value['id']) == false){
                                        $personalInfo = PersonalInfoDuringIp::find(@$value['id']);
                                        $getperson = PersonalInfoDuringIp::where('id',@$value['id'])->first();
                                        $getUser = User::where('email',$getperson->email)->first();
                                    } else{
                                        $personalInfo = new PersonalInfoDuringIp;
                                    }
                                    $personalInfo->ip_id =$request->ip_id;
                                    $personalInfo->follow_up_id = $ipFollowups->id ;
                                    $personalInfo->patient_id =$ipCheck->user_id;
                                    $personalInfo->name = @$value['name'] ;
                                    $personalInfo->email = @$value['email'] ;
                                    $personalInfo->contact_number = @$value['contact_number'];
                                    $personalInfo->country_id = @$value['country_id'];
                                    $personalInfo->city = @$value['city'];
                                    $personalInfo->postal_area = @$value['postal_area'];
                                    $personalInfo->zipcode = @$value['zipcode'];
                                    $personalInfo->full_address = @$value['full_address'] ;
                                    $personalInfo->personal_number = @$value['personal_number'] ;
                                    $personalInfo->is_family_member = (@$value['is_family_member'] == true) ? @$value['is_family_member'] : 0 ;
                                    $personalInfo->is_caretaker = (@$value['is_caretaker'] == true) ? @$value['is_caretaker'] : 0 ;
                                    $personalInfo->is_contact_person = (@$value['is_contact_person'] == true) ? @$value['is_contact_person'] : 0 ;
                                    $personalInfo->is_guardian = (@$value['is_guardian'] == true) ? @$value['is_guardian'] : 0 ;
                                    $personalInfo->is_other = (@$value['is_other'] == true) ? @$value['is_other'] : 0 ;
                                    $personalInfo->is_presented = (@$value['is_presented'] == true) ? @$value['is_presented'] : 0 ;
                                    $personalInfo->is_participated = (@$value['is_participated'] == true) ? @$value['is_participated'] : 0 ;
                                    $personalInfo->how_helped = @$value['how_helped'];
                                    $personalInfo->is_other_name = @$value['is_other_name'];
                                    $personalInfo->save() ;
                                    /*-----Create Account /Entry in user table*/
                                    if($is_user == true) {
                                        $top_most_parent_id = auth()->user()->top_most_parent_id;
                                        $checkAlreadyUser = User::where('email',@$value['email'])->first();
                                        if(empty($checkAlreadyUser)) {
                                            $getUserType = UserType::find($user_type_id);
                                            $roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);

                                            if(!empty($getUser)){
                                                $userSave = User::find($getUser->id);
                                            } else {
                                                $userSave = new User;
                                                $userSave->unique_id = generateRandomNumber();
                                            }
                                            $userSave->user_type_id = $user_type_id;
                                            $userSave->branch_id = getBranchId();
                                            $userSave->role_id =  $roleInfo->id;
                                            $userSave->parent_id = $user->id;
                                            $userSave->top_most_parent_id = $top_most_parent_id;
                                            $userSave->name = @$value['name'] ;
                                            $userSave->email = @$value['email'] ;
                                            $userSave->password = Hash::make('12345678');
                                            $userSave->contact_number = @$value['contact_number'];
                                            $userSave->country_id = @$value['country_id'];
                                            $userSave->city = @$value['city'];
                                            $userSave->postal_area = @$value['postal_area'];
                                            $userSave->zipcode = @$value['zipcode'];
                                            $userSave->full_address = @$value['full_address'] ;
                                            $userSave->save();
                                            
                                            //update personal_info_during_ips
                                            $personalInfo->user_id =$userSave->id;
                                            $personalInfo->save();
                                            
                                            if(env('IS_MAIL_ENABLE',false) == true){ 
                                                    $variables = ([
                                                    'name' => $userSave->name,
                                                    'email' => $userSave->email,
                                                    'contact_number' => $userSave->contact_number,
                                                    'city' => $userSave->city,
                                                    'zipcode' => $userSave->zipcode,
                                                    ]);   
                                                $emailTem = EmailTemplate::where('id','2')->first();           
                                                $content = mailTemplateContent($emailTem->content,$variables);
                                                Mail::to($userSave->email)->send(new WelcomeMail($content));
                                            }
                                            if(!empty($user_type_id))
                                            {
                                               $role = $roleInfo;
                                               $userSave->assignRole($role->name);
                                            }
                                        }
                                    }
                                }

                                
                            }
                        }

                       
                         /*----------------Question Data--------------------*/
                        if(is_array($request->questions)  && sizeof($request->question) > 0 ){
                            foreach ($request->questions as $key => $ques) {
                                if(!empty($ques))
                                {
                                    $getQues = Question::where('id',$ques)->first();
                                    $question = new FollowupComplete;
                                    $question->follow_up_id = $ipFollowups->id;;
                                    $question->question_id = $ques;
                                    $question->question = ($getQues) ? $getQues->question : null;
                                    $question->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                                    $question->save();
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();

            $data = IpFollowUp::with('ActionByUser:id,name,email','PatientImplementationPlan.patient')
                ->where('is_latest_entry', 1)->whereIn('id', $ipfollowupsId)->get();
	        return prepareResult(true,getLangByLabelGroups('FollowUp','update') ,$data, config('httpcodes.success'));
			  
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$IpFollowUp = IpFollowUp::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('FollowUp','delete') ,[], config('httpcodes.success'));
		     	
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$ipFollowups = IpFollowUp::where('id',$id)->with('persons.Country','questions','PatientImplementationPlan.patient','ActionByUser:id,name,email')->first();
	        return prepareResult(true,'View Patient plan' ,$ipFollowups, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function approvedIpFollowUp(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'id' => 'required',   
	        ],
            [
            'id' =>  getLangByLabelGroups('FollowUp','id'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$id = $request->id;
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],config('httpcodes.not_found'));
            }
            $ipFollowups = IpFollowUp::find($id);
		 	$ipFollowups->approved_by = $user->id;
		 	$ipFollowups->approved_date = date('Y-m-d');
		 	$ipFollowups->status = '1';
		 	$ipFollowups->save();
	        return prepareResult(true,getLangByLabelGroups('FollowUp','approve') ,$ipFollowups, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function followUpComplete(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'follow_up_id' => 'required|exists:ip_follow_ups,id',   
                //'witness' => 'required',   
                //'witness.*' => 'required|distinct|exists:users,id',   
            ],
            [
            'id' =>  getLangByLabelGroups('FollowUp','id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->follow_up_id;
            $checkId= IpFollowUp::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],config('httpcodes.not_found'));
            }

            $witness = null;
            if(is_array($request->witness) && sizeof($request->witness)>0)
            {
                $witness = json_encode($request->witness);
            }

            $more_witness = null;
            if(is_array($request->more_witness) && sizeof($request->more_witness)>0)
            {
                $more_witness = json_encode($request->more_witness);
            }

            $ipFollowups = IpFollowUp::find($id);
            $ipFollowups->is_completed = 1;
            $ipFollowups->status = 2;
            $ipFollowups->action_by = $user->id;
            $ipFollowups->action_date = date('Y-m-d H:i:s');
            $ipFollowups->comment = $request->comment;
            $ipFollowups->witness = $witness;
            $ipFollowups->more_witness = $more_witness;
            $ipFollowups->save();
            if(is_array($request->question_answer) ){
                foreach ($request->question_answer as $key => $ans) {
                    if(!empty($ans['answer']))
                    {
                        $checkQuestion = FollowupComplete::where('follow_up_id',$request->follow_up_id)->where('question_id',$ans['question_id'])->first();
                        if(is_object($checkQuestion)){
                            $question = FollowupComplete::find($checkQuestion->id);
                            $question->answer = $ans['answer'];
                            $question->save();
                        }else {
                            $getQues = Question::where('id',$ans['question_id'])->first();
                            $question = new FollowupComplete;
                            $question->follow_up_id = $request->follow_up_id;;
                            $question->question_id = $ans['question_id'];
                            $question->question = ($getQues) ? $getQues->question : null;
                            $question->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                            $question->answer = $ans['answer'];
                            $question->save();
                        }
                    }
                }
            }
            return prepareResult(true,'Followup completed' ,$ipFollowups, config('httpcodes.success'));
            
        
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }
    public function followupEditHistory(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                 'parent_id' => 'required|exists:ip_follow_ups,id',   
            ],
            [
            'parent_id' =>  'Parent id is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->parent_id;
            $parent_id = 
            $query= IpFollowUp::where('parent_id',$id);
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Edited Folllowup list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,'Edited Folllowup List' ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    private function getWhereRawFromRequest(Request $request) {
        $w = '';

        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "ip_id = "."'" .$request->input('ip_id')."'".")";
        }
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
         if (is_null($request->start_date) == false || is_null($request->end_date) == false) {
           
            if ($w != '') {$w = $w . " AND ";}

            if ($request->start_date != '')
            {
              $w = $w . "("."start_date >= '".date('y-m-d',strtotime($request->start_date))."')";
            }
            if (is_null($request->start_date) == false && is_null($request->end_date) == false) 
                {

              $w = $w . " AND ";
            }
            if ($request->end_date != '')
            {
                $w = $w . "("."start_date <= '".date('y-m-d',strtotime($request->end_date))."')";
            }
            
          
           
        }
        if (is_null($request->input('title')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "title like '%" .trim(strtolower($request->input('title'))) . "%')";

             
        }
        if (is_null($request->input('title')) == false) {
            if ($w != '') {$w = $w . " OR ";}
             $w = $w . "(" . "description like '%" .trim(strtolower($request->input('title'))) . "%')";
        }

        return($w);

    }
    
}
