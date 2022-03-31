<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityClassification;
use App\Models\Activity;
use App\Models\ActivityAssigne;
use App\Models\PatientImplementationPlan;
use App\Models\Task;
use App\Models\AssignTask;
use App\Models\User;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;
class ActivityController extends Controller
{
    
    public function activities(Request $request)
    {
        try {
	        $user = getUser();
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  Activity::whereRaw($whereRaw)
                ->with('Category:id,name')
                ->orderBy('id', 'DESC');
            } else {
                $query = Activity::orderBy('id', 'DESC')->with('Category:id,name');
            }
           
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $query = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();
                if(!$user->hasPermissionTo('internalCom-read')){
                    $result = $query->makeHidden('internal_comment');

                } 
                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Activity list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
                if(!$user->hasPermissionTo('internalCom-read')){
                    $query = $query->makeHidden('internal_comment');

                } 
            }
            
            return prepareResult(true,"Activity list",$query,$this->success);
	       
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }

    public function store(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[   
        		'category_id' => 'required|exists:category_masters,id',   
        		'title' => 'required',   
        		'description' => 'required',   
        		'start_time' => 'required',    
                "employees"    => "required|array",
                "employees.*"  => "required|distinct", 
	        ],
            [
            'category_id.required' => getLangByLabelGroups('Activity','category_id'),
            'title.required' =>  getLangByLabelGroups('Activity','title'),
            'description.required' =>  getLangByLabelGroups('Activity','description'),
            'start_time.required' =>  getLangByLabelGroups('Activity','start_time'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
            if($request->is_repeat){
                $validator = Validator::make($request->all(),[     
                    'every' => 'required|numeric',          
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

                if($request->repetition_type=='2'){
                    $validator = Validator::make($request->all(),[     
                        "week_days"    => "required|array",
                        "week_days.*"  => "required|string|distinct",        
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                    }

                }
                if($request->repetition_type=='3'){
                    $validator = Validator::make($request->all(),[     
                        "month_day"    => "required",      
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                    }

                }

            } else{
                $validator = Validator::make($request->all(),[   
                    'start_date' => 'required|date',      
                ],
                [ 
                    'start_date.required' =>  getLangByLabelGroups('FollowUp','start_date'),      
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->remind_before_start ){
                $validator = Validator::make($request->all(),[     
                    "before_minutes"    => "required",
                    "before_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->remind_after_end ){
                $validator = Validator::make($request->all(),[     
                    "after_minutes"    => "required",
                    "after_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->is_emergency ){
                $validator = Validator::make($request->all(),[     
                    "emergency_minutes"    => "required",
                    "emergency_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->in_time ){
                $validator = Validator::make($request->all(),[     
                    "in_time_is_text_notify"    => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
        	$ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
            
	        $activity = new Activity;
		 	$activity->ip_id = $request->ip_id;
            $activity->branch_id = $request->branch_id;
		 	$activity->patient_id = ($ipCheck) ? $ipCheck->user_id : null;
		 	$activity->category_id = $request->category_id;
		 	$activity->subcategory_id = $request->subcategory_id;
		 	$activity->title = $request->title;
		 	$activity->description = $request->description;
		 	$activity->start_date = $request->start_date;
            $activity->start_time = $request->start_time;
            $activity->is_repeat = ($request->is_repeat) ? $request->is_repeat : 0;
            $activity->every = $request->every;
            $activity->repetition_type = $request->repetition_type;
            $activity->week_days = ($request->week_days) ? json_encode($request->week_days) :null;
            $activity->month_day = $request->month_day;
		 	$activity->end_date = $request->end_date;
		 	$activity->end_time = $request->end_time;
		 	$activity->address_url = $request->address_url;
            $activity->video_url = $request->video_url;
            $activity->information_url = $request->information_url;
            $activity->information_url = $request->information_url;
            $activity->file = $request->file;
		 	$activity->remind_before_start = ($request->remind_before_start) ? 1 :0;
            $activity->before_minutes = $request->before_minutes;
            $activity->before_is_text_notify = ($request->before_is_text_notify) ? 1 :0;
            $activity->before_is_push_notify = ($request->before_is_push_notify) ? 1 :0;
            $activity->remind_after_end  =($request->remind_after_end) ? 1 :0;
            $activity->after_minutes = $request->after_minutes;
            $activity->after_is_text_notify = ($request->after_is_text_notify) ? 1 :0;
            $activity->after_is_push_notify = ($request->after_is_push_notify) ? 1 :0;
            $activity->is_emergency  =($request->is_emergency) ? 1 :0;
            $activity->emergency_minutes = $request->emergency_minutes;
            $activity->emergency_is_text_notify = ($request->emergency_is_text_notify) ? 1 :0;
            $activity->emergency_is_push_notify = ($request->emergency_is_push_notify) ? 1 :0;
            $activity->in_time  =($request->in_time) ? 1 :0;
            $activity->in_time_is_text_notify  =($request->in_time_is_text_notify) ? 1 :0;
            $activity->in_time_is_push_notify  =($request->in_time_is_push_notify) ? 1 :0;
		 	$activity->created_by = $user->id;
            $activity->internal_comment = $request->internal_comment;
            $activity->external_comment = $request->external_comment;
            $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$activity->save();
            if(is_array($request->employees) ){
                foreach ($request->employees as $key => $employee) {
                    $activityAssigne = new ActivityAssigne;
                    $activityAssigne->activity_id = $activity->id;
                    $activityAssigne->user_id = $employee;
                    $activityAssigne->assignment_date = date('Y-m-d');
                    $activityAssigne->assignment_day ='1';
                    $activityAssigne->assigned_by = $user->id;
                    $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                    $activityAssigne->save();
                    /*-----------Send notification---------------------*/
                    $getUser = User::select('id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$employee)->first();
                    $user_type =  $getUser->user_type_id;
                    $module =  "";
                    $id =  $activityAssigne->id;
                    $screen =  "";
                    $companyObj = companySetting($getUser->top_most_parent_id);
                    $obj  =[
                        "type"=> 'activity',
                        "user_id"=> $getUser->id,
                        "name"=> $getUser->name,
                        "email"=> $getUser->email,
                        "user_type"=> $getUser->user_type_id,
                        "title"=> $activity->title,
                        "patient_id"=> ($activity->Patient)? $activity->Patient->unique_id : null,
                        "start_date"=> $activity->start_date,
                        "start_time"=> $activity->start_time,
                        "company"=>  $companyObj,
                        "company_id"=>  $getUser->top_most_parent_id,

                    ];
                    if(env('IS_NOTIFICATION_ENABLE')== true &&  ($request->in_time == true ) && ($request->in_time_is_push_notify== true)){
                        pushNotification('activity',$companyObj,$obj,$module,$id,$screen);
                    }
                    if(env('IS_ENABLED_SEND_SMS')== true &&  ($request->in_time== true) && ($request->in_time_is_text_notify== true)){
                        sendMessage('activity',$obj,$companyObj);
                    }
                    
                    
                }
            }
            if(!empty($request->task) ){
                addTask($request->task,$activity->id);
            }
            
	        return prepareResult(true,'Activity Added successfully' ,$activity, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
                'category_id' => 'required|exists:category_masters,id',   
                'title' => 'required',   
                'description' => 'required',   
                'start_time' => 'required',     
                'reason_for_editing' => 'required', 
                "employees"    => "required|array",
                "employees.*"  => "required|distinct",     
            ],
            [
            'category_id.required' => getLangByLabelGroups('Activity','category_id'),
            'title.required' =>  getLangByLabelGroups('Activity','title'),
            'description.required' =>  getLangByLabelGroups('Activity','description'),
            'start_time.required' =>  getLangByLabelGroups('Activity','start_time'),
            'reason_for_editing.required' => 'Please Enter Reason for editing',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
        	if($request->is_repeat){
                $validator = Validator::make($request->all(),[     
                    'every' => 'required|numeric',          
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

                if($request->repetition_type=='2'){
                    $validator = Validator::make($request->all(),[     
                        "week_days"    => "required|array",
                        "week_days.*"  => "required|string|distinct",        
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                    }

                }
                if($request->repetition_type=='3'){
                    $validator = Validator::make($request->all(),[     
                        "month_day"    => "required",      
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                    }

                }

            } else{
                $validator = Validator::make($request->all(),[   
                    'start_date' => 'required|date',      
                ],
                [ 
                    'start_date.required' =>  getLangByLabelGroups('FollowUp','start_date'),      
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->remind_before_start ){
                $validator = Validator::make($request->all(),[     
                    "before_minutes"    => "required",
                    "before_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->remind_after_end ){
                $validator = Validator::make($request->all(),[     
                    "after_minutes"    => "required",
                    "after_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->is_emergency ){
                $validator = Validator::make($request->all(),[     
                    "emergency_minutes"    => "required",
                    "emergency_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }
            if($request->in_time ){
                $validator = Validator::make($request->all(),[     
                    "in_time_is_text_notify"    => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
                }

            }

        	$checkId = Activity::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            $ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
        	$parent_id  = (empty($checkId->parent_id)) ? $id : $checkId->parent_id;
        	$activity = new Activity;
	       	$activity->parent_id = $parent_id;
		 	$activity->ip_id = $request->ip_id;
            $activity->branch_id = $request->branch_id;
	        $activity->patient_id = ($ipCheck) ? $ipCheck->user_id : null;
		 	$activity->category_id = $request->category_id;
		 	$activity->subcategory_id = $request->subcategory_id;
		 	$activity->title = $request->title;
		 	$activity->description = $request->description;
		 	$activity->start_date = $request->start_date;
            $activity->start_time = $request->start_time;
            $activity->is_repeat = ($request->is_repeat) ? $request->is_repeat : 0;
            $activity->every = $request->every;
            $activity->repetition_type = $request->repetition_type;
            $activity->week_days = ($request->week_days) ? json_encode($request->week_days) :null;
            $activity->month_day = $request->month_day;
		 	$activity->end_date = $request->end_date;
		 	$activity->end_time = $request->end_time;
		 	$activity->address_url = $request->address_url;
            $activity->video_url = $request->video_url;
            $activity->information_url = $request->information_url;
            $activity->address_url = $request->address_url;
            $activity->file = $request->file;
            $activity->remind_before_start = ($request->remind_before_start) ? 1 :0;
            $activity->before_minutes = $request->before_minutes;
            $activity->before_is_text_notify = ($request->before_is_text_notify) ? 1 :0;
            $activity->before_is_push_notify = ($request->before_is_push_notify) ? 1 :0;
            $activity->remind_after_end  =($request->remind_after_end) ? 1 :0;
            $activity->after_minutes = $request->after_minutes;
            $activity->after_is_text_notify = ($request->after_is_text_notify) ? 1 :0;
            $activity->after_is_push_notify = ($request->after_is_push_notify) ? 1 :0;
            $activity->is_emergency  =($request->is_emergency) ? 1 :0;
            $activity->emergency_minutes = $request->emergency_minutes;
            $activity->emergency_is_text_notify = ($request->emergency_is_text_notify) ? 1 :0;
            $activity->emergency_is_push_notify = ($request->emergency_is_push_notify) ? 1 :0;
            $activity->in_time  =($request->in_time) ? 1 :0;
            $activity->in_time_is_text_notify  =($request->in_time_is_text_notify) ? 1 :0;
            $activity->in_time_is_push_notify  =($request->in_time_is_push_notify) ? 1 :0;
            $activity->created_by = $user->id;
		 	$activity->edited_by = $user->id;
		 	$activity->edit_date = date('Y-m-d');
            $activity->reason_for_editing = $request->reason_for_editing;
            $activity->internal_comment = $request->internal_comment;
            $activity->external_comment = $request->external_comment;
            $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$activity->save();
            if(is_array($request->employees) ){
                foreach ($request->employees as $key => $employee) {
                    $activityAssigne = new ActivityAssigne;
                    $activityAssigne->activity_id = $activity->id;
                    $activityAssigne->user_id = $employee;
                    $activityAssigne->assignment_date = date('Y-m-d');
                    $activityAssigne->assignment_day = '1';
                    $activityAssigne->assigned_by = $user->id;
                    $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                    $activityAssigne->save();
                    /*-----------Send notification---------------------*/
                    $getUser = User::select('id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$employee)->first();
                    $user_type =  $getUser->user_type_id;
                    $module =  "";
                    $id =  $activityAssigne->id;
                    $screen =  "";
                    $companyObj = companySetting($getUser->top_most_parent_id);
                    $obj  =[
                        "type"=> 'activity',
                        "user_id"=> $getUser->id,
                        "name"=> $getUser->name,
                        "email"=> $getUser->email,
                        "user_type"=> $getUser->user_type_id,
                        "title"=> $activity->title,
                        "patient_id"=> ($activity->Patient)? $activity->Patient->unique_id : null,
                        "start_date"=> $activity->start_date,
                        "start_time"=> $activity->start_time,
                        "company"=>  $companyObj,
                        "company_id"=>  $getUser->top_most_parent_id,

                    ];
                    if(env('IS_NOTIFICATION_ENABLE')== true &&  ($request->in_time == true ) && ($request->in_time_is_push_notify== true)){
                        pushNotification('activity',$companyObj,$obj,$module,$id,$screen);
                    }
                    if(env('IS_ENABLED_SEND_SMS')== true &&  ($request->in_time== true) && ($request->in_time_is_text_notify== true)){
                        sendMessage('activity',$obj,$companyObj);
                    }
                }
            }
            if(!empty($request->task) ){
                addTask($request->task,$activity->id);
            }
		 
	        return prepareResult(true,getLangByLabelGroups('Activity','update') ,$activity, $this->success);
			  
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= Activity::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
        	$activity = Activity::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Activity','delete') ,[], $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function approvedActivity(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'id' => 'required',   
	        ],
            [
            'id' => getLangByLabelGroups('Activity','id'),   
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$id = $request->id;
        	$checkId= Activity::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            $activity = Activity::find($id);
		 	$activity->approved_by = $user->id;
		 	$activity->approved_date = date('Y-m-d');
		 	$activity->status = '1';
		 	$activity->save();
	        return prepareResult(true,getLangByLabelGroups('Activity','approve') ,$activity, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= Activity::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
        	$activity = Activity::where('id',$id)->with('Parent:id,title','Category:id,name','Subcategory:id,name','Patient:id,name','assignEmployee.employee:id,name,email','ImplementationPlan')->first();
	        return prepareResult(true,'View Activity' ,$activity, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
     public function activityAssignments(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'activity_id' => 'required|exists:activities,id',   
        		'user_id' => 'required|exists:users,id',    
	        ],
            [
            'activity_id' => getLangByLabelGroups('Activity','activity_id'),   
            'user_id' => getLangByLabelGroups('Activity','user_id'),    
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
  
        	$checkId= Activity::where('id',$request->activity_id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            $check_already = ActivityAssigne::where('user_id',$request->user_id)->where('activity_id',$request->activity_id)->first();
            if (is_object($check_already)) {
                return prepareResult(false,'This activity is already assigned for this employee', [],$this->not_found);
            }
            $activityAssigne = new ActivityAssigne;
		 	$activityAssigne->activity_id = $request->activity_id;
		 	$activityAssigne->user_id = $request->user_id;
		 	$activityAssigne->assignment_date =  date('Y-m-d');
		 	$activityAssigne->assignment_day = '1';
		 	$activityAssigne->assigned_by = $user->id;
            $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$activityAssigne->save();

        	$activityAssigne = ActivityAssigne::where('id',$activityAssigne->id)->with('Activity','User:id,name')->first();
	        return prepareResult(true,getLangByLabelGroups('Activity','assigne') ,$activityAssigne, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

     public function activityEditHistory(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'parent_id' => 'required|exists:activities,id',   
            ],
            [
            'parent_id' =>  'Parent id is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $id = $request->parent_id;
            $parent_id = 
            $query= Activity::where('parent_id',$id);
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
                return prepareResult(true,"Edited Activity list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,'Activity Ip List' ,$query, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function activityAction(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'activity_id' => 'required|exists:activities,id',   
                'status'     => 'required|in:1,2,3',  
                'question' => 'required',  
                'option' => 'required',  
                'comment' => 'required',  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $id = $request->activity_id;
            $activity = Activity::find($id);
            $activity->status = $request->status;
            $activity->question = $request->question;
            $activity->selected_option = $request->option;
            $activity->comment = $request->comment;
            $activity->action_by = $user->id;
            $activity->save();
            $activityAssigned = ActivityAssigne::where('activity_id',$request->activity_id)->update(['status'=>$request->status,'reason'=>$request->comment]);
           
            return prepareResult(true,'Action Done successfully' ,$activity, $this->success);
            
        
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
