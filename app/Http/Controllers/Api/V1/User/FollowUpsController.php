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
use App\Models\EmailTemplate;
use Validator;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\WelcomeMail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class FollowUpsController extends Controller
{
	public function followups(Request $request)
    {
        try {
	        $user = getUser();
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  IpFollowUp::whereRaw($whereRaw)
                ->with('Parent:id,title','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','PatientImplementationPlan')
                ->orderBy('id', 'DESC');
            } else {
                $query = IpFollowUp::with('Parent:id,title','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','PatientImplementationPlan')
                ->orderBy('id', 'DESC');
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
                return prepareResult(true,"Follow ups list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Follow ups list",$query,$this->success);
	    
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }

    public function store(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'ip_id' => 'required',   
        		'title' => 'required',   
        		'description' => 'required',   
        		'follow_up_type' => 'required|in:1,2',   
        		'start_date' => 'required|date',      
        		'start_time' => 'required',     
	        ],
            [
                'ip_id' =>  getLangByLabelGroups('FollowUp','ip_id'),   
                'title' =>  getLangByLabelGroups('FollowUp','title'),   
                'description' =>  getLangByLabelGroups('FollowUp','description'),   
                'follow_up_type' =>  getLangByLabelGroups('FollowUp','follow_up_type'),   
                'start_date' =>  getLangByLabelGroups('FollowUp','start_date'),      
                'start_time' =>  getLangByLabelGroups('FollowUp','start_time'),  
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
        	if(!$ipCheck) {
              	return prepareResult(false,getLangByLabelGroups('FollowUp','ip_id'),[], $this->not_found); 
        	}
	        $ipFollowups = new IpFollowUp;
		 	$ipFollowups->ip_id = $request->ip_id ;
		 	$ipFollowups->parent_id = ($request->parent_id) ? $request->parent_id :null;
		 	$ipFollowups->top_most_parent_id = $user->top_most_parent_id;
		 	$ipFollowups->title = $request->title;
		 	$ipFollowups->description = $request->description;
		 	$ipFollowups->follow_up_type = $request->follow_up_type;
		 	$ipFollowups->repetition_type = $request->repetition_type;
		 	$ipFollowups->repetition_days = $request->repetition_days;
		 	$ipFollowups->start_date = $request->start_date;
		 	$ipFollowups->start_time = $request->start_time;
		 	$ipFollowups->is_completed = ($request->is_completed) ? 1:0;
		 	$ipFollowups->end_date = $request->end_date;
		 	$ipFollowups->end_time = $request->end_time;
		 	$ipFollowups->remarks = $request->remarks;
		 	$ipFollowups->reason_for_editing = $request->reason_for_editing;
            $ipFollowups->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$ipFollowups->created_by = $user->id;
		 	$ipFollowups->save();
		 	 /*-----------------Personal Informationn ----------------*/
            if(is_array($request->persons) ){
                foreach ($request->persons as $key => $value) {
                    $is_user = false;
                    if($value['is_family_member'] == true){
                        $user_type_id ='8';
                        $is_user = true;
                    }
                    if($value['is_caretaker'] == true){
                        $user_type_id ='7';
                        $is_user = true;
                    }
                    if(($value['is_caretaker'] == true) && ($value['is_family_member'] == true )){
                        $user_type_id ='10';
                        $is_user = true;
                    }
                    if($value['is_contact_person'] == true){
                        $user_type_id ='9';
                        $is_user = true;
                    }
                    $personalInfo = new PersonalInfoDuringIp;
                    $personalInfo->ip_id =$patientPlan->id;
                    $personalInfo->name = $value['name'] ;
                    $personalInfo->email = $value['email'] ;
                    $personalInfo->contact_number = $value['contact_number'];
                    $personalInfo->country = $value['country_id'];
                    $personalInfo->city = $value['city'];
                    $personalInfo->postal_area = $value['postal_area'];
                    $personalInfo->zipcode = $value['zipcode'];
                    $personalInfo->full_address = $value['full_address'] ;
                    $personalInfo->is_family_member = ($value['is_family_member'] == true) ? $value['is_family_member'] : 0 ;
                    $personalInfo->is_caretaker = ($value['is_caretaker'] == true) ? $value['is_caretaker'] : 0 ;
                    $personalInfo->is_contact_person = ($value['is_contact_person'] == true) ? $value['is_contact_person'] : 0 ;
                    $personalInfo->save() ;
                    /*-----Create Account /Entry in user table*/
                    if($is_user == true) {
                        if(auth()->user()->user_type_id=='1'){
                            $top_most_parent_id = auth()->user()->id;
                        }
                        elseif(auth()->user()->user_type_id=='2')
                        {
                            $top_most_parent_id = auth()->user()->id;
                        } else {
                            $top_most_parent_id = auth()->user()->top_most_parent_id;
                        }
                        $checkAlreadyUser = User::where('email',$value['email'])->first();
                        if(empty($checkAlreadyUser)) {
                            $userSave = new User;
                            $userSave->user_type_id = $user_type_id;
                            $userSave->role_id =  $user_type_id;
                            $userSave->parent_id = $user->id;
                            $userSave->top_most_parent_id = $top_most_parent_id;
                            $userSave->name = $value['name'] ;
                            $userSave->email = $value['email'] ;
                            $userSave->password = Hash::make('12345678');
                            $userSave->contact_number = $value['contact_number'];
                            $userSave->country_id = $value['country_id'];
                            $userSave->city = $value['city'];
                            $userSave->postal_area = $value['postal_area'];
                            $userSave->zipcode = $value['zipcode'];
                            $userSave->full_address = $value['full_address'] ;
                            $userSave->save();
                            
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
                               $role = Role::where('id',$user_type_id)->first();
                               $userSave->assignRole($role->name);
                            }
                        }
                    }

                    
                }
            }
		 	
	        return prepareResult(true,getLangByLabelGroups('FollowUp','create') ,$ipFollowups, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[  
        		'ip_id' => 'required',   
        		'title' => 'required',   
        		'description' => 'required',   
        		'follow_up_type' => 'required|in:1,2',   
        		'start_date' => 'required|date',      
        		'start_time' => 'required',     
	        ],
            [  
                'ip_id' =>  getLangByLabelGroups('FollowUp','ip_id'),   
                'title' =>  getLangByLabelGroups('FollowUp','title'),   
                'description' =>  getLangByLabelGroups('FollowUp','description'),   
                'follow_up_type' =>  getLangByLabelGroups('FollowUp','follow_up_type'),   
                'start_date' =>  getLangByLabelGroups('FollowUp','start_date'),      
                'start_time' =>  getLangByLabelGroups('FollowUp','start_time'),  
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
        	if(!$ipCheck) {
              	return prepareResult(false,getLangByLabelGroups('FollowUp','ip_not_found'),[], $this->not_found); 
        	}
        	$checkId = IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],$this->not_found);
            }
            
	        $ipFollowups =  new  IpFollowUp;
		 	$ipFollowups->ip_id = $request->ip_id ;
		 	$ipFollowups->parent_id = ($request->parent_id) ? $request->parent_id :null;
		 	$ipFollowups->top_most_parent_id = $user->top_most_parent_id;
		 	$ipFollowups->title = $request->title;
		 	$ipFollowups->description = $request->description;
		 	$ipFollowups->follow_up_type = $request->follow_up_type;
		 	$ipFollowups->repetition_type = $request->repetition_type;
		 	$ipFollowups->repetition_days = $request->repetition_days;
		 	$ipFollowups->start_date = $request->start_date;
		 	$ipFollowups->start_time = $request->start_time;
		 	$ipFollowups->is_completed = ($request->is_completed) ? 1:0;
		 	$ipFollowups->end_date = $request->end_date;
		 	$ipFollowups->end_time = $request->end_time;
		 	$ipFollowups->remarks = $request->remarks;
		 	$ipFollowups->reason_for_editing = $request->reason_for_editing;
		 	$ipFollowups->edited_by = $user->id;
            $ipFollowups->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$ipFollowups->save();
             /*-----------------Personal Informationn ----------------*/
            if(is_array($request->persons) ){
                foreach ($request->persons as $key => $value) {
                    $is_user = false;
                    if($value['is_family_member'] == true){
                        $user_type_id ='8';
                        $is_user = true;
                    }
                    if($value['is_caretaker'] == true){
                        $user_type_id ='7';
                        $is_user = true;
                    }
                    if(($value['is_caretaker'] == true) && ($value['is_family_member'] == true )){
                        $user_type_id ='10';
                        $is_user = true;
                    }
                    if($value['is_contact_person'] == true){
                        $user_type_id ='9';
                        $is_user = true;
                    }
                    $personalInfo = new PersonalInfoDuringIp;
                    $personalInfo->ip_id =$patientPlan->id;
                    $personalInfo->name = $value['name'] ;
                    $personalInfo->email = $value['email'] ;
                    $personalInfo->contact_number = $value['contact_number'];
                    $personalInfo->country = $value['country_id'];
                    $personalInfo->city = $value['city'];
                    $personalInfo->postal_area = $value['postal_area'];
                    $personalInfo->zipcode = $value['zipcode'];
                    $personalInfo->full_address = $value['full_address'] ;
                    $personalInfo->is_family_member = ($value['is_family_member'] == true) ? $value['is_family_member'] : 0 ;
                    $personalInfo->is_caretaker = ($value['is_caretaker'] == true) ? $value['is_caretaker'] : 0 ;
                    $personalInfo->is_contact_person = ($value['is_contact_person'] == true) ? $value['is_contact_person'] : 0 ;
                    $personalInfo->save() ;
                    /*-----Create Account /Entry in user table*/
                    if($is_user == true) {
                        if(auth()->user()->user_type_id=='1'){
                            $top_most_parent_id = auth()->user()->id;
                        }
                        elseif(auth()->user()->user_type_id=='2')
                        {
                            $top_most_parent_id = auth()->user()->id;
                        } else {
                            $top_most_parent_id = auth()->user()->top_most_parent_id;
                        }
                        $checkAlreadyUser = User::where('email',$value['email'])->first();
                        if(empty($checkAlreadyUser)) {
                            $userSave = new User;
                            $userSave->user_type_id = $user_type_id;
                            $userSave->role_id =  $user_type_id;
                            $userSave->parent_id = $user->id;
                            $userSave->top_most_parent_id = $top_most_parent_id;
                            $userSave->name = $value['name'] ;
                            $userSave->email = $value['email'] ;
                            $userSave->password = Hash::make('12345678');
                            $userSave->contact_number = $value['contact_number'];
                            $userSave->country_id = $value['country_id'];
                            $userSave->city = $value['city'];
                            $userSave->postal_area = $value['postal_area'];
                            $userSave->zipcode = $value['zipcode'];
                            $userSave->full_address = $value['full_address'] ;
                            $userSave->save();
                            
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
                               $role = Role::where('id',$user_type_id)->first();
                               $userSave->assignRole($role->name);
                            }
                        }
                    }

                    
                }
            }
            
		 	
	        return prepareResult(true,getLangByLabelGroups('FollowUp','update') ,$ipFollowups, $this->success);
			  
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],$this->not_found);
            }
        	$IpFollowUp = IpFollowUp::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('FollowUp','delete') ,[], $this->success);
		     	
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],$this->not_found);
            }
        	$ipFollowups = IpFollowUp::where('id',$id)->with('Parent:id,title','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','PatientImplementationPlan')->first();
	        return prepareResult(true,'View Patient plan' ,$ipFollowups, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
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
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$id = $request->id;
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],$this->not_found);
            }
            $ipFollowups = IpFollowUp::find($id);
		 	$ipFollowups->approved_by = $user->id;
		 	$ipFollowups->approved_date = date('Y-m-d');
		 	$ipFollowups->status = '1';
		 	$ipFollowups->save();
	        return prepareResult(true,getLangByLabelGroups('FollowUp','approve') ,$ipFollowups, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
     private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        return($w);

    }
    
}
