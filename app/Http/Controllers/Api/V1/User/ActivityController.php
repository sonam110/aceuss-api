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
use App\Models\Journal;
use App\Models\Deviation;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;
use App\Models\EmailTemplate;

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
		try 
		{
			$user = getUser();
            if(!empty($user->branch_id)) {
                $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }

			$whereRaw = $this->getWhereRawFromRequest($request);
			$query = Activity::select('activities.*')->with('Category:id,name','Subcategory:id,name','Patient','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email','assignEmployee.employee:id,name,email','branch:id,name')->withCount('comments')
			->where('is_latest_entry', 1);

			if($user->user_type_id =='2') {

			}
			elseif($user->user_type_id =='3') {
				$agnActivity  = ActivityAssigne::where('activity_assignes.user_id',$user->id)->pluck('activity_id');
				$query = $query->whereIn('activities.id',$agnActivity);

			}
			else {
				$query =  $query->whereIn('activities.branch_id',$allChilds);
			}

			if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
			{
				$query->where(function ($q) use ($user) {
					$q->where('activities.patient_id', $user->id)
					->orWhere('activities.patient_id', $user->parent_id);
				});
			}

			if(!empty($request->title))
			{
				$query->where('title', 'LIKE', '%'.$request->title.'%');
			}

			if(!empty($request->start_date))
			{
				$query->where('start_date',">=" ,$request->start_date);
			}
			if(!empty($request->end_date))
			{
				$query->where('start_date',"<=" ,$request->end_date);
			}

			if(!empty($request->patient))
			{
				$query->where('patient_id', $request->patient);
			}

			if($whereRaw != '') { 
				$query = $query->whereRaw($whereRaw);
			} else {
				$query = $query->with('Category:id,name','ImplementationPlan.ipFollowUps:id,ip_id,title');
			}

			if(!empty($request->with_journal) && $request->with_journal==1)
			{
				$query->join('journals', function ($join) {
					$join->on('activities.id', '=', 'journals.activity_id');
				})
				->withoutGlobalScope('top_most_parent_id')
				->where('journals.top_most_parent_id', $user->top_most_parent_id);

				$query->where('activities.top_most_parent_id', $user->top_most_parent_id)
				->whereNull('activities.deleted_at');
			}
			if(!empty($request->with_deviation) && $request->with_deviation==1)
			{
				$query->join('deviations', function ($join) {
					$join->on('activities.id', '=', 'deviations.activity_id');
				})
				->withoutGlobalScope('top_most_parent_id')
				->where('deviations.top_most_parent_id', $user->top_most_parent_id);

				$query->where('activities.top_most_parent_id', $user->top_most_parent_id)
				->whereNull('deviations.deleted_at');
			}

			$query = $query->orderBy('activities.start_date', 'ASC')->orderBy('activities.start_time', 'ASC');

            ////////Date and time Passed Counts
			$datePassedActivityCounts = Activity::select([
				\DB::raw('COUNT(id) as total_activities_time_passed'),
			])
			->where('is_latest_entry', 1)
			->where('status', 0)
			->where(\DB::raw("CONCAT(`start_date`, ' ', `start_time`)"), '<=', date('Y-m-d H:i:s'));
			if($user->user_type_id =='2'){

			}
			elseif($user->user_type_id =='3'){
				$agnActivity  = ActivityAssigne::where('activity_assignes.user_id',$user->id)->pluck('activity_id');
				$datePassedActivityCounts = $datePassedActivityCounts->whereIn('activities.id', $agnActivity);

			}
			else{
				$datePassedActivityCounts =  $datePassedActivityCounts->whereIn('activities.branch_id',$allChilds);
			}

			if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
			{
				$datePassedActivityCounts->where(function ($q) use ($user) {
					$q->where('activities.patient_id', $user->id)
					->orWhere('activities.patient_id', $user->parent_id);
				});
			}


			$whereRaw3 = $this->getWhereRawFromRequestTimeExeceed($request);

			if($whereRaw3 != '') { 
				$datePassedActivityCounts = $datePassedActivityCounts->whereRaw($whereRaw3);
			}

			$datePassedActivityCounts = $datePassedActivityCounts->first();

            ////////Counts
			$activityCounts = Activity::select([
				\DB::raw('COUNT(IF(status = 0, 0, NULL)) as total_pending'),
				\DB::raw('COUNT(IF(status = 1, 0, NULL)) as total_done'),
				\DB::raw('COUNT(IF(status = 2, 0, NULL)) as total_not_done'),
				\DB::raw('COUNT(IF(status = 3, 0, NULL)) as total_not_applicable'),
			])->where('is_latest_entry', 1);
			if($user->user_type_id =='2'){

			}
			elseif($user->user_type_id =='3'){
				$agnActivity  = ActivityAssigne::where('activity_assignes.user_id',$user->id)->pluck('activity_id');
				$activityCounts = $activityCounts->whereIn('activities.id', $agnActivity);

			}
			 else{
				$activityCounts =  $activityCounts->whereIn('activities.branch_id',$allChilds);
			}

			

			if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
			{
				$activityCounts->where(function ($q) use ($user) {
					$q->where('activities.patient_id', $user->id)
					->orWhere('activities.patient_id', $user->parent_id);
				});
			}


			$whereRaw2 = $this->getWhereRawFromRequestOther($request);

			if($whereRaw2 != '') { 
				$activityCounts = $activityCounts->whereRaw($whereRaw2);
			}

			$activityCounts = $activityCounts->first();


            ////////Journal & Deviation
			$jour_and_devi = Activity::select('id')->whereDate('start_date', date('Y-m-d'))->where('is_latest_entry', 1);
			if($user->user_type_id =='2'){

			} else{
				$jour_and_devi =  $jour_and_devi->whereIn('id',$allChilds);
			}

			if($user->user_type_id =='3'){
				$agnActivity  = ActivityAssigne::where('user_id',$user->id)->pluck('activity_id');
				$jour_and_devi = $jour_and_devi->whereIn('id', $agnActivity);

			}

			if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
			{
				$jour_and_devi->where(function ($q) use ($user) {
					$q->where('patient_id', $user->id)
					->orWhere('patient_id', $user->parent_id);
				});
			}

			$jour_and_devi = $jour_and_devi->pluck('id');
			$today_created_journal = Journal::whereIn('activity_id', $jour_and_devi)->whereDate('created_at', date('Y-m-d'));

			if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
			{
				$today_created_journal->where(function ($q) use ($user) {
					$q->where('patient_id', $user->id)
					->orWhere('patient_id', $user->parent_id);
				});
			}
			$today_created_journal = $today_created_journal->count();

			$today_created_deviation = Deviation::whereIn('activity_id', $jour_and_devi)->whereDate('created_at', date('Y-m-d'));

			if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
			{
				$today_created_deviation->where(function ($q) use ($user) {
					$q->where('patient_id', $user->id)
					->orWhere('patient_id', $user->parent_id);
				});
			}
			$today_created_deviation = $today_created_deviation->count();

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
					'last_page' => ceil($total / $perPage),

					'total_pending' => $activityCounts->total_pending,
					'total_done' => $activityCounts->total_done,
					'total_not_done' => $activityCounts->total_not_done,
					'total_not_applicable' => $activityCounts->total_not_applicable,
					'today_created_journal' => $today_created_journal,
					'today_created_deviation' => $today_created_deviation,
					'total_activities_time_passed' => $datePassedActivityCounts->total_activities_time_passed
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
				'category_id.required' => getLangByLabelGroups('Activity','message_category_id'),
				'title.required' =>  getLangByLabelGroups('Activity','message_title'),
				'description.required' =>  getLangByLabelGroups('Activity','message_description'),
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

				if(!empty($request->end_date))
				{
					$validator = Validator::make($request->all(),[  
						'start_date' => 'required|date|after_or_equal:'.$ipCheck->start_date,    
                    // 'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:'.$ipCheck->end_date,   
					]);

					if ($validator->fails()) {
						return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
					}
				}
				else
				{
					$validator = Validator::make($request->all(),[  
						'start_date' => 'required|date|after_or_equal:'.$ipCheck->start_date,    
					]);

					if ($validator->fails()) {
						return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
					}	
				}


                /*$ipUpdate  = PatientImplementationPlan::find($ipCheck->id);
                $ipUpdate->start_date  = $request->start_date;
                $ipUpdate->end_date  = $end_date;
                $ipUpdate->how_many_time  = $request->how_many_time;
                $ipUpdate->when_during_the_day  = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                $ipUpdate->save();*/
            }

            $repeatedDates = activityDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $group_id = generateRandomNumber();
            $branch_id = User::select('branch_id')->find($request->patient_id)->branch_id;
            $activity_ids = [];
            if(!empty($repeatedDates)) {
            	foreach ($repeatedDates as $key1 => $date) {
            		if(is_array($request->how_many_time_array) && sizeof($request->how_many_time_array) > 0){
            			foreach ($request->how_many_time_array as $key2 => $time) {
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
            								$getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$employee)->first();
            								$data_id =  $activity->id;
            								$notification_template = EmailTemplate::where('mail_sms_for', 'activity-assignment')->first();

            								if($key1 == 0 && $key2 == 0)
            								{
            									if($request->is_compulsory == true && $key == 0){
            										$user = User::find($getUser->top_most_parent_id);
        											$variable_data = [
        												'{{name}}' => $user->name,
        												'{{assigned_by}}' => Auth::User()->name,
        												'{{activity_title}}' => $activity->title,
        												'{{start_date}}' => $activity->start_date,
        												'{{start_time}}' => $activity->start_time
        											];
        										 	actionNotification($user,$data_id,$notification_template,$variable_data);
            									}
            									if(($request->in_time == true ) && ($request->in_time_is_push_notify== true)){
            										$variable_data = [
        												'{{name}}'=> $getUser->name,
        												'{{assigned_by}}'=> Auth::User()->name,
        												'{{activity_title}}'=> $activity->title,
        												'{{start_date}}'=> $activity->start_date,
        												'{{start_time}}'=> $activity->start_time
        											];
            										actionNotification($getUser,$data_id,$notification_template,$variable_data);
            									}
            								}



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
            								if(env('IS_ENABLED_SEND_SMS')== true &&  ($request->in_time== true) && ($request->in_time_is_text_notify== true)){
            									sendMessage('activity',$obj,$companyObj);
            								}
            							}

            						}
            					}
            					if(auth()->user()->user_type_id==3)
            					{
            						$checkEntry = ActivityAssigne::where('activity_id', $activity->id)
            						->where('user_id', auth()->id())->first();
            						if(!$checkEntry)
            						{
            							$activityAssigne = new ActivityAssigne;
            							$activityAssigne->activity_id = $activity->id;
            							$activityAssigne->user_id = auth()->id();
            							$activityAssigne->assignment_date = date('Y-m-d');
            							$activityAssigne->assignment_day ='1';
            							$activityAssigne->assigned_by = $user->id;
            							$activityAssigne->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            							$activityAssigne->save();
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
            	$activityList = Activity::select('activities.*')->with('Category:id,name','Subcategory:id,name','Patient','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email','assignEmployee.employee:id,name,email','branch:id,name')->withCount('comments')
            	->whereIn('id',$activity_ids)
            	->get();
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
    			'category_id.required' => getLangByLabelGroups('Activity','message_category_id'),
    			'title.required' =>  getLangByLabelGroups('Activity','message_title'),
    			'description.required' =>  getLangByLabelGroups('Activity','message_description'),
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
    			if(!empty($request->end_date))
    			{
    				$validator = Validator::make($request->all(),[  
    					'start_date' => 'required|date|after_or_equal:'.$ipCheck->start_date,    
    					'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:'.$ipCheck->end_date,   
    				]);

    				if ($validator->fails()) {
    					return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
    				}
    			}
    			else
    			{
    				$validator = Validator::make($request->all(),[  
    					'start_date' => 'required|date|after_or_equal:'.$ipCheck->start_date,    
    				]);

    				if ($validator->fails()) {
    					return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
    				}	
    			}
                /*$ipUpdate  = PatientImplementationPlan::find($ipCheck->id);
                $ipUpdate->start_date  = $request->start_date;
                $ipUpdate->end_date  = $end_date;
                $ipUpdate->how_many_time  = $request->how_many_time;
                $ipUpdate->when_during_the_day  = ($request->how_many_time_array) ? json_encode($request->how_many_time_array) :null;
                $ipUpdate->save();*/
            }
            $checkId = Activity::where('id',$id)->first();
            if (!is_object($checkId)) {
            	return prepareResult(false, getLangByLabelGroups('Activity','message_id_not_found'), [],config('httpcodes.not_found'));
            }

            $repeatedDates = activityDateFrame($request->start_date,$end_date,$request->is_repeat,$every,$request->repetition_type,$request->repeat_dates);
            $branch_id = User::select('branch_id')->find($request->patient_id)->branch_id;
            $activity_ids = [];
            $parent_id  = (empty($checkId->parent_id)) ? $id : $checkId->parent_id;
            if(!empty($repeatedDates)) {
            	foreach ($repeatedDates as $key1 => $date) {
            		if(is_array($request->how_many_time_array)  && sizeof($request->how_many_time_array) > 0 ){
            			foreach ($request->how_many_time_array as $key2 => $time) {
            				if(!empty($time['start']))
            				{
            					// Activity::where('id',$id)->update(['is_latest_entry'=>0]);
            					$activity = Activity::find($id);

            					$activityLog = $activity->replicate();

            					// $activity = new Activity;
            					$activity->ip_id = $request->ip_id;
            					$activity->parent_id = $parent_id;
            					$activity->group_id = $checkId->group_id;
            					$activity->branch_id = !empty($request->branch_id) ? $request->branch_id : $branch_id;
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
            								$getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$employee)->first();
            								$user_type =  $getUser->user_type_id;
            								$data_id =  $activity->id;
            								$notification_template = EmailTemplate::where('mail_sms_for', 'activity-assignment')->first();

            								if($key1 == 0 && $key2 == 0)
            								{
            									if($request->is_compulsory == true && $key == 0){
            										$user = User::find($getUser->top_most_parent_id);
        											$variable_data = [
        												'{{name}}' => $user->name,
        												'{{assigned_by}}' => Auth::User()->name,
        												'{{activity_title}}' => $activity->title,
        												'{{start_date}}' => $activity->start_date,
        												'{{start_time}}' => $activity->start_time
        											];
        										 	actionNotification($user,$data_id,$notification_template,$variable_data);
            									}
            									if(($request->in_time == true ) && ($request->in_time_is_push_notify== true)){
            										$variable_data = [
        												'{{name}}'=> $getUser->name,
        												'{{assigned_by}}'=> Auth::User()->name,
        												'{{activity_title}}'=> $activity->title,
        												'{{start_date}}'=> $activity->start_date,
        												'{{start_time}}'=> $activity->start_time
        											];
            										actionNotification($getUser,$data_id,$notification_template,$variable_data);
            									}
            								}
            								
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
            	$activityList = Activity::select('activities.*')->with('Category:id,name','Subcategory:id,name','Patient','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email','assignEmployee.employee:id,name,email','branch:id,name')->withCount('comments')
            	->whereIn('id',$activity_ids)
            	->get();
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
    			return prepareResult(false,getLangByLabelGroups('Activity','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		Task::where('resource_id',$id)->where('type_id','1')->delete();
    		/*-----------notify-user-activity-deleted--------------------*/
    		$users = $checkId->assignEmployee;
    		$data_id =  $checkId->id;
    		$notification_template = EmailTemplate::where('mail_sms_for', 'trashed-acvity-created')->first();
    		foreach ($users as $key => $value) {
    			$variable_data = [
	    		    '{{name}}'              => $value->name,
	    		    '{{title}}'				=> $checkId->title,
	    		    '{{deleted_by}}'        => Auth::User()->name,
	    		];
	    		actionNotification($value,$data_id,$notification_template,$variable_data);
    		}
    		
    		//-----------------------------------------------//
    		// $checkId->tasks->delete();
    		$checkId->delete();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Activity','message_delete') ,[], config('httpcodes.success'));
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
    			'id' => getLangByLabelGroups('Activity','message_id'),   
    		]);
    		if ($validator->fails()) {
    			return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
    		}
    		$id = $request->id;
    		$checkId= Activity::where('id',$id)->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('Activity','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		$activity = Activity::find($id);
    		$activity->approved_by = $user->id;
    		$activity->approved_date = date('Y-m-d');
    		$activity->status = '1';
    		$activity->save();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Activity','message_approve') ,$activity, config('httpcodes.success'));
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
    			return prepareResult(false,getLangByLabelGroups('Activity','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		$activity = Activity::where('id',$id)->with('Parent:id,title','Category:id,name','Subcategory:id,name','Patient.PatientInformation','assignEmployee.employee:id,name,email','ImplementationPlan.ipFollowUps:id,ip_id,title','ActionByUser:id,name,email')->first();
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
    			'activity_id' => getLangByLabelGroups('Activity','message_activity_id'),   
    			'user_id' => getLangByLabelGroups('Activity','message_user_id'),    
    		]);
    		if ($validator->fails()) {
    			return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
    		}

    		$checkId= Activity::where('id',$request->activity_id)->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('Activity','message_id_not_found'), [],config('httpcodes.not_found'));
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

    		/*-----------Send notification---------------------*/

    		$user = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$request->user_id)->first();
    		$data_id =  $checkId->id;
    		$notification_template = EmailTemplate::where('mail_sms_for', 'activity-assignment')->first();
			$variable_data = [
				'{{name}}'  			=> $user->name,
				'{{assigned_by}}' 		=> Auth::User()->name,
				'{{activity_title}}'	=> $checkId->title,
				'{{start_date}}' 		=> $checkId->start_date,
				'{{start_time}}' 		=> $checkId->start_time
			];
    		actionNotification($user,$data_id,$notification_template,$variable_data);
    		DB::commit();
    		$activityAssigne = ActivityAssigne::where('id',$activityAssigne->id)->with('Activity','User:id,name')->first();
    		return prepareResult(true,getLangByLabelGroups('Activity','message_assigne') ,$activityAssigne, config('httpcodes.success'));
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

    		$id = $request->activity_id;
    		$activity = Activity::find($id);

    		$receivers_ids = [];

    		if($activity->assignEmployee->count() > 0)
    		{
    			foreach ($activity->assignEmployee as $key => $value) {
    				$receivers_ids[] = $value->user_id;
    			}
    		}

    		$is_action_perform = false;

    		$isAssignEmp = ActivityAssigne::where('user_id',$user->id)->where('activity_id',$request->activity_id)->first();
    		if(is_object($isAssignEmp)){
    			$is_action_perform = true; 
    			$receivers_ids[] = $activity->top_most_parent_id;
    			$receivers_ids[] = $activity->branch_id;
    		}

    		$isBranch = Activity::where('branch_id', $user->id)->where('id',$request->activity_id)->first();
    		if(is_object($isBranch)){
    			$is_action_perform = true; 
    			$receivers_ids[] = $activity->top_most_parent_id;
    		}

    		$isBranch = Activity::where('top_most_parent_id', auth()->id())->where('id',$request->activity_id)->first();
    		if(is_object($isBranch)){
    			$is_action_perform = true; 
    			$receivers_ids[] = $activity->branch_id;
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
    				$journal =  journal($activity->id);
    				$journal_id = (!empty($journal)) ? $journal : null;
    			}
    			if($is_deviation_assign_module == true && $is_deviation == '1'){
    				$deviation = deviation($activity->id);
    				$deviation_id = (!empty($deviation)) ? $deviation : null;
    			}

    			/*-----------Send notification---------------------*/

    			$receivers_ids = array_filter(array_unique($receivers_ids));
    			$data_id =  $activity->id;
    			

    			if($request->status == 1) {
    				$notification_template = EmailTemplate::where('mail_sms_for', 'activity-done')->first();
    			}
    			elseif ($request->status == 2) {
    				$notification_template = EmailTemplate::where('mail_sms_for', 'activity-not-done')->first();
    			}
    			elseif ($request->status == 3) {
    				$notification_template = EmailTemplate::where('mail_sms_for', 'activity-not-applicable')->first();
    			}
    			$extra_param = ['status'=>$request->status,'start_date'=>$activity->start_date];

    			foreach ($receivers_ids as $key => $value) {
    				$user = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$value)->first();
					$variable_data = [
						'{{name}}'              => $user->name,
						'{{action_by}}'         => Auth::User()->name,
						'{{activity_title}}'    => $activity->title,
						'{{start_date}}'        => $activity->start_date,
						'{{start_time}}'        => $activity->start_time
					];
    				actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param);
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
    		return prepareResult(true,getLangByLabelGroups('Activity','message_delete') ,[], config('httpcodes.success'));
    	}
    	catch(Exception $exception) {
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    public function activityTag(Request $request)
    {
    	DB::beginTransaction();
    	try 
    	{
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

            /*-----------Send notification---------------------*/

            $user = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$getActivity->top_most_parent_id)->first();
            $data_id =  $getActivity->id;
            $notification_template = EmailTemplate::where('mail_sms_for', 'activity-not-applicable')->first();
            $extra_param = ['status'=>3,'start_date'=>$getActivity->start_date];
            $companyObj = companySetting($user->top_most_parent_id);
			$variable_data = [
				'{{name}}'              => $user->name,
				'{{action_by}}'         => Auth::User()->name,
				'{{activity_title}}'    => $getActivity->title,
				'{{start_date}}'        => $getActivity->start_date,
				'{{start_time}}'        => $getActivity->start_time
			];

            actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param);

    		return prepareResult(true, 'Activity Added as not applicable.' ,[], config('httpcodes.success'));
    	}
    	catch(Exception $exception) {
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    private function getWhereRawFromRequest(Request $request)
    {
    	$w = '';
    	if (is_null($request->input('status')) == false) {
    		if ($w != '') {$w = $w . " AND ";}
    		$w = $w . "(" . "activities.status = "."'" .$request->input('status')."'".")";
    	}
    	if (is_null($request->input('ip_id')) == false) {
    		if ($w != '') {$w = $w . " AND ";}
    		$w = $w . "(" . "activities.ip_id = "."'" .$request->input('ip_id')."'".")";
    	}
    	if (is_null($request->input('patient_id')) == false) {
    		if ($w != '') {$w = $w . " AND ";}
    		$w = $w . "(" . "activities.patient_id = "."'" .$request->input('patient_id')."'".")";
    	}
    	if (is_null($request->input('branch_id')) == false) {
    		if ($w != '') {$w = $w . " AND ";}
    		$w = $w . "(" . "activities.branch_id = "."'" .$request->input('branch_id')."'".")";
    	}
    	if (is_null($request->input('category_id')) == false) {
    		if ($w != '') {$w = $w . " AND ";}
    		$w = $w . "(" . "activities.category_id = "."'" .$request->input('category_id')."'".")";
    	}

    	if (is_null($request->start_date) == false || is_null($request->end_date) == false) {

    		if ($w != '') {$w = $w . " AND ";}

    		if ($request->start_date != '')
    		{
    			$w = $w . "("."activities.start_date >= '".date('y-m-d',strtotime($request->start_date))."')";
    		}
    		if (is_null($request->start_date) == false && is_null($request->end_date) == false) 
    		{

    			$w = $w . " AND ";
    		}
    		if ($request->end_date != '')
    		{
    			$w = $w . "("."activities.start_date <= '".date('y-m-d',strtotime($request->end_date))."')";
    		}
    	}
    	if (is_null($request->input('activities.title')) == false) {
    		if ($w != '') {$w = $w . " AND ";}
    		$w = $w . "(" . "title like '%" .trim(strtolower($request->input('title'))) . "%')";


    	}
    	if (is_null($request->input('activities.title')) == false) {
    		if ($w != '') {$w = $w . " OR ";}
    		$w = $w . "(" . "description like '%" .trim(strtolower($request->input('title'))) . "%')";
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

    private function getWhereRawFromRequestTimeExeceed(Request $request)
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
    	return($w);
    }
}
