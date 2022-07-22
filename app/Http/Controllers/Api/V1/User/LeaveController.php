<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;
use Auth;
use App\Models\ScheduleTemplate;
use App\Models\CompanyWorkShift;

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

			$query = Schedule::orderBy('id', 'DESC')->whereIn('user_id',$child_ids)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->where('leave_applied',1);

			if(!empty($request->emp_id))
			{
				$query->where('user_id', $request->emp_id);
			}
			if($request->leave_group_id == 'yes')
			{
				$query->groupBy('leave_group_id');
			}
			if($request->leave_approved == 'yes')
			{
				$query->where('leave_approved', 1);
			}
            if($request->leave_approved == 'no')
            {
                $query->where('leave_approved', 0);
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

    public function store(Request $request)
    {
    	DB::beginTransaction();
    	try 
    	{
    		$group_id = generateRandomNumber();
    		if(ScheduleTemplate::where('status','1')->count()<=0)
    		{
    			return prepareResult(false,getLangByLabelGroups('Leave','message_template_not_found'), ['Add Schedule Template First'],config('httpcodes.not_found'));
    		}
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
    				$schedules = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->get();

    				if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
    				{
                        foreach ($schedules as $key => $schedule) {
                            $vacation_duration = 0;
                            if($request->leave_type=='vacation')
                            {
                                $vacation_duration = $schedule->scheduled_work_duration + $schedule->extra_work_duration + $schedule->ob_work_duration + $schedule->emergency_work_duration;
                            }
                            $schedule->update([
                                'leave_applied' => '1',
                                'leave_type' => $request->leave_type,
                                'leave_reason' => $request->reason,
                                'leave_group_id' => $group_id,
                                'vacation_duration' => $vacation_duration,
                            ]);
                        }
    				}
    				else
    				{
                        $shift_start_time = $date.' 00:00:00';
                        $shift_end_time = $date.' 23:59:59';

                        // $result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,'basic');
                        // $vacation_duration = 0;
                        // if($request->leave_type=='vacation')
                        // {
                        //     $vacation_duration = $result['scheduled_work_duration'] + $result['extra_work_duration'] + $result['emergency_work_duration'] + $result['ob_work_duration'];
                        // }
    					$schedule = new Schedule;
    					$schedule->schedule_template_id = $schedule_template_id;
    					$schedule->shift_id = null;
    					$schedule->user_id = Auth::id();
    					$schedule->parent_id = null;
    					$schedule->shift_start_time = $shift_start_time;
    					$schedule->shift_end_time = $shift_end_time;
    					$schedule->patient_id = NULL;
    					$schedule->group_id = $group_id;
    					$schedule->shift_name = '';
    					$schedule->shift_color = '';
    					$schedule->shift_date = $date;
    					$schedule->leave_applied = 1;
                        $schedule->only_leave = 1;
    					$schedule->leave_type = $request->leave_type;
    					$schedule->leave_reason = $request->reason;
    					$schedule->leave_group_id = $group_id;
    					$schedule->leave_approved = 0;
                        $schedule->leave_approved_by = NULL;
                        $schedule->leave_approved_date_time = NULL;
                        $schedule->leave_notified_to = NULL;
                        $schedule->notified_group = NULL;
                        $schedule->is_active = 1;
                        // $schedule->scheduled_work_duration = $result['scheduled_work_duration'];
                        // $schedule->extra_work_duration = $result['extra_work_duration'];
                        // $schedule->emergency_work_duration = $result['emergency_work_duration'];
                        // $schedule->ob_work_duration = $result['ob_work_duration'];
                        // $schedule->ob_type = $result['ob_type'];
                        // $schedule->vacation_duration = $vacation_duration;
                        $schedule->scheduled_work_duration = 0;
                        $schedule->extra_work_duration = 0;
                        $schedule->emergency_work_duration = 0;
                        $schedule->ob_work_duration = 0;
                        $schedule->ob_type = null;
                        $schedule->ob_start_time = null;
						$schedule->ob_end_time = null;
                        $schedule->vacation_duration = 24;
    					$schedule->status = 0;
    					$schedule->schedule_type = 'basic';
    					$schedule->created_by = Auth::id();
    					$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    					$schedule->save();
    				}

    				//--notify-top-most-company-for-new-leave-application--//
    				$user = User::find(Auth::user()->top_most_parent_id);
    				$data_id =  $schedule->id;

    				$notification_template = EmailTemplate::where('mail_sms_for', 'leave-applied')->first();
                    $variable_data = [
                        '{{requested_by}}'  => Auth::User()->name,
                        '{{date}}'          => $schedule->shift_date,
                        '{{reason}}'        => $request->reason
                    ];
    				actionNotification($user,$data_id,$notification_template,$variable_data);
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
                        $schedules = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->get();

                        if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
                        {
                            foreach ($schedules as $key => $schedule) {
                                $vacation_duration = 0;
                                if($request->leave_type=='vacation')
                                {
                                    $vacation_duration = $schedule->scheduled_work_duration + $schedule->extra_work_duration + $schedule->ob_work_duration + $schedule->emergency_work_duration;
                                }
                                $schedule->update([
                                    'leave_applied' => '1',
                                    'leave_type' => $request->leave_type,
                                    'leave_reason' => $value['reason'],
                                    'leave_group_id' => $group_id,
                                    'vacation_duration' => $vacation_duration,
                                ]);
                            }
                        }
    					else
    					{
    						$shift_start_time = $date.' 00:00:00';
                            $shift_end_time = $date.' 23:59:59';
    						$schedule = new Schedule;
    						$schedule->schedule_template_id = $schedule_template_id;
    						$schedule->shift_id = NULL;
    						$schedule->user_id = Auth::id();
    						$schedule->parent_id = NULL;
    						$schedule->shift_start_time = $shift_start_time;
    						$schedule->shift_end_time = $shift_end_time;
    						$schedule->patient_id = NULL;
    						$schedule->group_id = $group_id;
    						$schedule->shift_name = '';
    						$schedule->shift_color = '';
    						$schedule->shift_date = $date;
    						$schedule->leave_applied = 1;
    						$schedule->only_leave = 1;
    						$schedule->leave_type = $request->leave_type;
    						$schedule->leave_reason = $value['reason'];
    						$schedule->leave_group_id = $group_id;
    						$schedule->leave_approved = 0;
                            $schedule->leave_approved_by = NULL;
                            $schedule->leave_approved_date_time = NULL;
                            $schedule->leave_notified_to = NULL;
                            $schedule->notified_group = NULL;
                            $schedule->is_active = 1;
                            $schedule->scheduled_work_duration = 0;
                            $schedule->extra_work_duration = 0;
                            $schedule->emergency_work_duration = 0;
                            $schedule->ob_work_duration = 0;
                            $schedule->ob_type = null;
                            $schedule->ob_start_time = null;
							$schedule->ob_end_time = null;
                            $schedule->vacation_duration = 24;
    						$schedule->status = 0;
    						$schedule->schedule_type = 'basic';
    						$schedule->created_by = Auth::id();
    						$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    						$schedule->save();
    					}

                        //--notify-top-most-company-for-new-leave-application--//
                        $user = User::find(Auth::user()->top_most_parent_id);
                        $data_id =  $schedule->id;

                        $notification_template = EmailTemplate::where('mail_sms_for', 'leave-applied')->first();
                        $variable_data = [
                            '{{requested_by}}'  => Auth::User()->name,
                            '{{date}}'          => $schedule->shift_date,
                            '{{reason}}'        => $value['reason']
                        ];
                        actionNotification($user,$data_id,$notification_template,$variable_data);
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

    public function show($id)
    {
    	try 
    	{
    		$checkId= Schedule::where('id',$id)->with('user:id,name,gender,user_type_id','user.userType:id,name', 'leaves:id,leave_group_id,date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('Leave','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		return prepareResult(true,'View Leave' ,$checkId, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

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
            $group_id = generateRandomNumber();
    		if(!empty($request->leave_group_id))
    		{
    			$leave = Schedule::where('leave_group_id',$request->leave_group_id)->first();
    			$update = Schedule::where('leave_group_id',$request->leave_group_id)
    			->update([
    				'leave_approved' => '1',
    				'leave_approved_by' => Auth::id(), 
    				'leave_approved_date_time' => date('Y-m-d H:i:s')
    			]);
    			$dates = [];
    			$leaves = Schedule::where('leave_group_id',$request->leave_group_id)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->groupBy('group_id')->get();
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
                		//----notify-selected-employees-to-accept-work----//
    					$data_id =  $request->leave_group_id;
    					$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-request')->first();
                        $variable_data = [
                            '{{name}}'          => $user->name,
                            '{{requested_by}}'  => Auth::User()->name,
                            '{{dates}}'         => $dates
                        ];
    					actionNotification($user,$data_id,$notification_template,$variable_data);
                        //----------------------------------------------//
    					$users_id[] = $user->id;
    				}

    				Schedule::where('leave_group_id',$request->leave_group_id)
    				->update([
    					'leave_notified_to' => json_encode($users_id),
    					'notified_group' => $notified_group
    				]);
    			}

                //----notify-employee-leave-approved----//
    			$user = User::find($leave->user_id);
    			$data_id =  $request->leave_group_id;
                $extra_param = ['employee_id'=>$user->id,'leave_group_id'=>$request->leave_group_id];
    			$notification_template = EmailTemplate::where('mail_sms_for', 'leave-approved-multiple')->first();
    			$variable_data = [
                    '{{name}}'  => $user->name,
                    '{{dates}}' => $dates,
                    '{{approved_by}}'=> Auth::user()->name
                ];
    			actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param);
                //--------------------------------------//
    		}
    		elseif(!empty($request->leaves))
    		{
    			$leave_ids = [];
    			foreach ($request->leaves as $key => $value) 
    			{
    				$leave_ids[] = $value['schedule_id']; 
    				$leave = Schedule::find($value['schedule_id']);
    				$user = User::find($value['employee_id']);
    				$update = Schedule::find($value['schedule_id'])
    				->update([
    					'leave_approved' => '1',
    					'leave_approved_by' => Auth::id(), 
    					'leave_approved_date_time' => date('Y-m-d H:i:s'), 
    					'status' => 1,
    					'slot_assigned_to' => $user->id
    				]);
                    $shift_start_time = $leave->shift_start_time;
                    $shift_end_time = $leave->shift_end_time;

                    $shift_type = $leave->shift_type;
                    $result = scheduleWorkCalculation($leave->shift_date,$shift_start_time,$shift_end_time,'extra',$shift_type);

                    $schedule = new Schedule;
                    $schedule->top_most_parent_id = $leave->top_most_parent_id;
                    $schedule->user_id = $user->id;
                    $schedule->patient_id = $leave->patient_id;
                    $schedule->shift_id = $leave->shift_id;
                    $schedule->parent_id = $leave->parent_id;
                    $schedule->created_by = Auth::id();
                    $schedule->slot_assigned_to = $leave->slot_assigned_to;
                    $schedule->employee_assigned_working_hour_id = $leave->assignedWork_id;
                    $schedule->schedule_template_id = $leave->schedule_template_id;
                    $schedule->schedule_type = $leave->schedule_type;
                    $schedule->shift_date = $leave->shift_date;
                    $schedule->group_id = $group_id;
                    $schedule->shift_name = $leave->shift_name;
                    $schedule->shift_type = $leave->shift_type;
                    $schedule->shift_color = $leave->shift_color;
                    $schedule->shift_start_time = $shift_start_time;
                    $schedule->shift_end_time = $shift_end_time;
                    $schedule->leave_applied = 0;
                    $schedule->leave_group_id = null;
                    $schedule->leave_type = null;
                    $schedule->leave_reason = null;
                    $schedule->leave_approved = 0;
                    $schedule->leave_approved_by = null;
                    $schedule->leave_approved_date_time = null;
                    $schedule->leave_notified_to = null;
                    $schedule->notified_group = null;
                    $schedule->is_active = 1;
                    $schedule->scheduled_work_duration = $result['scheduled_work_duration'];
                    $schedule->extra_work_duration = $result['extra_work_duration'];
                    $schedule->emergency_work_duration = $result['emergency_work_duration'];
                    $schedule->ob_work_duration = $result['ob_work_duration'];
                    $schedule->ob_type = $result['ob_type'];
                    $schedule->ob_start_time = $result['ob_start_time'];
					$schedule->ob_end_time = $result['ob_end_time'];
                    $schedule->status = $request->status ? $request->status :0;
                    $schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
                    $schedule->save();

    				//----notify-emp-for-schedule-assigned---//

					$data_id =  $schedule->id;
					$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-assignment')->first();
					$variable_data = [
                        '{{name}}'          => $user->name,
                        '{{schedule_title}}'=> $schedule->title,
                        '{{date}}'          => $schedule->shift_date,
                        '{{start_time}}'    => $schedule->shift_start_time,
                        '{{end_time}}'      => $schedule->shift_end_time,
                        '{{assigned_by}}'   => Auth::User()->name
                    ];
					actionNotification($user,$data_id,$notification_template,$variable_data);
                    //----------------------------------------//

                    //----notify-employee-leave-approved---//
					$leave_approved_user = User::find($leave->user_id);
					$data_id =  $leave->id;
                    $extra_param = ['employee_id'=>$leave->user_id,'leave_group_id'=>$leave->leave_group_id];
					$notification_template = EmailTemplate::where('mail_sms_for', 'leave-approved')->first();
					$variable_data = [
                        '{{name}}'  => $user->name,
                        '{{date}}' => $leave->shift_date,
                        '{{approved_by}}'=> Auth::user()->name
                    ];
					actionNotification($leave_approved_user,$data_id,$notification_template,$variable_data,$extra_param);
                    //----------------------------------------//
    			}
    			$leaves = Schedule::whereIn('id',$leave_ids)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->groupBy('leave_group_id')->get();
    		}


    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$leaves, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    public function leavesApproveByGroupId($leave_group_id)
    {
    	try 
    	{
    		$update = Schedule::where('leave_group_id',$leave_group_id)
    		->update([
    			'leave_approved' => '1',
    			'leave_approved_by' => Auth::id(), 
    			'leave_approved_date_time' => date('Y-m-d'), 
    			'status' => 1
    		]);
    		$leaves = Schedule::where('leave_group_id', $leave_group_id)
    		->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')
    		->groupBy('leave_group_id')
    		->first();
    		$dates = [];
    		foreach ($leaves->leaves as $key => $value) {
    			$dates[] = $value->shift_date;
    		}

    		//-------notify-employee-leave-approved--------//
    		$user = User::find($leaves->user_id);
    		$data_id =  $leave_group_id;
            $extra_param = ['employee_id'=>$user->id,'leave_group_id'=>$leave_group_id];
    		$notification_template = EmailTemplate::where('mail_sms_for', 'leave-approved-multiple')->first();
    		$variable_data = [
                '{{name}}'  => $user->name,
                '{{dates}}' => implode(',', $dates),
                '{{approved_by}}'=> Auth::user()->name
            ];
    		actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param);
            //----------------------------------------------//

    		return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$leaves, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    public function leaveScheduleSlotSelected(Request $request)
    {
    	DB::beginTransaction();
    	try 
    	{
    		$leave = Schedule::where('leave_group_id',$request->leave_group_id)->where('shift_date',$request->date)->first();

    		Schedule::where('leave_group_id',$request->leave_group_id)
    		->where('shift_date',$request->date)
    		->update([
    			'slot_assigned_to' => Auth::id()
    		]);


    		$shift_name         = $leave->shift_name;
    		$shift_color        = $leave->shift_color;
    		$parent_id          = $leave->id;

    		$date = $leave->shift_date;

            $shift_start_time = $leave->shift_start_time;
            $shift_end_time = $leave->shift_end_time;

            $shift_type = $leave->shift_type;
            $result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,'extra',$shift_type);

    		$schedule = new Schedule;
			$schedule->top_most_parent_id = $leave->top_most_parent_id;
			$schedule->user_id = Auth::id();
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
            $schedule->shift_type = $leave->shift_type;
			$schedule->shift_color = $leave->shift_color;
			$schedule->shift_start_time = $shift_start_time;
			$schedule->shift_end_time = $shift_end_time;
			$schedule->leave_applied = 0;
			$schedule->leave_group_id = null;
			$schedule->leave_type = null;
			$schedule->leave_reason = null;
			$schedule->leave_approved = 0;
			$schedule->leave_approved_by = null;
			$schedule->leave_approved_date_time = null;
			$schedule->leave_notified_to = null;
			$schedule->notified_group = null;
			$schedule->is_active = 1;
			$schedule->scheduled_work_duration = $result['scheduled_work_duration'];
            $schedule->extra_work_duration = $result['extra_work_duration'];
            $schedule->emergency_work_duration = $result['emergency_work_duration'];
            $schedule->ob_work_duration = $result['ob_work_duration'];
            $schedule->ob_type = $result['ob_type'];
            $schedule->ob_start_time = $result['ob_start_time'];
            $schedule->ob_end_time = $result['ob_end_time'];
			$schedule->status = $leave->status;
			$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
			$schedule->save();
    		$schedule->save();
            
    		$dates = [];
    		$leaves = Schedule::where('notified_group',$leave->notified_group)->where('assign_status','vacant')->get();
    		foreach ($leaves as $key => $value) {
    			$dates[] = $value->date;
    		}
    		if(!empty($dates))
    		{
    			$dates = implode(',', $dates);

    			$users = User::whereIn('id',json_decode($leave->leave_notified_to))
    			->where('id','!=',Auth::id())
    			->get();

    			$users_id = [];

    			foreach ($users as $key => $user) 
    			{
    				//--notify-emp-request-schedule-slot-selected--//
    				$data_id =  $leave->group_id;
    				$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-slot-selected')->first();
    				$variable_data = [
						'{{name}}'          => $user->name,
						'{{selected_by}}'   => Auth::User()->name,
						'{{vacant_dates}}'  => $dates,
						'{{selected_date}}' => $leave->date
					];
    				actionNotification($user,$data_id,$notification_template,$variable_data);
    				//-------------------------------------------//
    				$users_id[] = $user->id;
    			}

    			Schedule::where('group_id',$request->group_id)
    			->update([
    				'leave_notified_to' => json_encode($users_id),
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

    public function companyLeave(Request $request)
    {
    	DB::beginTransaction();
    	try 
    	{
    		$schedule_id = [];
    		$leave_group_id = generateRandomNumber();
    		if(ScheduleTemplate::where('status','1')->count()<=0)
    		{
    			return prepareResult(false,getLangByLabelGroups('Leave','message_template_not_found'), ['Add Schedule Template First'],config('httpcodes.not_found'));
    		}
    		$schedule_template_id = ScheduleTemplate::where('status','1')->first()->id;
    		$validation = \Validator::make($request->all(), [
    			'leaves'      => 'required|array',
    		]);

    		if ($validation->fails()) 
    		{
    			return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    		}
    		$dates = [];
    		foreach ($request->leaves as $key => $leave) 
    		{
    			foreach ($leave['dates'] as $key => $shift_date) 
    			{
    				$dates[] = $shift_date;
    				$schedules = Schedule::where('shift_date',$shift_date)->where('user_id',$request->emp_id)->get();
    				if(!empty($schedules))
    				{
                        foreach ($schedules as $key => $schedule) {
                            $schedule_id[] = $schedule->id;
                            $vacation_duration = 0;
                            if($request->leave_type=='vacation')
                            {
                                $vacation_duration = $schedule->scheduled_work_duration + $schedule->extra_work_duration + $schedule->ob_work_duration + $schedule->emergency_work_duration;
                            }
                            $schedule->update([
                                'leave_applied' => '1',
                                'leave_type' => $request->leave_type,
                                'leave_reason' => $request->reason,
                                'leave_group_id' => $leave_group_id,
                                'leave_approved' => '1',
                                'leave_approved_by' => Auth::id(), 
                                'leave_approved_date_time' => date('Y-m-d H:i:s'),
                                'slot_assigned_to' => $leave['assign_emp'],
                                'vacation_duration' => $vacation_duration,
                            ]);
                            
                            $shift_start_time = $schedule->shift_start_time;
                            $shift_end_time = $schedule->shift_end_time;
                            $shift_type = $schedule->shift_type;
                            $result = scheduleWorkCalculation($schedule->shift_date,$shift_start_time,$shift_end_time,'extra',$shift_type);

                            $assSchedule = new Schedule;
                            $assSchedule->top_most_parent_id = $schedule->top_most_parent_id;
                            $assSchedule->user_id = $leave['assign_emp'];
                            $assSchedule->patient_id = $schedule->patient_id;
                            $assSchedule->shift_id = $schedule->shift_id;
                            $assSchedule->parent_id = $schedule->id;
                            $assSchedule->created_by = Auth::id();
                            $assSchedule->slot_assigned_to = null;
                            $assSchedule->employee_assigned_working_hour_id = $schedule->assignedWork_id;
                            $assSchedule->schedule_template_id = $schedule->schedule_template_id;
                            $assSchedule->schedule_type = $schedule->schedule_type;
                            $assSchedule->shift_date = $schedule->shift_date;
                            $assSchedule->group_id = $schedule->group_id;
                            $assSchedule->shift_name = $schedule->shift_name;
                            $assSchedule->shift_type = $schedule->shift_type;
                            $assSchedule->shift_color = $schedule->shift_color;
                            $assSchedule->shift_start_time = $shift_start_time;
                            $assSchedule->shift_end_time = $shift_end_time;
                            $assSchedule->leave_applied = 0;
                            $assSchedule->leave_group_id = null;
                            $assSchedule->leave_type = null;
                            $assSchedule->leave_reason = null;
                            $assSchedule->leave_approved = 0;
                            $assSchedule->leave_approved_date_time = null;
                            $assSchedule->leave_notified_to = null;
                            $assSchedule->notified_group = null;
                            $assSchedule->is_active = 1;
                            $assSchedule->scheduled_work_duration = $result['scheduled_work_duration'];
                            $assSchedule->extra_work_duration = $result['extra_work_duration'];
                            $assSchedule->emergency_work_duration = $result['emergency_work_duration'];
                            $assSchedule->ob_work_duration = $result['ob_work_duration'];
                            $assSchedule->ob_type = $result['ob_type'];
                            $assSchedule->ob_start_time = $result['ob_start_time'];
                            $assSchedule->ob_end_time = $result['ob_end_time'];
                            $assSchedule->status = $schedule->status;
                            $assSchedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
                            $assSchedule->save();

                            //-------notify-employee-schedule-assigned-----------//
                            $user = User::find($leave['assign_emp']);
                            $data_id =  $assSchedule->id;

                            $notification_template = EmailTemplate::where('mail_sms_for', 'schedule-assignment')->first();
                            $variable_data = [
                                '{{name}}'          => $user->name,
                                '{{schedule_title}}'=> $assSchedule->title,
                                '{{date}}'          => $assSchedule->shift_date,
                                '{{start_time}}'    => $assSchedule->shift_start_time,
                                '{{end_time}}'      => $assSchedule->shift_end_time,
                                '{{assigned_by}}'   => Auth::User()->name
                            ];
                            actionNotification($user,$data_id,$notification_template,$variable_data);
                            //------------------------------------------//
                        }
    				}
    				else
    				{
    					$shift_start_time = $shift_date.' 00:00:00';
                        $shift_end_time = $shift_date.' 23:59:59';
    					$schedule = new Schedule;
    					$schedule->schedule_template_id = $schedule_template_id;
    					$schedule->shift_id = null;
    					$schedule->user_id = $request->emp_id;
    					$schedule->parent_id = null;
    					$schedule->shift_start_time = $shift_start_time;
    					$schedule->shift_end_time = $shift_end_time;
    					$schedule->patient_id = null;
    					$schedule->group_id = $leave_group_id;
    					$schedule->shift_name = '';
    					$schedule->shift_color = '';
    					$schedule->shift_date = $shift_date;
    					$schedule->leave_applied = 1;
    					$schedule->leave_type = $request->leave_type;
    					$schedule->leave_reason = $request->reason;
    					$schedule->leave_group_id = $leave_group_id;
    					$schedule->is_active = 1;
    					$schedule->leave_approved_date_time = date('Y-m-d H:i:s');
    					$schedule->leave_approved = 1;
    					$schedule->leave_approved_by = Auth::id();
    					$schedule->only_leave = 1;
    					$schedule->status = 1;
                        $schedule->scheduled_work_duration = 0;
                        $schedule->extra_work_duration = 0;
                        $schedule->emergency_work_duration = 0;
                        $schedule->ob_work_duration = 0;
                        $schedule->ob_type = null;
                        $schedule->ob_start_time = null;
						$schedule->ob_end_time = null;
                        $schedule->vacation_duration = 24;
    					$schedule->schedule_type = 'basic';
    					$schedule->created_by = Auth::id();
    					$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
    					$schedule->save();
    					$schedule_id[] = $schedule->id;
    				}
    			}
    		}

    		//--notify-emp-leave-applied-approved-by-company-----//
    		$user = User::find($request->emp_id);
    		$data_id =  $leave_group_id;
            $extra_param = ['employee_id'=>$user->id,'leave_group_id'=>$leave_group_id];

    		$notification_template = EmailTemplate::where('mail_sms_for', 'leave-applied-approved')->first();
    		$variable_data = [
				'{{name}}' 	=> $user->name,
				'{{dates}}'	=> implode(',', $dates),
				'{{approved_by}}'=> Auth::user()->name
			];
    		actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param);
    		//-----------------------------------------------------//

    		$data = Schedule::where('leave_group_id',$leave_group_id)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,company_type_id')->get();
    	DB::commit();
    	return prepareResult(true,getLangByLabelGroups('Leave','message_create') ,$data, config('httpcodes.success')); 
    	}
    	catch (\Throwable $exception) 
    	{
	    	\Log::error($exception);
	    	DB::rollback();
	    	return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
	    }
	}

	public function getCompanyLeaves()
	{
		try {
			$query = Schedule::where('top_most_parent_id', Auth::user()->top_most_parent_id)->where('leave_applied',1)->get(['shift_date']);
			$dates = [];
			foreach ($query as $key => $value) {
				$dates[] = $value->shift_date;
			}

			return prepareResult(true,"Leave Date list",$dates,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}
}
