<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityClassification;
use App\Models\Activity;
use App\Models\ActivityAssigne;
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
                $query =  Activity::whereRaw($whereRaw)
                ->with('Parent:id,title','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name','CompanyWorkShift:id,shift_name','ActivityClassification:id,name')
                ->orderBy('id', 'DESC');
            } else {
                $query = Activity::with('Parent:id,title','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name','CompanyWorkShift:id,shift_name','ActivityClassification:id,name')
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
        		'activity_class_id' => 'required|exists:activity_classifications,id',   
        		'category_id' => 'required|exists:category_masters,id',   
        		'title' => 'required',   
        		'description' => 'required',   
        		'start_time' => 'required',     
	        ],
            [
            'activity_class_id.required' => getLangByLabelGroups('Activity','activity_class_id'),
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

        	
	        $activity = new Activity;
		 	$activity->activity_class_id = $request->activity_class_id;
		 	$activity->ip_id = $request->ip_id;
            $activity->branch_id = $request->branch_id;
		 	$activity->patient_id = $request->patient_id;
		 	$activity->emp_id = $request->emp_id;
		 	$activity->shift_id = $request->shift_id;
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
		 	$activity->external_link = $request->external_link;
		 	$activity->activity_status = ($request->activity_status) ? $request->activity_status: 1;
		 	$activity->notity_to_users = $request->notity_to_users;
		 	$activity->created_by = $user->id;
		 	$activity->remind_before_start = ($request->remind_before_start) ? $request->remind_before_start: 0;
		 	$activity->remind_after_end = ($request->remind_after_end) ? $request->remind_after_end: 0;
            $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$activity->save();
            if(!empty($request->emp_id)){
                $activityAssigne = new ActivityAssigne;
                $activityAssigne->activity_id = $activity->id;
                $activityAssigne->user_id = $request->emp_id;
                $activityAssigne->assignment_date = date('Y-m-d');
                $activityAssigne->assignment_day = date('l');
                $activityAssigne->assigned_by = $user->id;
                $activityAssigne->status = '1';
                $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $activityAssigne->save();
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
                'activity_class_id' => 'required|exists:activity_classifications,id',   
                'category_id' => 'required|exists:category_masters,id',   
                'title' => 'required',   
                'description' => 'required',   
                'start_time' => 'required',     
                'reason_for_editing' => 'required',     
            ],
            [
            'activity_class_id.required' => getLangByLabelGroups('Activity','activity_class_id'),
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
        	$parent_id  = (empty($checkId->parent_id)) ? $id : $checkId->parent_id;
        	$activity = new Activity;
	       	$activity->parent_id = $parent_id;
		 	$activity->activity_class_id = $request->activity_class_id;
		 	$activity->ip_id = $request->ip_id;
            $activity->branch_id = $request->branch_id;
		 	$activity->patient_id = $request->patient_id;
		 	$activity->emp_id = $request->emp_id;
		 	$activity->shift_id = $request->shift_id;
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
		 	$activity->external_link = $request->external_link;
		 	$activity->activity_status = ($request->activity_status) ? $request->activity_status: 1;
		 	$activity->done_by = ($request->done_by) ? $request->done_by: null;
		 	$activity->not_done_by = ($request->not_done_by) ? $request->not_done_by: null;
		 	$activity->not_done_reason = ($request->not_done_reason) ? $request->not_done_reason: null;
		 	$activity->not_applicable_reason = ($request->not_applicable_reason) ? $request->not_applicable_reason: null;
		 	$activity->notity_to_users = $request->notity_to_users;
		 	$activity->reason_for_editing = $request->reason_for_editing;
		 	$activity->remind_before_start = ($request->remind_before_start) ? $request->remind_before_start: 0;
		 	$activity->remind_after_end = ($request->remind_after_end) ? $request->remind_after_end: 0;
		 	$activity->edited_by = $user->id;
		 	$activity->edit_date = date('Y-m-d');
            $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$activity->save();
            if(!empty($request->emp_id)){
                $activityAssigne = new ActivityAssigne;
                $activityAssigne->activity_id = $activity->id;
                $activityAssigne->user_id = $request->emp_id;
                $activityAssigne->assignment_date = date('Y-m-d');
                $activityAssigne->assignment_day = date('l');
                $activityAssigne->assigned_by = $user->id;
                $activityAssigne->status = '1';
                $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $activityAssigne->save();
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
		 	$activityAssigne->status = '1';
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
                'status'     => 'required|in:0,1,2,3,4',  
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
            $activity->save();
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
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        return($w);

    }
}
