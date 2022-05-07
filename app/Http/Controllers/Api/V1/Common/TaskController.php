<?php

namespace App\Http\Controllers\Api\v1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\AssignTask;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:task-browse',['except' => ['show']]);
        $this->middleware('permission:task-add', ['only' => ['store']]);
        $this->middleware('permission:task-edit', ['only' => ['update']]);
        $this->middleware('permission:task-read', ['only' => ['show']]);
        $this->middleware('permission:task-delete', ['only' => ['destroy']]);
        $this->middleware('permission:calendar-browse', ['only' => ['calanderTask']]);
        
    }
    public function tasks(Request $request)
    {
        try {
	        $user = getUser();

            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = Task::select('id','type_id','parent_id','title','description','status','branch_id','id','status', 'updated_at','created_by','start_date','end_date');
            if($user->user_type_id =='2'){
                
                $query = $query->orderBy('id','DESC');
            } else{
                $query =  $query->whereIn('branch_id',$allChilds);
            }

            if($user->user_type_id =='3'){
                $assTask  = AssignTask::where('user_id',$user->id)->pluck('task_id')->implode(',');
                $query = $query->whereIn('id',explode(',',$assTask));

            }
           
	        $whereRaw = $this->getWhereRawFromRequest($request);
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
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->with('assignEmployee.employee:id,name,email,contact_number')->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Task list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->with('assignEmployee.employee:id,name,email,contact_number')->get();
            }
            return prepareResult(true,"Task list",$query,config('httpcodes.success')); 
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function store(Request $request)
    {
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[   
                'title' => 'required',   
                'description' => 'required',   
                'start_date' => 'required',    
                'how_many_time' => 'required',    
                'how_many_time_array' => 'required',    
                "how_many_time_array.*"  => "required|distinct", 
               // "employees"    => "required|array",
                //"employees.*"  => "required|distinct|exists:users,id",   
            ],
            [
            'title.required' =>  getLangByLabelGroups('Activity','title'),
            'description.required' =>  getLangByLabelGroups('Activity','description'),
            'start_date.required' =>  getLangByLabelGroups('FollowUp','start_date'),  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
		   $end_date = $request->end_date;
            $every = '1';

            $dateValidate =  Carbon::parse($request->start_date)->addYears(3)->format('Y-m-d');
            
            if(!empty($request->end_date)){
                $validator = Validator::make($request->all(),[     
                    'end_date' => 'before:' .$dateValidate. '',                         
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }
            }
            if($request->is_repeat){
                $end_date = (empty($request->end_date)) ? Carbon::parse($request->start_date)->addYears(1)->format('Y-m-d') : $request->end_date;
                $every = $request->every;
                if($request->repetition_type !='1'){
                    $validator = Validator::make($request->all(),[     
                        "repeat_dates"    => "required|array",
                        "repeat_dates.*"  => "required|string|distinct",        
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                    }

                }

            } 
            if($request->remind_before_start ){
                $validator = Validator::make($request->all(),[     
                    "before_minutes"    => "required",
                    "before_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            if($request->remind_after_end ){
                $validator = Validator::make($request->all(),[     
                    "after_minutes"    => "required",
                    "after_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            if($request->is_emergency ){
                $validator = Validator::make($request->all(),[     
                    "emergency_minutes"    => "required",
                    "emergency_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            if($request->in_time ){
                $validator = Validator::make($request->all(),[     
                    "in_time_is_text_notify"    => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
		   $repeatedDates = activityDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $group_id = generateRandomNumber();
            $branch_id = getBranchId();
		    $task_ids = [];
		    if(!empty($repeatedDates)) {
                foreach ($repeatedDates as $key => $date) {
                	if(is_array($request->how_many_time_array) ){
                        foreach ($request->how_many_time_array as $key => $time) {
                            if(!empty($time['start']))
                            {
    						    $task = new Task;
    						    $task->type_id = $request->type_id; 
    						    $task->resource_id = $request->resource_id; 
    						    $task->parent_id = $request->parent_id; 
    						    $task->group_id = $group_id;
                                $task->branch_id = $branch_id; 
    						    $task->category_id = $request->category_id;
    						 	$task->subcategory_id = $request->subcategory_id;
    						    $task->title = $request->title; 
    						    $task->description = $request->description; 
    						    $task->start_date = $date; 
    						    $task->start_time = $time['start']; 
    						    $task->address_url = $request->address_url;
    		                    $task->video_url = $request->video_url;
    		                    $task->information_url = $request->information_url;
    		                    $task->information_url = $request->information_url;
    		                    $task->file = $request->file;
    						    $task->how_many_time = $request->how_many_time;
                                $task->is_repeat = ($request->is_repeat) ? 1:0; 
                                $task->every = $request->every; 
                                $task->repetition_type = $request->repetition_type; 
                                $task->how_many_time_array = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                                $task->repeat_dates = ($request->repeat_dates) ? json_encode($request->repeat_dates) :null;
                                $task->end_date = $date;
                                $task->end_time = $time['end'];
    						    $task->remind_before_start = ($request->remind_before_start) ? 1 :0;
    				            $task->before_minutes = $request->before_minutes;
    				            $task->before_is_text_notify = ($request->before_is_text_notify) ? 1 :0;
    				            $task->before_is_push_notify = ($request->before_is_push_notify) ? 1 :0;
    				            $task->remind_after_end  =($request->remind_after_end) ? 1 :0;
    				            $task->after_minutes = $request->after_minutes;
    				            $task->after_is_text_notify = ($request->after_is_text_notify) ? 1 :0;
    				            $task->after_is_push_notify = ($request->after_is_push_notify) ? 1 :0;
    				            $task->is_emergency  =($request->is_emergency) ? 1 :0;
    				            $task->emergency_minutes = $request->emergency_minutes;
    				            $task->emergency_is_text_notify = ($request->emergency_is_text_notify) ? 1 :0;
    				            $task->emergency_is_push_notify = ($request->emergency_is_push_notify) ? 1 :0;
    				            $task->in_time  =($request->in_time) ? 1 :0;
    				            $task->in_time_is_text_notify  =($request->in_time_is_text_notify) ? 1 :0;
    				            $task->in_time_is_push_notify  =($request->in_time_is_push_notify) ? 1 :0;
    						    $task->created_by =$user->id;
                                $task->is_latest_entry = 1;
    						    $task->save();
    						    $task_ids[] = $task->id;
    						    if(is_array($request->employees) && sizeof($request->employees) > 0 ){
                                    	$validator = Validator::make($request->all(),[   
    		                            "employees"    => "required|array",
    		                            "employees.*"  => "required|distinct|exists:users,id",   
    		                        ]);
    		                        if ($validator->fails()) {
    		                            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
    		                        }
    						        foreach ($request->employees as $key => $employee) {
                                        if(!empty($employee))
                                        {
                                
        						            $taskAssign = new AssignTask;
        						            $taskAssign->task_id = $task->id;
        						            $taskAssign->user_id = $employee;
        						            $taskAssign->assignment_date = date('Y-m-d');
        						            $taskAssign->assigned_by = $user->id;
        						            $taskAssign->save();
    						           }
                                    }
    						    }
                            }
						}
					}
				}
			
				$taskList = Task::whereIn('id',$task_ids)->get();
				return prepareResult(true,'Task Added successfully' ,$taskList, config('httpcodes.success'));

			}else{
                 return prepareResult(false,'No date found',[], config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[      
                'title' => 'required',   
                'description' => 'required',   
                'start_date' => 'required',    
                'how_many_time' => 'required',    
                'how_many_time_array' => 'required',    
                "how_many_time_array.*"  => "required|distinct", 
               // "employees"    => "required|array",
                //"employees.*"  => "required|distinct|exists:users,id",   
            ],
            [
            'title.required' =>  getLangByLabelGroups('Activity','title'),
            'description.required' =>  getLangByLabelGroups('Activity','description'),
            'start_date.required' =>  getLangByLabelGroups('FollowUp','start_date'),  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
		   $end_date = $request->end_date;
            $every = '1';

            $dateValidate =  Carbon::parse($request->start_date)->addYears(3)->format('Y-m-d');
            
            if(!empty($request->end_date)){
                $validator = Validator::make($request->all(),[     
                    'end_date' => 'before:' .$dateValidate. '',                         
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }
            }
            if($request->is_repeat){
                $end_date = (empty($request->end_date)) ? Carbon::parse($request->start_date)->addYears(1)->format('Y-m-d') : $request->end_date;
                $every = $request->every;
                if($request->repetition_type !='1'){
                    $validator = Validator::make($request->all(),[     
                        "repeat_dates"    => "required|array",
                        "repeat_dates.*"  => "required|string|distinct",        
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                    }

                }

            } 
            if($request->remind_before_start ){
                $validator = Validator::make($request->all(),[     
                    "before_minutes"    => "required",
                    "before_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            if($request->remind_after_end ){
                $validator = Validator::make($request->all(),[     
                    "after_minutes"    => "required",
                    "after_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            if($request->is_emergency ){
                $validator = Validator::make($request->all(),[     
                    "emergency_minutes"    => "required",
                    "emergency_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            if($request->in_time ){
                $validator = Validator::make($request->all(),[     
                    "in_time_is_text_notify"    => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            $checkId = Task::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
		    $repeatedDates = activityDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $group_id = generateRandomNumber();
            $branch_id = getBranchId();
		    $task_ids = [];
		    $parent_id  = (empty($checkId->parent_id)) ? null : $checkId->parent_id;
		    if(!empty($repeatedDates)) {
                foreach ($repeatedDates as $key => $date) {
                	if(is_array($request->how_many_time_array) ){
                        foreach ($request->how_many_time_array as $key => $time) {
                            if(!empty($time['start']))
                            {
    						    $task = new Task;
    						    $task->type_id = $request->type_id; 
    						    $task->resource_id = $request->resource_id; 
    						    $task->parent_id = $parent_id; 
    						    $task->group_id = $group_id;
                                $task->branch_id = $branch_id; 
    						    $task->category_id = $request->category_id;
    						 	$task->subcategory_id = $request->subcategory_id;
    						    $task->title = $request->title; 
    						    $task->description = $request->description; 
    						    $task->start_date = $date; 
    						    $task->start_time = $time['start']; 
    						    $task->address_url = $request->address_url;
    		                    $task->video_url = $request->video_url;
    		                    $task->information_url = $request->information_url;
    		                    $task->information_url = $request->information_url;
    		                    $task->file = $request->file;
    						    $task->how_many_time = $request->how_many_time;
                                $task->is_repeat = ($request->is_repeat) ? 1:0; 
                                $task->every = $request->every; 
                                $task->repetition_type = $request->repetition_type; 
                                $task->how_many_time_array = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                                $task->repeat_dates = ($request->repeat_dates) ? json_encode($request->repeat_dates) :null;
                                $task->end_date = $date;
                                $task->end_time = $time['end'];
    						    $task->remind_before_start = ($request->remind_before_start) ? 1 :0;
    				            $task->before_minutes = $request->before_minutes;
    				            $task->before_is_text_notify = ($request->before_is_text_notify) ? 1 :0;
    				            $task->before_is_push_notify = ($request->before_is_push_notify) ? 1 :0;
    				            $task->remind_after_end  =($request->remind_after_end) ? 1 :0;
    				            $task->after_minutes = $request->after_minutes;
    				            $task->after_is_text_notify = ($request->after_is_text_notify) ? 1 :0;
    				            $task->after_is_push_notify = ($request->after_is_push_notify) ? 1 :0;
    				            $task->is_emergency  =($request->is_emergency) ? 1 :0;
    				            $task->emergency_minutes = $request->emergency_minutes;
    				            $task->emergency_is_text_notify = ($request->emergency_is_text_notify) ? 1 :0;
    				            $task->emergency_is_push_notify = ($request->emergency_is_push_notify) ? 1 :0;
    				            $task->in_time  =($request->in_time) ? 1 :0;
    				            $task->in_time_is_text_notify  =($request->in_time_is_text_notify) ? 1 :0;
    				            $task->in_time_is_push_notify  =($request->in_time_is_push_notify) ? 1 :0;
    						    $task->created_by =$user->id;
                                $task->is_latest_entry = 1;
    						    $task->save();

                                Task::where('id',$id)->update(['is_latest_entry'=>0]);

    						    $task_ids[] = $task->id;
    						    if(is_array($request->employees)  && sizeof($request->employees) > 0 ){
    						    	$validator = Validator::make($request->all(),[   
    		                            "employees"    => "required|array",
    		                            "employees.*"  => "required|distinct|exists:users,id",   
    		                        ]);
    		                        if ($validator->fails()) {
    		                            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
    		                        }
    						        foreach ($request->employees as $key => $employee) {
                                        if(!empty($employee))
                                        {
        						            $taskAssign = new AssignTask;
        						            $taskAssign->task_id = $task->id;
        						            $taskAssign->user_id = $employee;
        						            $taskAssign->assignment_date = date('Y-m-d');
        						            $taskAssign->assigned_by = $user->id;
        						            $taskAssign->save();
        						        }
                                    }
                                        
    						    }
                            }
						}
					}
				}
			
				$taskList = Task::whereIn('id',$task_ids)->get();
				return prepareResult(true,'Task Update successfully' ,$taskList, config('httpcodes.success'));

			}else{
                 return prepareResult(false,'No date found',[], config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        try {
	    	$user = getUser();
        	$checkId= Task::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$Task = Task::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('FollowUp','delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id)
    {
        try {
	    	$user = getUser();
        	$checkId= Task::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('FollowUp','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$Task = Task::where('id',$id)->with('assignEmployee.employee:id,name,email,contact_number','CategoryType:id,name','Category:id,name','Subcategory:id,name')->first();
	        return prepareResult(true,'View Task' ,$Task, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function calanderTask(Request $request)
    {
        try {
	    	$user = getUser();
	    	$whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  Task::select('id','type_id','parent_id','title','status','branch_id','created_by', DB::raw('DATE(start_date) as date'))->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = Task::select('id','type_id','parent_id','title','status','branch_id','created_by', DB::raw('DATE(start_date) as date'))->orderBy('id', 'DESC');
            }
             $data = [];
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();
                foreach ($result as $key => $value) {
		    		$taskList =  Task::where('start_date',$value->date)->get();
		    		$history =[];
		    		foreach ($taskList as $key => $task) {
		    			$history[] = [
		                    'name' => $task->title,
		                    'status' => $task->status,
	                    ];
		    		}

		    		$data[$value->date]= $history;
		    		
		    	}
                $pagination =  [
                    'data' => $data,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Calander Task",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
	            foreach ($query as $key => $value) {
		    		$taskList =  Task::where('start_date',$value->date)->get();
		    		$history =[];
		    		foreach ($taskList as $key => $task) {
		    			$history[] = [
		                    'name' => $task->title,
		                    'status' => $task->status,
	                    ];
		    		}
		    		$data[$value->date]= $history;
		    		
		    	}
		    	
		    	return prepareResult(true,'Calander Task' ,$data, config('httpcodes.success'));
          	}
        	
	        
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function taskEditHistory(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'parent_id' => 'required|exists:activities,id',   
            ],
            [
            'parent_id' =>  'Parent id is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->parent_id;
            $parent_id = 
            $query= Task::where('parent_id',$id);
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
                return prepareResult(true,"Edited Task list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,'Edited Task list' ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
     public function taskAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'task_id' => 'required|exists:activities,id',   
                'status'     => 'required|in:1,2,3',  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $is_action_perform = false;
           
            $isAssignEmp = AssignTask::where('user_id',$user->id)->where('task_id',$request->task_id)->first();
            if(is_object($isAssignEmp)){
                $is_action_perform = true; 
            }
            $isBranch = Task::where('branch_id',$user->id)->where('id',$request->task_id)->first();
            if(is_object($isBranch)){
                $is_action_perform = true; 
            }
            if($is_action_perform == false){
                return prepareResult(false,'You are not authorized to perform this action',[], config('httpcodes.bad_request')); 
            }
            
            $id = $request->task_id;
            $task = Task::find($id);
            $task->status = $request->status;
            $task->action_by = $user->id;
            $task->action_date = date('Y-m-d');
            $task->comment = $request->comment;
            $task->save();

            $updateStatus = AssignTask::where('task_id',$request->task_id)->update(['status'=> $request->status]);
            
            DB::commit();
               
            return prepareResult(true,'Action Done successfully' ,$task, config('httpcodes.success'));
           
        
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        if (is_null($request->input('type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "type_id = "."'" .$request->input('type_id')."'".")";
        }
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        if (is_null($request->input('created_by')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "created_by = "."'" .$request->input('created_by')."'".")";
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
        if (is_null($request->dateRange) == false) {
        	$now = Carbon::now();
        	if($request->dateRange=='today'){
        		if ($w != '') {$w = $w . " AND ";}
        		$w = $w . "("."start_date >= '".date('y-m-d',strtotime($request->start_date))."')";
        	}
        }
        return($w);

    }
}
