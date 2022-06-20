<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Schedule;
use App\Models\User;
use App\Models\EmailTemplate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;
use Auth;
use App\Models\ScheduleTemplate;

class LeaveController extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware('permission:Leave-browse',['except' => ['show']]);
    //     $this->middleware('permission:Leave-add', ['only' => ['store']]);
    //     $this->middleware('permission:Leave-edit', ['only' => ['update']]);
    //     $this->middleware('permission:Leave-read', ['only' => ['show']]);
    //     $this->middleware('permission:Leave-delete', ['only' => ['destroy']]);

    // }

	public function Leaves(Request $request)
	{
		try 
		{
			$child_ids = [Auth::id()];
			foreach (Auth::user()->childs as $key => $value) {
				$child_ids[] = $value->id;
			}

			$query = Schedule::orderBy('id', 'DESC')->whereIn('user_id',$child_ids)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->where('leave_applied',1);

			if(!empty($request->emp_id))
			{
				$query->where('user_id', $request->emp_id);
			}
			if($request->leave_group_id == 'yes')
			{
				$query->groupBy('leave_group_id');
			}
			if(!empty($request->leave_approved))
			{
				$query->groupBy('leave_group_id');
			}
			if(!empty($request->start_date))
			{
				$query->where('shift_date',">=" ,$request->start_date);
			}
			if(!empty($request->end_date))
			{
				$query->where('shift_date',"<=" ,$request->end_date);
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
				return prepareResult(true,"Leave list",$pagination,config('httpcodes.success'));
			}
			else
			{
				$query = $query->get();
			}
			return prepareResult(true,"Leave list",$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
    	DB::beginTransaction();
    	try 
    	{
    		$group_id = generateRandomNumber();
    		$schedule_template_id = ScheduleTemplate::where('status','1')->first()->id;
    		if($request->is_repeat == 1)
    		{
    			$validation = \Validator::make($request->all(), [
    				'start_date'    => 'required|date',
    				'end_date'      => 'required|date',
    			]);

    			if ($validation->fails()) {
    				return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    			}
    			$start_date = $request->start_date;
    			$end_date = $request->end_date;
    			$every_week = $request->every_week;
    			$week_days = $request->week_days;

    			if(empty($request->week_days) && (empty($request->every_week) || $request->every_week == 1) )
    			{
    				$date1 = strtotime($start_date);
    				$date2 = strtotime($end_date);
    				for ($curDate=$date1; $curDate<=$date2; $curDate += (86400)) 
    				{                                   
    					$dates[] = date('Y-m-d', $curDate);
    				}
    			}
    			else
    			{
    				$dates = calculateDates($start_date,$end_date,$every_week,$week_days);
    			}

    			foreach ($dates as $key => $date) 
    			{
    				$schedule = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->first();

    				if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
    				{
    					$schedule->update([
    						'leave_applied' => '1',
    						'leave_type' => $request->leave_type,
    						'leave_reason' => $request->reason,
    						'leave_group_id' => $group_id,
    					]);
    				}
    				else
    				{
    					$startEndTime = getStartEndTime(date('00:00:00'), date('23:59:59'), $date);
    					$schedule = new Schedule;
    					$schedule->schedule_template_id = $schedule_template_id;
    					$schedule->shift_id = null;
    					$schedule->user_id = Auth::id();
    					$schedule->parent_id = null;
    					$schedule->shift_start_time = $startEndTime['start_time'];
    					$schedule->shift_end_time = $startEndTime['end_time'];
    					$schedule->patient_id = null;
    					$schedule->group_id = $group_id;
    					$schedule->shift_name = '';
    					$schedule->shift_color = '';
    					$schedule->shift_date = $date;
    					$schedule->leave_applied = 1;
    					$schedule->leave_type = $request->leave_type;
    					$schedule->leave_reason = $request->reason;
    					$schedule->leave_group_id = $group_id;
    					$schedule->leave_approved = 0;
    					$schedule->status = 0;
    					$schedule->schedule_type = 'basic';
    					$schedule->created_by = Auth::id();
    					$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    					$schedule->save();
    				}
    			}        
    		}
    		else
    		{
    			$validation = \Validator::make($request->all(), [
    				'leaves'      => 'required|array',
    			]);

    			if ($validation->fails()) {
    				return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    			}
    			foreach ($request->leaves as $key => $value) 
    			{
    				foreach ($value['dates'] as $key => $date) 
    				{
    					$schedule = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->first();

    					if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
    					{
    						$schedule->update([
    							'leave_applied' => '1',
    							'leave_type' => $request->leave_type,
    							'leave_reason' => $value['reason'],
    							'leave_group_id' => $group_id,
    						]);
    					}
    					else
    					{
    						$startEndTime = getStartEndTime(date('00:00:00'), date('23:59:59'), $date);
    						$schedule = new Schedule;
    						$schedule->schedule_template_id = $schedule_template_id;
    						$schedule->shift_id = null;
    						$schedule->user_id = Auth::id();
    						$schedule->parent_id = null;
    						$schedule->shift_start_time = $startEndTime['start_time'];
    						$schedule->shift_end_time = $startEndTime['end_time'];
    						$schedule->patient_id = null;
    						$schedule->group_id = $group_id;
    						$schedule->shift_name = '';
    						$schedule->shift_color = '';
    						$schedule->shift_date = $date;
    						$schedule->leave_applied = 1;
    						$schedule->leave_type = $request->leave_type;
    						$schedule->leave_reason = $value['reason'];
    						$schedule->leave_group_id = $group_id;
    						$schedule->leave_approved = 0;
    						$schedule->status = 0;
    						$schedule->schedule_type = 'basic';
    						$schedule->created_by = Auth::id();
    						$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    						$schedule->save();
    					}
    				}   
    			}  
    		}

    		$data = Schedule::where('leave_group_id',$group_id)->get();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_create') ,$data, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    	try 
    	{
    		$checkId= Schedule::where('id',$id)->with('user:id,name,gender,user_type_id','user.userType:id,name', 'leaves:id,group_id,date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('Leave','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		return prepareResult(true,'View Leave' ,$checkId, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request,$id)
    {
    	$validation = \Validator::make($request->all(), [
    		'reason'      => 'required',
    	]);

    	if ($validation->fails()) {
    		return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    	} 

    	DB::beginTransaction();
    	try {
    		$leave = Schedule::where('id',$id)->with('user:id,name,gender,user_type_id','user.userType:id,name')->first();
    		$leave->leave_reason = $request->reason;
    		$leave->entry_mode = $request->entry_mode ? $request->entry_mode : 'web';
    		$leave->save();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_create') ,$leave, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */

    public function destroy($id)
    {
    	try 
    	{
    		$checkId= Schedule::find($id);
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('Leave','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		Schedule::where('id',$id)->delete();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_delete') ,[], config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    public function getUserLeaves($id)
    {
    	try {
    		$query = Schedule::where('user_id', $id)->where('leave_applied',1)->get(['shift_date']);
    		$dates = [];
    		foreach ($query as $key => $value) {
    			$dates[] = $value->shift_date;
    		}

    		return prepareResult(true,"Leave list",$dates,config('httpcodes.success'));
    	}
    	catch(Exception $exception) {
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

    	}
    }

    public function leavesApprove(Request $request)
    {
    	DB::beginTransaction();
    	try 
    	{
    		$notified_group = generateRandomNumber();
    		if(!empty($request->group_id))
    		{

    			$leave = Schedule::where('leave_group_id',$request->group_id)->first();
    			$update = Schedule::where('leave_group_id',$request->group_id)
    			->update([
    				'leave_approved' => '1',
    				'leave_approved_by' => Auth::id(), 
    				'leave_approved_date_time' => date('Y-m-d H:i:s')
    			]);
    			$dates = [];
    			$leaves = Schedule::where('leave_group_id',$request->group_id)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,group_id,date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->groupBy('group_id')->get();
    			foreach ($leaves as $key => $value) {
    				$dates[] = $value->date;
    			}
    			$dates = implode(',', $dates);

    			if($request->notify_employees == true)
    			{
    				$users = User::whereIn('employee_type',$request->employee_type)->get();
    				$users_id = [];

    				foreach ($users as $key => $user) 
    				{
                		//---------------------Notification------------------//

    					$title = "";
    					$body = "";
    					$module =  "leave";
    					$event = "leaveApproved";
    					$id =  $request->group_id;
    					$screen =  "list";

    					$getMsg = EmailTemplate::where('mail_sms_for', 'schedule-request')->first();
    					if($getMsg )
    					{
    						$body = $getMsg->notify_body;
    						$title = $getMsg->mail_subject;

    						$arrayVal = [
    							'{{name}}'  		=> $user->name,
    							'{{requested_by}}' 	=> Auth::User()->name,
    							'{{dates}}'			=> $dates
    						];
    						$body = strReplaceAssoc($arrayVal, $body);
    					}
    					actionNotification($event,$user,$title,$body,$module,$screen,$id,'info',1);
    					$users_id[] = $user->id;
    				}

    				Schedule::where('leave_group_id',$request->group_id)
    				->update([
    					'notified_to' => json_encode($users_id),
    					'notified_group' => $notified_group
    				]);
    			}
    		}
    		elseif(!empty($request->leaves))
    		{
    			$leave_ids = [];
    			foreach ($request->leaves as $key => $value) 
    			{
    				$leave_ids[] = $value['schedule_id']; 
    				$leave = Schedule::find($value['schedule_id']);
    				$user = User::find($leave['user_id']);
    				$update = Schedule::find($value['schedule_id'])
    				->update([
    					'leave_approved' => '1',
    					'leave_approved_by' => Auth::id(), 
    					'leave_approved_date_time' => date('Y-m-d H:i:s'), 
    					'status' => 1,
    					'assigned_to' => $user->id
    				]);
    				$startEndTime = getStartEndTime($leave->shift_start_time, $leave->shift_end_time, $leave->shift_date);

    				$schedule = new Schedule;
    				$schedule->top_most_parent_id = $leave->top_most_parent_id;
    				$schedule->user_id = $user->id;
    				$schedule->patient_id = $leave->patient_id;
    				$schedule->shift_id = $leave->shift_id;
    				$schedule->parent_id = $leave->id;
    				$schedule->created_by = Auth::id();
    				$schedule->slot_assigned_to = null;
    				$schedule->employee_assigned_working_hour_id = $leave->assignedWork_id;
    				$schedule->schedule_template_id = $leave->schedule_template_id;
    				$schedule->schedule_type = $leave->schedule_type;
    				$schedule->shift_date = $leave->shift_date;
    				$schedule->group_id = $leave->group_id;
    				$schedule->shift_name = $leave->shift_name;
    				$schedule->shift_color = $leave->shift_color;
    				$schedule->shift_start_time = $startEndTime['start_time'];
    				$schedule->shift_end_time = $startEndTime['end_time'];
    				$schedule->leave_applied = 0;
    				$schedule->leave_group_id = null;
    				$schedule->leave_type = null;
    				$schedule->leave_reason = null;
    				$schedule->leave_approved = 0;
    				$schedule->leave_approved_date_time = null;
    				$schedule->leave_notified_to = null;
    				$schedule->notified_group = null;
    				$schedule->is_active = 1;
    				$schedule->scheduled_work_duration = $leave->scheduled_work_duration;
    				$schedule->extra_work_duration = $leave->extra_work_duration;
    				$schedule->status = $leave->status;
    				$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    				$schedule->save();

    				//---------------notification--------------//

    				if($request->notify_employees == true)
    				{
    					$title = "";
    					$body = "";
    					$module =  "leave";
    					$event = "leaveApproved";
    					$id =  $leave->group_id;
    					$screen =  "detail";

    					$getMsg = EmailTemplate::where('mail_sms_for', 'schedule-request')->first();
    					if($getMsg )
    					{
    						$body = $getMsg->notify_body;
    						$title = $getMsg->mail_subject;

    						$arrayVal = [
    							'{{name}}'  		=> $user->name,
    							'{{requested_by}}' 	=> Auth::User()->name,
    							'{{dates}}'			=> $leave->shift_date
    						];
    						$body = strReplaceAssoc($arrayVal, $body);
    					}
    					actionNotification($event,$user,$title,$body,$module,$screen,$id,'info',1);
    				}
    			}
    			$leaves = Schedule::whereIn('id',$leave_ids)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->groupBy('leave_group_id')->get();
    		}


    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$leaves, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    public function leavesApproveByGroupId($group_id)
    {
    	try 
    	{
    		$update = Schedule::where('group_id',$group_id)
    		->update([
    			'is_approved' => '1',
    			'approved_by' => Auth::id(), 
    			'approved_date' => date('Y-m-d'), 
    			'approved_time' => date('H:i'), 
    			'status' => 1
    		]);
    		$leaves = Schedule::where('group_id', $group_id)
    		->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,group_id,date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')
    		->groupBy('group_id')
    		->first();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$leaves, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    public function leaveScheduleSlotSelected(Request $request)
    {
        // $validation = \Validator::make($request->all(), [
        //          'date'      => 'required',
        //      ]);

     //     if ($validation->fails()) {
     //        return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
     //     }


    	DB::beginTransaction();
    	try 
    	{
    		$leave = Schedule::where('group_id',$request->group_id)->where('date',$request->date)->first();

    		Schedule::where('group_id',$request->group_id)
    		->where('date',$request->date)
    		->update([
    			'assigned_to' => Auth::id(),
    			'assign_status' => 'assigned'
    		]);


    		$old_schedule = Schedule::find($leave->schedule_id);
    		if (!is_object($old_schedule)) {
    			return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), ['schedule not found'],config('httpcodes.not_found'));
    		}

    		$shift_name         = $old_schedule->shift_name;
    		$shift_color        = $old_schedule->shift_color;
    		$parent_id          = $old_schedule->id;

    		$date = $leave->date;

    		$startEndTime = getStartEndTime($old_schedule->shift_start_time, $old_schedule->shift_end_time, $date);

    		$schedule = new Schedule;
    		$schedule->shift_id         = $request->shift_id;
    		$schedule->user_id          = Auth::id();
    		$schedule->parent_id        = $parent_id;
    		$schedule->shift_name       = $shift_name;
    		$schedule->shift_start_time = $startEndTime['start_time'];
    		$schedule->shift_end_time   = $startEndTime['end_time'];
    		$schedule->patient_id       = $old_schedule->patient_id;
    		$schedule->shift_color      = $shift_color;
    		$schedule->shift_date       = $date;
    		$schedule->leave_applied    = $request->leave_applied ? $request->leave_applied :0;
    		$schedule->leave_approved   = $request->leave_approved ? $request->leave_approved :0;
    		$schedule->status           = $request->status ? $request->status :0;
    		$schedule->entry_mode       = $request->entry_mode ? $request->entry_mode :'Web';
    		$schedule->created_by       = Auth::id();
    		$schedule->employee_assigned_working_hour_id = $request->employee_assigned_working_hour_id;
    		$schedule->save();

            //-----------------notification---------------------------//
    		$dates = [];
    		$leaves = Schedule::where('notified_group',$leave->notified_group)->where('assign_status','vacant')->get();
    		foreach ($leaves as $key => $value) {
    			$dates[] = $value->date;
    		}
    		if(!empty($dates))
    		{
    			$dates = implode(',', $dates);

    			$users = User::whereIn('id',json_decode($leave->notified_to))
    			->where('id','!=',Auth::id())
    			->get();

    			$users_id = [];

    			foreach ($users as $key => $user) 
    			{
    				$title = "";
    				$body = "";
    				$module =  "leave";
    				$event = "scheduleSlotSelected";
    				$id =  $leave->group_id;
    				$screen =  "list";

    				$getMsg = EmailTemplate::where('mail_sms_for', 'schedule-slot-selected')->first();
    				if($getMsg )
    				{
    					$body = $getMsg->notify_body;
    					$title = $getMsg->mail_subject;

    					$arrayVal = [
    						'{{name}}'          => $user->name,
    						'{{selected_by}}'   => Auth::User()->name,
    						'{{vacant_dates}}'  => $dates,
    						'{{selected_date}}' => $leave->date
    					];
    					$body = strReplaceAssoc($arrayVal, $body);
    				}
    				actionNotification($event,$user,$title,$body,$module,$screen,$id,'info',1);
    				$users_id[] = $user->id;
    			}

    			Schedule::where('group_id',$request->group_id)
    			->update([
    				'notified_to' => json_encode($users_id),
    				'notified_group' => $leave->notified_group
    			]);
    		}

    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('OVHour','message_create') ,$leave, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }


    public function store(Request $request)
    {
    	DB::beginTransaction();
    	try 
    	{
    		$group_id = generateRandomNumber();
    		$schedule_template_id = ScheduleTemplate::where('status','1')->first()->id;
    		if($request->is_repeat == 1)
    		{
    			$validation = \Validator::make($request->all(), [
    				'start_date'    => 'required|date',
    				'end_date'      => 'required|date',
    			]);

    			if ($validation->fails()) {
    				return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    			}
    			$start_date = $request->start_date;
    			$end_date = $request->end_date;
    			$every_week = $request->every_week;
    			$week_days = $request->week_days;

    			if(empty($request->week_days) && (empty($request->every_week) || $request->every_week == 1) )
    			{
    				$date1 = strtotime($start_date);
    				$date2 = strtotime($end_date);
    				for ($curDate=$date1; $curDate<=$date2; $curDate += (86400)) 
    				{                                   
    					$dates[] = date('Y-m-d', $curDate);
    				}
    			}
    			else
    			{
    				$dates = calculateDates($start_date,$end_date,$every_week,$week_days);
    			}

    			foreach ($dates as $key => $date) 
    			{
    				$schedule = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->first();

    				if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
    				{
    					$schedule->update([
    						'leave_applied' => '1',
    						'leave_type' => $request->leave_type,
    						'leave_reason' => $request->reason,
    						'leave_group_id' => $group_id,
    					]);
    				}
    				else
    				{
    					$startEndTime = getStartEndTime(date('00:00:00'), date('23:59:59'), $date);
    					$schedule = new Schedule;
    					$schedule->schedule_template_id = $schedule_template_id;
    					$schedule->shift_id = null;
    					$schedule->user_id = Auth::id();
    					$schedule->parent_id = null;
    					$schedule->shift_start_time = $startEndTime['start_time'];
    					$schedule->shift_end_time = $startEndTime['end_time'];
    					$schedule->patient_id = null;
    					$schedule->group_id = $group_id;
    					$schedule->shift_name = '';
    					$schedule->shift_color = '';
    					$schedule->shift_date = $date;
    					$schedule->leave_applied = 1;
    					$schedule->leave_type = $request->leave_type;
    					$schedule->leave_reason = $request->reason;
    					$schedule->leave_group_id = $group_id;
    					$schedule->leave_approved = 0;
    					$schedule->status = 0;
    					$schedule->schedule_type = 'basic';
    					$schedule->created_by = Auth::id();
    					$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    					$schedule->save();
    				}
    			}        
    		}
    		else
    		{
    			$validation = \Validator::make($request->all(), [
    				'leaves'      => 'required|array',
    			]);

    			if ($validation->fails()) {
    				return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    			}
    			foreach ($request->leaves as $key => $value) 
    			{
    				foreach ($value['dates'] as $key => $date) 
    				{
    					$schedule = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->first();

    					if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
    					{
    						$schedule->update([
    							'leave_applied' => '1',
    							'leave_type' => $request->leave_type,
    							'leave_reason' => $value['reason'],
    							'leave_group_id' => $group_id,
    						]);
    					}
    					else
    					{
    						$startEndTime = getStartEndTime(date('00:00:00'), date('23:59:59'), $date);
    						$schedule = new Schedule;
    						$schedule->schedule_template_id = $schedule_template_id;
    						$schedule->shift_id = null;
    						$schedule->user_id = Auth::id();
    						$schedule->parent_id = null;
    						$schedule->shift_start_time = $startEndTime['start_time'];
    						$schedule->shift_end_time = $startEndTime['end_time'];
    						$schedule->patient_id = null;
    						$schedule->group_id = $group_id;
    						$schedule->shift_name = '';
    						$schedule->shift_color = '';
    						$schedule->shift_date = $date;
    						$schedule->leave_applied = 1;
    						$schedule->leave_type = $request->leave_type;
    						$schedule->leave_reason = $value['reason'];
    						$schedule->leave_group_id = $group_id;
    						$schedule->leave_approved = 0;
    						$schedule->status = 0;
    						$schedule->schedule_type = 'basic';
    						$schedule->created_by = Auth::id();
    						$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    						$schedule->save();
    					}
    				}   
    			}  
    		}

    		$data = Schedule::where('leave_group_id',$group_id)->get();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_create') ,$data, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }
}
