<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\AssignTask;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;
use App\Models\EmailTemplate;
use App\Models\User;

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
            if(!empty($user->branch_id)) {
                if($user->user_type_id==11)
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                    $allChilds[] = $user->id;
                }
                else
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                }
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
            $query = Task::select('tasks.id','tasks.type_id','tasks.parent_id','tasks.resource_id','tasks.title','tasks.description','tasks.status','tasks.branch_id','tasks.id', 'tasks.updated_at','tasks.created_by','tasks.file','tasks.start_date','tasks.start_time','tasks.end_date','tasks.end_time','tasks.comment','tasks.action_by')
                ->where('is_latest_entry',1)
                ->with('actionBy:id,name','branch:id,name,branch_name','createdBy:id,name');
            if($user->user_type_id =='2'){
                
                // $query = $query->orderBy('id','DESC');
            }
            elseif($user->user_type_id =='3'){
                $assTask  = AssignTask::where('user_id',$user->id)->pluck('task_id');
                $query = $query->whereIn('id', $assTask);
            }
            else
            {
                $query =  $query->whereIn('branch_id',$allChilds);
            }

            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $resource_id = empty($request->resource_id) ? $user->id : $request->resource_id;
                $query->where(function ($q) use ($user, $resource_id) {
                    $q->where('tasks.resource_id', $user->id)
                        ->orWhere('tasks.patient_id', $user->parent_id)
                        ->orWhere('tasks.patient_id', $resource_id);
                })->whereNotNull('tasks.patient_id')
                    ->whereNotNull('tasks.resource_id');
            }

            if(!empty($request->title))
            {
                $title = $request->title;
                $query->where(function ($q) use ($title) {
                    $q->where('tasks.title', 'LIKE', '%'.$title.'%')
                        ->orWhere('tasks.description', 'LIKE', '%'.$title.'%');
                });
            }

            if(!empty($request->status))
            {
                if($request->status==1)
                {
                    $query->where('status', $request->status);
                }
                elseif($request->status=='no')
                {
                    $query->where('status', '0');
                }
            }

           
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  $query->whereRaw($whereRaw);
            }

            $query = $query->orderBy('tasks.start_date', 'ASC')->orderBy('tasks.start_time', 'ASC')->orderBy('tasks.first_create_date', 'ASC');

            if(!empty($request->activity_id))
            {
                $query->where('type_id',1)->where('resource_id', $request->activity_id);
            }
            if(!empty($request->type_id) && $request->type_id!='7')
            {
                $query->where('type_id', $request->type_id);
            }

            if(!empty($request->resource_id))
            {
                if($request->type_id=='7')
                {
                    $resource_id = empty($request->resource_id) ? $user->id : $request->resource_id;
                    $query->where(function ($q) use ($user, $resource_id) {
                        $q->where('tasks.resource_id', $user->id)
                        ->orWhere('tasks.patient_id', $user->parent_id)
                        ->orWhere('tasks.patient_id', $resource_id);
                    })->whereNotNull('tasks.patient_id')
                ->whereNotNull('tasks.resource_id');
                }
                else
                {
                    $query->where('resource_id', $request->resource_id);
                }
            }

            if(!empty($request->emp_id))
            {
                $query->join('assign_tasks', function ($join) {
                    $join->on('assign_tasks.task_id', '=', 'tasks.id');
                })
                ->where('user_id',$request->emp_id);
            }

            $taskCounts = Task::select([
                \DB::raw('COUNT(IF(tasks.status = 1, 0, NULL)) as total_done'),
                \DB::raw('COUNT(IF(tasks.status = 0, 0, NULL)) as total_not_done')
            ])->where('is_latest_entry',1);

            if($user->user_type_id =='2'){

            } elseif($user->user_type_id =='3'){
                $assTask  = AssignTask::where('user_id',$user->id)->pluck('task_id');
                $taskCounts = $taskCounts->whereIn('tasks.id', $assTask);
            } else{
                $taskCounts =  $taskCounts->whereIn('tasks.branch_id',$allChilds);
            }

            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $resource_id = empty($request->resource_id) ? $user->id : $request->resource_id;
                $taskCounts->where(function ($q) use ($user, $resource_id) {
                    $q->where('tasks.resource_id', $user->id)
                        ->orWhere('tasks.patient_id', $user->parent_id)
                        ->orWhere('tasks.patient_id', $resource_id);
                })
                ->whereNotNull('tasks.patient_id')
                ->whereNotNull('tasks.resource_id');
            }

            if(!empty($request->type_id) && $request->type_id!='7')
            {
                $taskCounts->where('type_id', $request->type_id);
            }

            if(!empty($request->resource_id))
            {
                if($request->type_id=='7')
                {
                    $resource_id = empty($request->resource_id) ? $user->id : $request->resource_id;
                    $taskCounts->where(function ($q) use ($user, $resource_id) {
                        $q->where('tasks.resource_id', $user->id)
                        ->orWhere('tasks.patient_id', $user->parent_id)
                        ->orWhere('tasks.patient_id', $resource_id);
                    })->whereNotNull('tasks.patient_id')
                ->whereNotNull('tasks.resource_id');
                }
                else
                {
                    $taskCounts->where('resource_id', $request->resource_id);
                }
            }

            if(!empty($request->emp_id))
            {
                $taskCounts->join('assign_tasks', function ($join) {
                    $join->on('assign_tasks.task_id', '=', 'tasks.id');
                })
                ->where('user_id',$request->emp_id);
            }

            $taskCounts = $taskCounts->first();


            
            
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->with('assignEmployee.employee:id,name,email,contact_number','createdBy:id,name')->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage),
                    'completed_tasks' => $taskCounts->total_done,
                    'not_completed_tasks' => $taskCounts->total_not_done
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->with('assignEmployee.employee:id,name,email,contact_number','createdBy:id,name')->get();
                // $data = [
                //     $query = $query,
                //     'total_completed' => $taskCounts->first()->total_done,
                //     'total_not_completed' => $taskCounts->first()->total_not_done
                // ];
            }
            return prepareResult(true,getLangByLabelGroups('Task','message_list'),$query,config('httpcodes.success')); 
	    }
        catch(Exception $exception) {
	       logException($exception);
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
            'title.required' =>  getLangByLabelGroups('Activity','message_title'),
            'description.required' =>  getLangByLabelGroups('Activity','message_description'),
            'start_date.required' =>  getLangByLabelGroups('FollowUp','message_start_date'),  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
		    $end_date = $request->end_date;
            $every = '1';

            $dateValidate =  Carbon::parse($request->start_date)->addYears(1)->format('Y-m-d');
            
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
                    // "before_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            
		    $repeatedDates = taskDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $group_id = generateRandomNumber();
            $branch_id = getBranchId();
		    $task_ids = [];
		    if(!empty($repeatedDates)) {
                foreach ($repeatedDates as $key => $date) {
                	if(is_array($request->how_many_time_array) ){
                        foreach ($request->how_many_time_array as $key => $time) {
                            if(!empty($time['start']))
                            {
                                $get_patient_id = ($user->user_type_id==6) ? $user->id : null;
                                if($request->type_id=='1')
                                {
                                    $get_info = \DB::table('activities')->find($request->resource_id);
                                    $get_patient_id = @$get_info->patient_id;
                                }
                                elseif($request->type_id=='2')
                                {
                                    $get_info = \DB::table('patient_implementation_plans')->find($request->resource_id);
                                    $get_patient_id = @$get_info->user_id;
                                }
                                elseif($request->type_id=='3' || $request->type_id=='7')
                                {
                                    $get_info = \DB::table('users')->find($request->resource_id);
                                    $get_patient_id = @$get_info->id;
                                }
                                elseif($request->type_id=='4')
                                {
                                    $get_info = \DB::table('deviations')->find($request->resource_id);
                                    $get_patient_id = @$get_info->patient_id;
                                }
                                elseif($request->type_id=='5')
                                {
                                    $get_info = \DB::table('ip_follow_ups')->find($request->resource_id);
                                    $get_patient_id = @$get_info->patient_id;
                                }
                                elseif($request->type_id=='6')
                                {
                                    $get_info = \DB::table('journals')->find($request->resource_id);
                                    $get_patient_id = @$get_info->patient_id;
                                }

    						    $task = new Task;
    						    $task->type_id = $request->type_id; 
    						    $task->resource_id = $request->resource_id; 
                                $task->patient_id = $get_patient_id;
    						    $task->parent_id = $request->parent_id; 
    						    $task->group_id = $group_id;
                                $task->branch_id = $branch_id;
    						    $task->title = $request->title; 
    						    $task->description = $request->description; 
    						    $task->start_date = $date; 
    						    $task->start_time = $time['start']; 
    						    $task->file = $request->file;
    						    $task->how_many_time = $request->how_many_time;
                                $task->is_repeat = ($request->is_repeat) ? 1 : 0; 
                                $task->every = $request->every; 
                                $task->repetition_type = $request->repetition_type; 
                                $task->how_many_time_array = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                                $task->repeat_dates = ($request->repeat_dates) ? json_encode($request->repeat_dates) :null;
                                $task->end_date = empty($request->repetition_type) ? $request->end_date : $date;
                                $task->end_time = $time['end'];
    						    $task->remind_before_start = ($request->remind_before_start) ? 1  : 0;
    				            $task->before_minutes = $request->before_minutes;
    				            $task->before_is_text_notify = ($request->before_is_text_notify) ? 1  : 0;
    				            $task->before_is_push_notify = ($request->before_is_push_notify) ? 1  : 0;
    						    $task->created_by =$user->id;
                                $task->is_latest_entry = 1;
                                $task->first_create_date = date('Y-m-d');
    						    $task->save();
    						    $task_ids[] = $task->id;
    						    if(is_array($request->employees) && sizeof($request->employees) > 0 )
                                {
    						        foreach ($request->employees as $key => $employee) 
                                    {
                                        if(!empty($employee))
                                        {
                                            $taskAssign = new AssignTask;
        						            $taskAssign->task_id = $task->id;
        						            $taskAssign->user_id = $employee;
        						            $taskAssign->assignment_date = date('Y-m-d');
        						            $taskAssign->assigned_by = $user->id;
        						            $taskAssign->save();

                                            /*----notify-emp-task-assigned---*/

                                            $userRec = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->withoutGlobalScope('top_most_parent_id')->where('id',$employee)->first();
                                            $data_id =  $task->id;
                                            $notification_template = EmailTemplate::where('mail_sms_for', 'task-assignment')->first();
                                            if($userRec)
                                            {
                                                $variable_data = [
                                                    '{{name}}' => aceussDecrypt($userRec->name),
                                                    '{{assigned_by}}' => aceussDecrypt(Auth::User()->name),
                                                    '{{task_title}}' => $task->title
                                                ];
                                                actionNotification($userRec,$data_id,$notification_template,$variable_data, null, null, true);
                                            }
    						            }
                                    }
    						    }

                                if(auth()->user()->user_type_id==3)
                                {
                                    $checkEntry = AssignTask::where('task_id', $task->id)
                                        ->where('user_id', auth()->id())->first();
                                    if(!$checkEntry)
                                    {
                                        $taskAssign = new AssignTask;
                                        $taskAssign->task_id = $task->id;
                                        $taskAssign->user_id = auth()->id();
                                        $taskAssign->assignment_date = date('Y-m-d');
                                        $taskAssign->assigned_by = $user->id;
                                        $taskAssign->save();


                                        /*---notify-employee-task-assigned----*/

                                        $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',auth()->id())->first();
                                        $data_id =  $task->id;
                                        $notification_template = EmailTemplate::where('mail_sms_for', 'task-created-assigned')->first();
                                        if($getUser)
                                        {
                                            $variable_data = [
                                                '{{name}}' => aceussDecrypt($getUser->name),
                                                '{{task_title}}' => $task->title,
                                            ];
                                            actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);
                                        }
                                    }
                                }
                            }
						}
					}
				}

                
			
				$taskList = Task::select('id','type_id','parent_id','resource_id','title','description','branch_id','id','status', 'updated_at','created_by','start_date','end_date','comment')
                    ->whereIn('id',$task_ids)->with('assignEmployee.employee:id,name,email,contact_number','createdBy:id,name')->get();
				return prepareResult(true,getLangByLabelGroups('Task','message_create') ,$taskList, config('httpcodes.success'));

			}else{
                 return prepareResult(false,getLangByLabelGroups('Task','message_no_date_found'),[], config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
	        logException($exception);
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
            'title.required' =>  getLangByLabelGroups('Activity','message_title'),
            'description.required' =>  getLangByLabelGroups('Activity','message_description'),
            'start_date.required' =>  getLangByLabelGroups('FollowUp','message_start_date'),  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
		    
            $end_date = $request->end_date;
            $every = '1';

            $dateValidate =  Carbon::parse($request->start_date)->addYears(1)->format('Y-m-d');
            
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

            if($request->remind_before_start )
            {
                $validator = Validator::make($request->all(),[     
                    "before_minutes"    => "required",
                    // "before_is_text_notify"  => "required",        
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            
            $checkId = Task::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Task','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $old_end_date = $checkId->end_date;
            $repeatedDates = taskDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $group_id = $checkId->group_id;
            $branch_id = $checkId->branch_id;
		    $task_ids = [];
		    $parent_id  = (empty($checkId->parent_id)) ? null : $checkId->parent_id;
		    if(!empty($repeatedDates)) {
                foreach ($repeatedDates as $key => $date) {
                	if(is_array($request->how_many_time_array) ){
                        foreach ($request->how_many_time_array as $key => $time) {
                            if(!empty($time['start']))
                            {   
                                $task = new Task;
    						    $task->type_id = $checkId->type_id; 
                                $task->resource_id = $request->resource_id; 
    						    $task->patient_id = $checkId->patient_id; 
    						    $task->parent_id = $parent_id; 
    						    $task->group_id = $group_id;
                                $task->branch_id = $branch_id;
    						    $task->title = $request->title; 
    						    $task->description = $request->description; 
    						    $task->start_date = $date; 
    						    $task->start_time = $time['start']; 
    		                    $task->file = $request->file;
    						    $task->how_many_time = count($request->how_many_time_array);
                                $task->is_repeat = ($request->is_repeat) ? 1 : 0; 
                                $task->every = $request->every; 
                                $task->repetition_type = $request->repetition_type; 
                                $task->how_many_time_array = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                                $task->repeat_dates = ($request->repeat_dates) ? json_encode($request->repeat_dates) :null;
                                $task->end_date = empty($request->repetition_type) ? $request->end_date : $date;
                                $task->end_time = $time['end'];
    						    $task->remind_before_start = ($request->remind_before_start) ? 1  : 0;
    				            $task->before_minutes = $request->before_minutes;
    				            $task->before_is_text_notify = ($request->before_is_text_notify) ? 1  : 0;
    				            $task->before_is_push_notify = ($request->before_is_push_notify) ? 1  : 0;
                                $task->created_by = $user->id;
    				            $task->status = !empty($request->status) ? $request->status : 0;
                                $task->is_latest_entry = 1;
                                $task->first_create_date = $checkId->first_create_date;
    						    $task->save();

                                //update status
                                Task::where('id',$id)->update([
                                    'is_latest_entry' => 0,
                                    'edited_by' => auth()->id(),
                                    'comment'   => $request->reason_for_editing
                                ]);

    						    $task_ids[] = $task->id;
    						    if(is_array($request->employees)  && sizeof($request->employees) > 0 ){
    						    	
                                    //update old entries
                                    if($old_end_date != $request->end_date)
                                    {
                                        $checkId = Task::where('group_id',$checkId->group_id)
                                        ->whereDate('start_date', '>=',  $request->start_date)
                                        ->whereDate('start_date', '<=',  $request->end_date)
                                        ->update([
                                            'is_latest_entry' => 0,
                                            'edited_by' => auth()->id(),
                                            'comment'   => $request->reason_for_editing
                                        ]);
                                    }

    						        foreach ($request->employees as $key => $employee) {
                                        if(!empty($employee))
                                        {
                                            if(AssignTask::where('task_id', $task->id)->where('user_id', $employee)->count()<1)
                                            {
                                                $taskAssign = new AssignTask;
                                                $taskAssign->task_id = $task->id;
                                                $taskAssign->user_id = $employee;
                                                $taskAssign->assignment_date = date('Y-m-d');
                                                $taskAssign->assigned_by = $user->id;
                                                $taskAssign->save();

                                                /*---notify-employee-task-assigned----*/

                                                $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id', $employee)->first();
                                                $data_id =  $task->id;
                                                $notification_template = EmailTemplate::where('mail_sms_for', 'task-created-assigned')->first();
                                                if($getUser)
                                                {
                                                    $variable_data = [
                                                        '{{name}}' => aceussDecrypt($getUser->name),
                                                        '{{task_title}}' => $task->title,
                                                    ];
                                                    actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);
                                                }
                                            }
        						        }
                                    }
                                }
                            }
						}
					}
				}
			
				$taskList = Task::select('id','type_id','parent_id','resource_id','title','description','status','branch_id', 'updated_at','created_by','start_date','end_date','comment')
                    ->whereIn('id',$task_ids)->with('assignEmployee.employee:id,name,email,contact_number','createdBy:id,name')->get();
				return prepareResult(true,getLangByLabelGroups('Task','message_update') ,$taskList, config('httpcodes.success'));

			} else {
                 return prepareResult(false,'No date found',[], config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
	       logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        try {
	    	$user = getUser();
        	$checkId= Task::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Task','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $Task = Task::where('id',$id)->delete();
        	AssignTask::where('task_id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Task','message_delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
	       logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id)
    {
        try {
	    	$user = getUser();
        	$checkId= Task::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Task','message_record_not_found'), [],config('httpcodes.not_found'));
            }
        	$task = Task::where('id',$id)->with('assignEmployee.employee:id,name,email,contact_number','CategoryType:id,name','Category:id,name','Subcategory:id,name','actionBy:id,name','createdBy:id,name')->first();
	        return prepareResult(true,getLangByLabelGroups('Task','message_show') ,$task, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	       logException($exception);
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
		    	
		    	return prepareResult(true,getLangByLabelGroups('Task','message_calender_task') ,$data, config('httpcodes.success'));
          	}
        	
	        
        }
        catch(Exception $exception) {
	       logException($exception);
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
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,getLangByLabelGroups('Task','message_log') ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	       logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
     public function taskAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'task_id' => 'required|exists:tasks,id',   
                'status'     => 'required|in:1,2,3',  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $id = $request->task_id;
            $task = Task::find($id);

            $receivers_ids = [];

            if($task->assignEmployee->count() > 0)
            {
                foreach ($task->assignEmployee as $key => $value) {
                    $receivers_ids[] = $value->user_id;
                }
            }

            $is_action_perform = false;
           
            $isAssignEmp = AssignTask::where('user_id',$user->id)->where('task_id',$request->task_id)->first();
            if(is_object($isAssignEmp)){
                $is_action_perform = true; 
                $receivers_ids[] = $task->top_most_parent_id;
                $receivers_ids[] = $task->branch_id;
            }
            $isBranch = Task::where('branch_id',$user->id)->where('id',$request->task_id)->first();
            if(is_object($isBranch)){
                $is_action_perform = true; 
                $receivers_ids[] = $task->top_most_parent_id;
            }
            if($is_action_perform == false){
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_unauthorized'),[], config('httpcodes.bad_request')); 
            }

            $task->status = $request->status;
            $task->action_by = $user->id;
            $task->action_date = date('Y-m-d H:i:s');
            $task->comment = $request->comment;
            $task->save();

            $updateStatus = AssignTask::where('task_id',$request->task_id)->update(['status'=> $request->status]);

            /*-----------Send notification---------------------*/

            $receivers_ids = array_filter(array_unique($receivers_ids));
            $data_id =  $task->id;

            if($request->status == 1) {
                // $action = "Marked as Done";
                $notification_template = EmailTemplate::where('mail_sms_for', 'task-done')->first();
            }
            else
            {
                // $action = "Marked as Not Done";
                $notification_template = EmailTemplate::where('mail_sms_for', 'task-not-done')->first();
            }

            foreach ($receivers_ids as $key => $value) {
                $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->withoutGlobalScope('top_most_parent_id')->where('id',$value)->first();
                if($getUser)
                {
                    $variable_data = [
                        '{{name}}'      => aceussDecrypt($getUser->name),
                        '{{action_by}}' => aceussDecrypt(Auth::User()->name),
                        // '{{action}}'    => $action,
                        '{{task_title}}'=> $task->title
                    ];
                    actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);
                }
            }
            
            DB::commit();
            $taskList = Task::select('id','type_id','parent_id','title','description','status','branch_id', 'updated_at','created_by','start_date','end_date')
                    ->where('id',$request->task_id)->with('assignEmployee.employee:id,name,email,contact_number','createdBy:id,name')->first();
            return prepareResult(true,getLangByLabelGroups('Task','message_action') ,$taskList, config('httpcodes.success'));
           
        
        }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';        
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
