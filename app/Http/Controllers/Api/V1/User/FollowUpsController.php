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
use App\Models\Task;
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
            if(!empty($user->branch_id)) {
                $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
            
	        $whereRaw = $this->getWhereRawFromRequest($request);
            $query = IpFollowUp::with('ActionByUser:id,name,email','PatientImplementationPlan.patient')
                ->where('is_latest_entry', 1);
            if($user->user_type_id =='2'){
                
            } else{
                $query =  $query->whereIn('branch_id',$allChilds);
            }
            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $query->where(function ($q) use ($user) {
                    $q->where('patient_id', $user->id)
                    ->orWhere('patient_id', $user->parent_id);
                });
            }
            if($whereRaw != '') { 
                $query =  $query->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = $query->orderBy('id', 'DESC');
            }

            if($request->is_completed == "1")
            {
                $query = $query->where('is_completed', 1);
            }

            if($request->is_completed == "0")
            {
                $query = $query->where('is_completed', 0);
            }

            if(!empty($request->title))
            {
                $query->where(function ($query) use ($request) {
                     $query->where('title', 'LIKE', '%'.$request->title.'%')->orWhere('description', 'LIKE', '%'.$request->title.'%');
                });
            }

            if(!empty($request->start_date))
            {
                $query->where('start_date',">=" ,$request->start_date);
            }
            if(!empty($request->end_date))
            {
                $query->where('start_date',"<=" ,$request->end_date);
            }
            
            $followUpCounts = IpFollowUp::select([
                \DB::raw('COUNT(IF(is_completed = 1, 0, NULL)) as completed'),
                \DB::raw('COUNT(IF(is_completed = 0, 0, NULL)) as not_completed')
            ])->where('is_latest_entry',1);

            if($user->user_type_id =='2'){

            } else{
                $followUpCounts =  $followUpCounts->whereIn('ip_follow_ups.branch_id',$allChilds);
            }
            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $followUpCounts->where(function ($q) use ($user) {
                    $q->where('ip_follow_ups.patient_id', $user->id)
                    ->orWhere('ip_follow_ups.patient_id', $user->parent_id);
                });
            }

            $whereRaw2 = $this->getWhereRawFromRequestOther($request);

            if($whereRaw2 != '') { 
                $followUpCounts = $followUpCounts->whereRaw($whereRaw2);
            }

            $followUpCounts = $followUpCounts->first();

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
                    'last_page' => ceil($total / $perPage),
                    'completed_follow_ups' => $followUpCounts->completed,
                    'not_completed_follow_ups' => $followUpCounts->not_completed
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,getLangByLabelGroups('FollowUp','message_list'),$query,config('httpcodes.success'));
	    }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function store(Request $request)
    {
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
                //'ip_id.required' =>  getLangByLabelGroups('FollowUp','message_ip_id'),   
                'title.required' =>  getLangByLabelGroups('FollowUp','message_title'),   
                // 'description.required' =>  getLangByLabelGroups('FollowUp','message_description'),           
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        	$ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
        	if(!$ipCheck) {
              	return prepareResult(false,getLangByLabelGroups('FollowUp','message_ip_id'),[], config('httpcodes.not_found')); 
        	}
            if(is_array($request->repeat_datetime)   && sizeof($request->repeat_datetime) > 0){
                $ipfollowupsId = [];
                foreach ($request->repeat_datetime as $key => $followup) {
                    if(!empty($followup['start_date']))
                    {
            	        $ipFollowups = new IpFollowUp;
            		 	$ipFollowups->ip_id = $request->ip_id;
                        $ipFollowups->patient_id = $ipCheck->user_id;
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

                        /*--notify-user-followup-created--*/
                        $notifyUser = User::find($ipCheck->user_id);
                        $data_id =  $ipFollowups->id;
                        $notification_template = EmailTemplate::where('mail_sms_for', 'followup-created')->first();
                        $variable_data = [
                            '{{name}}' => $notifyUser->name,
                            '{{created_by}}' => Auth::User()->name,
                            '{{title}}' => $ipFollowups->title,
                            '{{start_date}}' => $ipFollowups->start_date,
                            '{{start_time}}' => $ipFollowups->start_time,
                            '{{end_date}}' => $ipFollowups->end_date,
                            '{{end_time}}' => $ipFollowups->end_time
                        ];
                        actionNotification($notifyUser,$data_id,$notification_template,$variable_data);
                        //------------------------------//
            		 	

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
	        return prepareResult(true,getLangByLabelGroups('FollowUp','message_create') ,$data, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
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
                'ip_id.required' =>  getLangByLabelGroups('FollowUp','message_ip_id'),   
                'title.required' =>  getLangByLabelGroups('FollowUp','message_title'),   
                'description.required' =>  getLangByLabelGroups('FollowUp','message_description'),           
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
            if(!$ipCheck) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','message_ip_id'),[], config('httpcodes.not_found')); 
            }
        	
        	$checkId = IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            if(is_array($request->repeat_datetime)  && sizeof($request->repeat_datetime) > 0 ){
                $ipfollowupsId = [];
                foreach ($request->repeat_datetime as $key => $followup) {
                    if(!empty($followup['start_date']))
                    {
                        $parent_id  = (empty($checkId->parent_id)) ? $id : $checkId->parent_id;
                        $ipFollowups = IpFollowUp::find($parent_id);
                        if($ipFollowups)
                        {
                            //new entry create for log
                            $ipFollowupsCopy = $ipFollowups->replicate();
                            $ipFollowupsCopy->parent_id = $ipFollowups->id;
                            $ipFollowupsCopy->is_latest_entry = 0;
                            $ipFollowupsCopy->created_at = $ipFollowups->created_at;
                            $ipFollowupsCopy->save();

                            //update Existing or current record
                            $ipFollowups->ip_id = $request->ip_id ;
                            $ipFollowups->branch_id = getBranchId() ;
                            $ipFollowups->parent_id = null;
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
                            $ipFollowups->created_at = Carbon::now();
                            $ipFollowups->save();
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
	        return prepareResult(true,getLangByLabelGroups('FollowUp','message_update') ,$data, config('httpcodes.success'));
			  
        }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            Task::where('resource_id',$id)->where('type_id','5')->delete();
            $IpFollowUp = IpFollowUp::where('id',$id)->delete();
        	$personalInfoDuringIp = PersonalInfoDuringIp::where('follow_up_id', $id)->delete();
         	return prepareResult(true,getLangByLabelGroups('FollowUp','message_delete') ,[], config('httpcodes.success'));
		     	
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','message_record_not_found'), [],config('httpcodes.not_found'));
            }
        	$ipFollowups = IpFollowUp::where('id',$id)->with('persons.Country','questions','PatientImplementationPlan.patient','ActionByUser:id,name,email')->first();
	        return prepareResult(true,getLangByLabelGroups('FollowUp','message_show') ,$ipFollowups, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
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
            'id' =>  getLangByLabelGroups('FollowUp','message_id'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$id = $request->id;
        	$checkId= IpFollowUp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $ipFollowups = IpFollowUp::find($id);
		 	$ipFollowups->approved_by = $user->id;
		 	$ipFollowups->approved_date = date('Y-m-d');
		 	$ipFollowups->status = '1';
		 	$ipFollowups->save();
	        return prepareResult(true,getLangByLabelGroups('FollowUp','message_approve') ,$ipFollowups, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
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
            'id' =>  getLangByLabelGroups('FollowUp','message_id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->follow_up_id;
            $checkId = IpFollowUp::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','message_record_not_found'), [],config('httpcodes.not_found'));
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

            //withness added
            foreach ($request->witness as $key => $value) 
            {
                $followupWitness = new PersonalInfoDuringIp;
                $followupWitness->ip_id = $checkId->ip_id;
                $followupWitness->user_id = $value;
                $followupWitness->follow_up_id = $id;
                $followupWitness->save();
            }

            if(is_array($request->question_answer))
            {
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
            return prepareResult(true,getLangByLabelGroups('FollowUp','message_complete') ,$ipFollowups, config('httpcodes.success'));
            
        
        }
        catch(Exception $exception) {
	        logException($exception);
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
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,getLangByLabelGroups('FollowUp','message_log') ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
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

        return($w);

    }

    private function getWhereRawFromRequestOther(Request $request)
    {
        $w = '';
        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "ip_id = "."'" .$request->input('ip_id')."'".")";
        }
        if (is_null($request->input('patient_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_id = "."'" .$request->input('patient_id')."'".")";
        }
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        if (is_null($request->input('category_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "category_id = "."'" .$request->input('category_id')."'".")";
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
        return($w);

    }
    
}
