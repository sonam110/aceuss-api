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
use Validator;
use Auth;
use Exception;
use DB;
class ActivityController extends Controller
{
    
    public function activities(Request $request)
    {
        try {
	        $user = getUser();
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  Activity::select('id','ip_id','title','status','branch_id')->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = Activity::select('id','ip_id','title','status','branch_id')->orderBy('id', 'DESC');
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
                return prepareResult(true,"Activity list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
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
                "employees.*"  => "required|string|distinct", 
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
             if($request->remind_before_start || $request->remind_after_end || $request->is_emergency || $request->in_time ){
                    $validator = Validator::make($request->all(),[     
                        "minutes"    => "required",
                        "is_text_notify"  => "required",        
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
            $activity->remind_after_end  =($request->remind_after_end) ? 1 :0;
            $activity->is_emergency  =($request->is_emergency) ? 1 :0;
            $activity->in_time  =($request->in_time) ? 1 :0;
            $activity->minutes = $request->minutes;
            $activity->is_text_notify  =($request->is_emergency) ? 1 :0;
            $activity->is_push_notify  =($request->is_text_notify) ? 1 :0;
		 	$activity->created_by = $user->id;
            $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$activity->save();
            if(is_array($request->employees) ){
                foreach ($request->employees as $key => $employee) {
                    $activityAssigne = new ActivityAssigne;
                    $activityAssigne->activity_id = $activity->id;
                    $activityAssigne->user_id = $employee;
                    $activityAssigne->assignment_date = date('Y-m-d');
                    $activityAssigne->assignment_day = date('l');
                    $activityAssigne->assigned_by = $user->id;
                    $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                    $activityAssigne->save();
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
                "employees.*"  => "required|string|distinct",     
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
            $activity->remind_after_end  =($request->remind_after_end) ? 1 :0;
            $activity->is_emergency  =($request->is_emergency) ? 1 :0;
            $activity->in_time  =($request->in_time) ? 1 :0;
            $activity->minutes = $request->minutes;
            $activity->is_text_notify  =($request->is_emergency) ? 1 :0;
            $activity->is_push_notify  =($request->is_text_notify) ? 1 :0;
            $activity->created_by = $user->id;
		 	$activity->edited_by = $user->id;
		 	$activity->edit_date = date('Y-m-d');
            $activity->reason_for_editing = $request->reason_for_editing;
            $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$activity->save();
            if(is_array($request->employees) ){
                foreach ($request->employees as $key => $employee) {
                    $activityAssigne = new ActivityAssigne;
                    $activityAssigne->activity_id = $activity->id;
                    $activityAssigne->user_id = $employee;
                    $activityAssigne->assignment_date = date('Y-m-d');
                    $activityAssigne->assignment_day = date('l');
                    $activityAssigne->assigned_by = $user->id;
                    $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                    $activityAssigne->save();
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
        	$activity = Activity::where('id',$id)->with('Parent:id,title','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name','CompanyWorkShift:id,shift_name','ActivityClassification:id,name','children')->first();
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
        		'activity_id' => 'required',   
        		'user_id' => 'required',   
        		'assignment_date' => 'required|date',   
        		'assignment_day' => 'required',   
	        ],
            [
            'activity_id' => getLangByLabelGroups('Activity','activity_id'),   
            'user_id' => getLangByLabelGroups('Activity','user_id'),   
            'assignment_date' => getLangByLabelGroups('Activity','assignment_date'),   
            'assignment_day' => getLangByLabelGroups('Activity','assignment_day'),   
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
  
        	$checkId= Activity::where('id',$request->activity_id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            $activityAssigne = new ActivityAssigne;
		 	$activityAssigne->activity_id = $request->activity_id;
		 	$activityAssigne->user_id = $request->user_id;
		 	$activityAssigne->assignment_date = $request->assignment_date;
		 	$activityAssigne->assignment_day = $request->assignment_day;
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
        return($w);

    }
}
