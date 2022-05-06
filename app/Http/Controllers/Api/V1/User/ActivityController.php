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
use App\Models\ActivityOption;
use App\Models\ActivityTimeLog;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:activity-browse',['except' => ['show']]);
        $this->middleware('permission:activity-add', ['only' => ['store']]);
        $this->middleware('permission:activity-edit', ['only' => ['update']]);
        $this->middleware('permission:activity-read', ['only' => ['show']]);
        $this->middleware('permission:activity-delete', ['only' => ['destroy','activityMultiDelete']]);
        
    }
    
    public function activities(Request $request)
    {
        try {
            $user = getUser();
            $branch_id = (!empty($user->branch_id)) ? $user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $whereRaw = $this->getWhereRawFromRequest($request);
            $query = Activity::with('Category:id,name','Subcategory:id,name','Patient','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email','assignEmployee.employee:id,name,email')->withCount('comments');
            if($user->user_type_id =='2'){

            } else{
                $query =  $query->whereIn('id',$allChilds);
            }

            if($user->user_type_id =='3'){
                $agnActivity  = ActivityAssigne::where('user_id',$user->id)->pluck('activity_id')->implode(',');
                $query = $query->whereIn('id',explode(',',$agnActivity));

            }
            if($user->user_type_id =='6'){
                $query = $query->where('patient_id',$user->id);

            }
            if($whereRaw != '') { 
                $query = $query->whereRaw($whereRaw);
            } else {
                $query = $query->with('Category:id,name','ImplementationPlan.ipFollowUps:id,ip_id,title');
            }

            $query = $query->orderBy('start_date', 'ASC')->orderBy('start_time', 'ASC');
           
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $query = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();
                if(!$user->hasPermissionTo('internalCom-read')){
                    $query = $query->makeHidden('internal_comment');

                } 
                $pagination =  [
                    'data' => $query,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Activity list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
                if(!$user->hasPermissionTo('internalCom-read')){
                    $query = $query->makeHidden('internal_comment');

                } 
            }
            
            return prepareResult(true,"Activity list",$query,config('httpcodes.success'));
           
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[   
                'category_id' => 'required|exists:category_masters,id',   
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
            'category_id.required' => getLangByLabelGroups('Activity','category_id'),
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

        
            $ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
            if(!empty($ipCheck)){
                $ipUpdate  = PatientImplementationPlan::find($ipCheck->id);
                $ipUpdate->start_date  = $request->start_date;
                $ipUpdate->end_date  = $end_date;
                $ipUpdate->how_many_time  = $request->how_many_time;
                $ipUpdate->when_during_the_day  = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                $ipUpdate->save();
            }
        
            $repeatedDates = activityDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $group_id = generateRandomNumber();
            $branch_id = getBranchId();
            $activity_ids = [];
            if(!empty($repeatedDates)) {
                foreach ($repeatedDates as $key => $date) {
                    if(is_array($request->how_many_time_array) && sizeof($request->how_many_time_array) > 0){
                        foreach ($request->how_many_time_array as $key => $time) {
                            if(!empty($time['start']))
                            {
                                $activity = new Activity;
                                $activity->ip_id = $request->ip_id;
                                $activity->group_id = $group_id;
                                $activity->branch_id = $branch_id;
                                $activity->patient_id = $request->patient_id;
                                $activity->category_id = $request->category_id;
                                $activity->subcategory_id = $request->subcategory_id;
                                $activity->title = $request->title;
                                $activity->description = $request->description;
                                $activity->start_date = $date;
                                $activity->start_time = $time['start'];
                                $activity->how_many_time = $request->how_many_time;
                                $activity->is_repeat = ($request->is_repeat) ? 1:0; 
                                $activity->every = $request->every; 
                                $activity->repetition_type = $request->repetition_type; 
                                $activity->how_many_time_array = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                                $activity->repeat_dates = ($request->repeat_dates) ? json_encode($request->repeat_dates) :null;
                                $activity->end_date = $date;
                                $activity->end_time = $time['end'];
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
                                $activity->is_risk  =($request->is_risk) ? 1 :0;
                                $activity->is_compulsory  =($request->is_compulsory) ? 1 :0;
                                $activity->created_by = $user->id;
                                $activity->internal_comment = $request->internal_comment;
                                $activity->external_comment = $request->external_comment;
                                $activity->message = $request->message;
                                $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                                $activity->is_latest_entry = 1;
                                $activity->save();
                                $activity_ids[] = $activity->id;
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
                                            if(env('IS_NOTIFICATION_ENABLE')== true && $request->is_compulsory == true){
                                                $objCom  =[
                                                    "type"=> 'activity',
                                                    "user_id"=> $getUser->top_most_parent_id,
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
                                                pushNotification('activity',$companyObj,$objCom,$module,$id,$screen);
                                            }
                                            if(env('IS_NOTIFICATION_ENABLE')== true &&  ($request->in_time == true ) && ($request->in_time_is_push_notify== true)){
                                                pushNotification('activity',$companyObj,$obj,$module,$id,$screen);
                                            }
                                            if(env('IS_ENABLED_SEND_SMS')== true &&  ($request->in_time== true) && ($request->in_time_is_text_notify== true)){
                                                sendMessage('activity',$obj,$companyObj);
                                            }
                                        }
                                        
                                    }
                                }
                                if(!empty($request->task) ){
                                    addTask($request->task,$activity->id);
                                }
                            }
                        
                       
                        }
                    }
                }
                 DB::commit();
                $activityList = Activity::whereIn('id',$activity_ids)->get();
                return prepareResult(true,'Activity Added successfully' ,$activityList, config('httpcodes.success'));
            } else{
                 return prepareResult(false,'No date found',[], config('httpcodes.bad_request'));
            }
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
                'category_id' => 'required|exists:category_masters,id',   
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
            'category_id.required' => getLangByLabelGroups('Activity','category_id'),
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
        
            $ipCheck = PatientImplementationPlan::where('id',$request->ip_id)->first();
            if(!empty($ipCheck)){
                $ipUpdate  = PatientImplementationPlan::find($ipCheck->id);
                $ipUpdate->start_date  = $request->start_date;
                $ipUpdate->end_date  = $end_date;
                $ipUpdate->how_many_time  = $request->how_many_time;
                $ipUpdate->when_during_the_day  = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                $ipUpdate->save();
            }
            $checkId = Activity::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
        
            $repeatedDates = activityDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $branch_id = getBranchId();
            $activity_ids = [];
            $parent_id  = (empty($checkId->parent_id)) ? $id : $checkId->parent_id;
            if(!empty($repeatedDates)) {
                foreach ($repeatedDates as $key => $date) {
                    if(is_array($request->how_many_time_array)  && sizeof($request->how_many_time_array) > 0 ){
                        foreach ($request->how_many_time_array as $key => $time) {
                            if(!empty($time['start']))
                            {
                                $activity = new Activity;
                                $activity->ip_id = $request->ip_id;
                                $activity->parent_id = $parent_id;
                                $activity->group_id = $checkId->group_id;
                                $activity->branch_id = $branch_id;
                                $activity->patient_id = $request->patient_id;
                                $activity->category_id = $request->category_id;
                                $activity->subcategory_id = $request->subcategory_id;
                                $activity->title = $request->title;
                                $activity->description = $request->description;
                                $activity->start_date = $date;
                                $activity->start_time = $time['start'];
                                $activity->how_many_time = $request->how_many_time;
                                $activity->is_repeat = ($request->is_repeat) ? 1:0; 
                                $activity->every = $request->every; 
                                $activity->repetition_type = $request->repetition_type; 
                                $activity->how_many_time_array = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                                $activity->repeat_dates = ($request->repeat_dates) ? json_encode($request->repeat_dates) :null;
                                $activity->end_date = $date;
                                $activity->end_time = $time['end'];
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
                                $activity->is_risk  =($request->is_risk) ? 1 :0;
                                $activity->is_compulsory  =($request->is_compulsory) ? 1 :0;
                                $activity->created_by = $user->id;
                                $activity->internal_comment = $request->internal_comment;
                                $activity->external_comment = $request->external_comment;
                                $activity->message = $request->message;
                                $activity->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                                $activity->is_latest_entry = 1;
                                $activity->save();

                                Activity::where('id',$id)->update(['is_latest_entry'=>0]);

                                $activity_ids[] = $activity->id;
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
                                           
                                            if(env('IS_NOTIFICATION_ENABLE')== true && $request->is_compulsory == true){
                                                $objCom  =[
                                                    "type"=> 'activity',
                                                    "user_id"=> $getUser->top_most_parent_id,
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
                                                pushNotification('activity',$companyObj,$objCom,$module,$id,$screen);
                                            }
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
                                }
                                if(!empty($request->task) ){
                                    addTask($request->task,$activity->id);
                                }
                            }
                        
                       
                        }
                    }
                }
                 DB::commit();
                $activityList = Activity::whereIn('id',$activity_ids)->get();
                return prepareResult(true,'Activity Update successfully' ,$activityList, config('httpcodes.success'));
            } else{
                 return prepareResult(false,'No date found',[], config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $checkId= Activity::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $activity = Activity::where('id',$id)->delete();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Activity','delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function approvedActivity(Request $request){
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'id' => 'required',   
            ],
            [
            'id' => getLangByLabelGroups('Activity','id'),   
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->id;
            $checkId= Activity::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $activity = Activity::find($id);
            $activity->approved_by = $user->id;
            $activity->approved_date = date('Y-m-d');
            $activity->status = '1';
            $activity->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Activity','approve') ,$activity, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function show($id){
        try {
            $user = getUser();
            $checkId= Activity::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $activity = Activity::where('id',$id)->with('Parent:id,title','Category:id,name','Subcategory:id,name','Patient','assignEmployee.employee:id,name,email','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email')->first();
            return prepareResult(true,'View Activity' ,$activity, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function activityAssignments(Request $request){
        DB::beginTransaction();
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
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
  
            $checkId= Activity::where('id',$request->activity_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $check_already = ActivityAssigne::where('user_id',$request->user_id)->where('activity_id',$request->activity_id)->first();
            if (is_object($check_already)) {
                return prepareResult(false,'This activity is already assigned for this employee', [],config('httpcodes.not_found'));
            }
            $activityAssigne = new ActivityAssigne;
            $activityAssigne->activity_id = $request->activity_id;
            $activityAssigne->user_id = $request->user_id;
            $activityAssigne->assignment_date =  date('Y-m-d');
            $activityAssigne->assignment_day = '1';
            $activityAssigne->assigned_by = $user->id;
            $activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $activityAssigne->save();
            DB::commit();

            $activityAssigne = ActivityAssigne::where('id',$activityAssigne->id)->with('Activity','User:id,name')->first();
            return prepareResult(true,getLangByLabelGroups('Activity','assigne') ,$activityAssigne, config('httpcodes.success'));
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
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
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
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
                return prepareResult(true,"Edited Activity list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,'Activity Ip List' ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function activityAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'activity_id' => 'required|exists:activities,id',   
                'status'     => 'required|in:1,2,3',  
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $is_action_perform = false;
            $isAssignEmp = ActivityAssigne::where('user_id',$user->id)->where('activity_id',$request->activity_id)->first();
            if(is_object($isAssignEmp)){
                $is_action_perform = true; 
            }

            $isBranch = Activity::where('branch_id', $user->id)->where('id',$request->activity_id)->first();
            if(is_object($isBranch)){
                $is_action_perform = true; 
            }

            $isBranch = Activity::where('top_most_parent_id', auth()->id())->where('id',$request->activity_id)->first();
            if(is_object($isBranch)){
                $is_action_perform = true; 
            }
            
            if($is_action_perform == false){
                return prepareResult(false,'You are not authorized to perform this action',[], config('httpcodes.bad_request')); 
            }
            $option  = ActivityOption::where('id',$request->option)->first();
            $is_journal_assign_module = checkAssignModule(3);
            $is_deviation_assign_module = checkAssignModule(4);
            $is_journal = ($option) ? $option->is_journal :'0';
            $is_deviation = ($option) ? $option->is_deviation :'0';
            $is_social = ($request->is_social) ? '1' :'0';

            if($request->status == '1' || $request->status == '2'){
                $validator = Validator::make($request->all(),[   
                  'option' => 'required|exists:activity_options,id',   
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }
    
            }
            if($request->status == '2' || $request->status == '3'){
                $validator = Validator::make($request->all(),[   
                'comment' => 'required',  
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }
    
            }
            
            $id = $request->activity_id;
            $activity = Activity::find($id);
            $activity->status = $request->status;
            $activity->selected_option = ($option) ? $option->option : null;
            $activity->comment = $request->comment;
            $activity->action_by = $user->id;
            $activity->action_date = date('Y-m-d');
            $activity->save();
            if($activity){
                $start_date_time = $activity->start_date.' '.$activity->start_time;
                $start_date = Carbon::parse($start_date_time);
                $current = Carbon::now()->format('Y-m-d H:i:s');
                $now = Carbon::parse($current);
                $interval = $start_date->diffInSeconds($now);
                $time_diff = dates($interval);
                $activityTimeLog = new ActivityTimeLog;
                $activityTimeLog->activity_id = $activity->id;
                $activityTimeLog->start_date = $activity->start_date;
                $activityTimeLog->start_time = $activity->start_time;
                $activityTimeLog->action_date =  Carbon::now()->format('Y-m-d');
                $activityTimeLog->action_time = Carbon::now()->format('H:is');
                $activityTimeLog->action_by = $user->id;
                $activityTimeLog->time_diff = $time_diff;
                $activityTimeLog->save();

                $activityAssigned = ActivityAssigne::where('activity_id',$request->activity_id)->update(['status'=>$request->status,'reason'=>$request->comment]);

                $journal_id = null;
                $deviation_id = null;
                if($is_journal_assign_module == true && $is_journal == '1'){
                   $journal =  journal(null,null,$activity->id,$activity->patient_id,$activity->category_id,$activity->subcategory_id,$activity->title,$activity->description,$is_deviation,$is_social);
                   $journal_id = (!empty($journal)) ? $journal : null;
                }
                if($is_deviation_assign_module == true && $is_deviation == '1'){
                    $deviation = deviation(null,$journal_id,$activity->id,$activity->patient_id,$activity->category_id,$activity->subcategory_id,$activity->title,$activity->description);
                    $deviation_id = (!empty($deviation)) ? $deviation : null;
                }
                 DB::commit();
               
                return prepareResult(true,'Action Done successfully' ,$activity, config('httpcodes.success'));
            } else {
                return prepareResult(false,'Opps! Something Went Wrong',[], config('httpcodes.bad_request')); 
            }
        
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    public function activityMultiDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            Activity::whereIn('id', $request->activity_ids)->update([
                'action_comment' => $request->comment
            ]);

            Activity::whereIn('id', $request->activity_ids)->delete();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Activity','delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function activityTag(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            Activity::where('id', $request->activity_id)->update([
                'activity_tag' => $request->activity_tag
            ]);
            DB::commit();
            return prepareResult(true, 'Activity tag added.' ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function activityNotApplicable(Request $request)
    {
        $validator = Validator::make($request->all(),[     
            "activity_id"   => "required",
            "from_date"     => "required|date|after_or_equal:today",        
            "end_date"      => "required|date|after_or_equal:from_date",        
            "action_comment"=> "required",        
        ]);
        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        }
        try {
            $user = getUser();
            $getActivity = Activity::find($request->activity_id);
            if(!$getActivity)
            {
                return prepareResult(false,'No date found',[], config('httpcodes.bad_request'));
            }
            Activity::where('group_id', $getActivity->group_id)
                ->whereDate('start_date', '>=', $request->from_date)
                ->whereDate('start_date', '<=', $request->end_date)
                ->update([
                'status' => 3, //Not Applicable
                'action_comment' => $request->action_comment,
                'action_by' => auth()->id(),
                'action_date' => date('Y-m-d')
            ]);
            return prepareResult(true, 'Activity Added as not applicable.' ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
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
