<?php

namespace App\Http\Controllers\Api\V1\User;

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
			$user = getUser();
			$child_ids = [Auth::id()];
			if(!empty($user->branch_id) && $user->user_type_id==11) 
			{
                $child_ids = User::where('branch_id', auth()->id())->pluck('id');
            }
            else
            {
            	foreach (Auth::user()->childs as $key => $value) {
					$child_ids[] = $value->id;
				}
            }
			
			$query = Schedule::orderBy('id', 'DESC')->whereIn('user_id',$child_ids)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,branch_name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,branch_name,company_type_id')->where('leave_applied',1);

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
				$query->whereDate('shift_date',">=" ,$request->start_date);
			}
			if(!empty($request->end_date))
			{
				$query->whereDate('shift_date',"<=" ,$request->end_date);
			}
			if(!empty($request->dates))
			{
				$query->whereIn('shift_date',$request->dates);
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
				$query = $pagination;
			}
			else
			{
				$query = $query->get();
			}
			return prepareResult(true,getLangByLabelGroups('Leave','message_list'),$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
	logException($exception);
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
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_record_not_found'), ['Add Schedule Template First'],config('httpcodes.not_found'));
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
								'leave_applied_date' => date('Y-m-d'),
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
						$schedule->leave_applied_date = date('Y-m-d');
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
						$schedule->vacation_duration = 24*60;
						$schedule->status = 0;
						$schedule->schedule_type = 'basic';
						$schedule->created_by = Auth::id();
						$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
						$schedule->save();
					}

    				//--notify-top-most-company-for-new-leave-application--//
					$getUser = User::find(Auth::user()->top_most_parent_id);
					$data_id =  $schedule->id;
					$extra_param = ['leave_group_id'=>$group_id,'leave_object'=>Schedule::where('id',$schedule->id)->with('user:id,name')->first()];
					$notification_template = EmailTemplate::where('mail_sms_for', 'leave-applied')->first();
					$variable_data = [
						'{{requested_by}}'  => aceussDecrypt(Auth::User()->name),
						'{{date}}'          => $schedule->shift_date,
						'{{reason}}'        => $request->reason
					];
					$socket = ($getUser->id==auth()->id()) ? false : true;
					actionNotification($getUser,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
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
									'leave_applied_date' => date('Y-m-d'),
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
							$schedule->leave_applied_date = date('Y-m-d');
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
							$schedule->vacation_duration = 24*60;
							$schedule->status = 0;
							$schedule->schedule_type = 'basic';
							$schedule->created_by = Auth::id();
							$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
							$schedule->save();
						}

                        //--notify-top-most-company-for-new-leave-application--//
						$getUser = User::find(Auth::user()->top_most_parent_id);
						$data_id =  $schedule->id;
						$extra_param = ['leave_group_id'=>$group_id,'leave_object'=>Schedule::where('id',$schedule->id)->with('user:id,name')->first()];
						$notification_template = EmailTemplate::where('mail_sms_for', 'leave-applied')->first();
						$variable_data = [
							'{{requested_by}}'  => aceussDecrypt(Auth::User()->name),
							'{{date}}'          => $schedule->shift_date,
							'{{reason}}'        => $value['reason']
						];
						$socket = ($getUser->id==auth()->id()) ? false : true;
						actionNotification($getUser,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
					}
				}  
			}

			$data = Schedule::where('leave_group_id',$group_id)->get();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Leave','message_create') ,$data, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function show($id)
	{
		try 
		{
			$checkId= Schedule::where('id',$id)->with('user:id,name,gender,user_type_id','user.userType:id,name', 'leaves:id,leave_group_id,date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,branch_name,company_type_id')->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('Leave','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,getLangByLabelGroups('Leave','message_view') ,$checkId, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
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
			return prepareResult(true,getLangByLabelGroups('Leave','message_update') ,$leave, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
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
				return prepareResult(false,getLangByLabelGroups('Leave','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			if(strtotime($checkId->shift_date)<=strtotime(date('Y-m-d')))
			{
				return prepareResult(false,getLangByLabelGroups('Leave','message_date_must_be_greater_than_today_date'), [],config('httpcodes.not_found'));
			}
			Schedule::where('id',$id)->delete();
			return prepareResult(true,getLangByLabelGroups('Leave','message_delete') ,[], config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
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

			return prepareResult(true,getLangByLabelGroups('Leave','message_list'),$dates,config('httpcodes.success'));
		}
		catch(Exception $exception) {
	logException($exception);
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
				$leaves = Schedule::where('leave_group_id',$request->leave_group_id)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,branch_name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,branch_name,company_type_id')->groupBy('group_id')->get();
				foreach ($leaves as $key => $value) {
					$dates[] = $value->date;
				}
				$dates = implode(',', $dates);

				if($request->notify_employees == true)
				{
					$users = User::whereIn('employee_type',$request->employee_type)->where('id','!=',$leave->user_id)->get();
					$users_id = [];

					foreach ($users as $key => $user) 
					{
                		//----notify-selected-employees-to-accept-work----//
						$data_id =  $request->leave_group_id;
						$extra_param = ['leave_group_id'=>$request->leave_group_id];
						$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-request')->first();
						if($user)
						{
							$variable_data = [
								'{{name}}'          => aceussDecrypt($user->name),
								'{{requested_by}}'  => aceussDecrypt(Auth::User()->name),
								'{{dates}}'         => $dates
							];
							$socket = ($user->id==auth()->id()) ? false : true;
							actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
						}
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
				$getUser = User::find($leave->user_id);
				$data_id =  $request->leave_group_id;
				$extra_param = ['employee_id'=>$getUser->id,'leave_group_id'=>$request->leave_group_id];
				$notification_template = EmailTemplate::where('mail_sms_for', 'leave-approved-multiple')->first();
				if($getUser)
				{
					$variable_data = [
						'{{name}}'  => aceussDecrypt($getUser->name),
						'{{dates}}' => $dates,
						'{{approved_by}}'=> aceussDecrypt(Auth::user()->name)
					];
					$socket = ($getUser->id==auth()->id()) ? false : true;
					actionNotification($getUser,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
				}
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
					$result = scheduleWorkCalculation($leave->shift_date,$shift_start_time,$shift_end_time,'extra',$shift_type,$user->id,$leave->assignedWork_id);

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
					$schedule->ob_red_work_duration = $result['ob_red_work_duration'];
					$schedule->ob_red_start_time = $result['ob_red_start_time'];
					$schedule->ob_red_end_time = $result['ob_red_end_time'];

					$schedule->ob_weekend_work_duration = $result['ob_weekend_work_duration'];
					$schedule->ob_weekend_start_time = $result['ob_weekend_start_time'];
					$schedule->ob_weekend_end_time = $result['ob_weekend_end_time'];

					$schedule->ob_weekday_work_duration = $result['ob_weekday_work_duration'];
					$schedule->ob_weekday_start_time = $result['ob_weekday_start_time'];
					$schedule->ob_weekday_end_time = $result['ob_weekday_end_time'];
					$schedule->status = $request->status ? $request->status :0;
					$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
					$schedule->save();

    				//----notify-emp-for-schedule-assigned---//

					$data_id =  $schedule->id;
					$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-assignment')->first();
					if($user)
					{
						$variable_data = [
							'{{name}}'          => aceussDecrypt($user->name),
							'{{schedule_title}}'=> $schedule->title,
							'{{date}}'          => $schedule->shift_date,
							'{{start_time}}'    => $schedule->shift_start_time,
							'{{end_time}}'      => $schedule->shift_end_time,
							'{{assigned_by}}'   => aceussDecrypt(Auth::User()->name)
						];
						$socket = ($user->id==auth()->id()) ? false : true;
						actionNotification($user,$data_id,$notification_template,$variable_data, null, null, $socket);
					}
                    //----------------------------------------//

                    //----notify-employee-leave-approved---//
					$leave_approved_user = User::find($leave->user_id);
					$data_id =  $leave->id;
					$extra_param = ['employee_id'=>$leave->user_id,'leave_group_id'=>$leave->leave_group_id];
					$notification_template = EmailTemplate::where('mail_sms_for', 'leave-approved')->first();
					if($user)
					{
						$variable_data = [
							'{{name}}'  => aceussDecrypt($user->name),
							'{{date}}' => $leave->shift_date,
							'{{approved_by}}'=> aceussDecrypt(Auth::user()->name)
						];
						$socket = ($leave_approved_user->id==auth()->id()) ? false : true;
						actionNotification($leave_approved_user,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
					}
                    //----------------------------------------//
				}
				$leaves = Schedule::whereIn('id',$leave_ids)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,branch_name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,branch_name,company_type_id')->groupBy('leave_group_id')->get();
			}


			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$leaves, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
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
			->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,branch_name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,branch_name,company_type_id')
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
			if($user)
			{
				$variable_data = [
					'{{name}}'  => aceussDecrypt($user->name),
					'{{dates}}' => implode(',', $dates),
					'{{approved_by}}'=> aceussDecrypt(Auth::user()->name)
				];
				$socket = ($user->id==auth()->id()) ? false : true;
				actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
			}
            //----------------------------------------------//

			return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$leaves, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function leaveScheduleSlotSelected(Request $request)
	{
		DB::beginTransaction();
		try 
		{
			$leaves = Schedule::whereIn('id',$request->schedule_ids)->get();

			$leave1 = Schedule::whereIn('id',$request->schedule_ids)->first();

			Schedule::whereIn('id',$request->schedule_ids)
			->update([
				'slot_assigned_to' => Auth::id(),
				'assign_status' => 'assigned'
			]);
			foreach ($leaves as $key => $leave) {
				$shift_name         = $leave->shift_name;
				$shift_color        = $leave->shift_color;
				$parent_id          = $leave->id;

				$date = $leave->shift_date;

				$shift_start_time = $leave->shift_start_time;
				$shift_end_time = $leave->shift_end_time;

				$shift_type = $leave->shift_type;
				$result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,'extra',$shift_type,Auth::id());

				$schedule = new Schedule;
				$schedule->top_most_parent_id = $leave->top_most_parent_id;
				$schedule->user_id = Auth::id();
				$schedule->patient_id = $leave->patient_id;
				$schedule->shift_id = $leave->shift_id;
				$schedule->parent_id = $leave->id;
				$schedule->created_by = Auth::id();
				$schedule->slot_assigned_to = null;
				$schedule->employee_assigned_working_hour_id = $result['assignedWork_id'];
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
				$schedule->ob_red_work_duration = $result['ob_red_work_duration'];
				$schedule->ob_red_start_time = $result['ob_red_start_time'];
				$schedule->ob_red_end_time = $result['ob_red_end_time'];

				$schedule->ob_weekend_work_duration = $result['ob_weekend_work_duration'];
				$schedule->ob_weekend_start_time = $result['ob_weekend_start_time'];
				$schedule->ob_weekend_end_time = $result['ob_weekend_end_time'];

				$schedule->ob_weekday_work_duration = $result['ob_weekday_work_duration'];
				$schedule->ob_weekday_start_time = $result['ob_weekday_start_time'];
				$schedule->ob_weekday_end_time = $result['ob_weekday_end_time'];
				$schedule->status = $leave->status;
				$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
				$schedule->save();
			}
			$selected_dates = [];
			$vacant_dates = [];
			$leaves1 = Schedule::where('notified_group',$leave1->notified_group)->where('assign_status','vacant')->get();
			foreach ($leaves1 as $key => $value) {
				$vacant_dates[] = $value->shift_date;
			}
			foreach ($leaves as $key => $value) {
				$selected_dates[] = $value->shift_date;
			}
			$vacant_dates = implode(',', $vacant_dates);
			if(!empty($vacant_dates))
			{
				$vacant_dates = 'N/A';
			}
			$selected_dates = implode(',', $selected_dates);

			$users = User::whereIn('id',json_decode($leave1->leave_notified_to))
			->where('id','!=',Auth::id())
			->get();

			$users_id = [];
			$data_id =  $leave->group_id;
			$company = User::find(Auth::user()->top_most_parent_id);
			//--notify-company-schedule-slot-selected--//
			$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-slot-selected')->first();
			$extra_param = ['leave_group_id'=>$leave->leave_group_id];
			if($company)
			{
				$variable_data = [
					'{{name}}'          => aceussDecrypt($company->name),
					'{{selected_by}}'   => aceussDecrypt(Auth::User()->name),
					'{{selected_dates}}'=> $selected_dates,
					'{{vacant_dates}}'  => $vacant_dates
				];
				$socket = ($company->id==auth()->id()) ? false : true;
				actionNotification($company,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
			}

			foreach ($users as $key => $user) 
			{
				//--notify-emp-request-schedule-slot-selected--//
				$data_id =  $leave->group_id;
				$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-slot-selected')->first();
				$extra_param = ['leave_group_id'=>$leave->leave_group_id];
				if($user)
				{
					$variable_data = [
						'{{name}}'          => aceussDecrypt($user->name),
						'{{selected_by}}'   => aceussDecrypt(Auth::User()->name),
						'{{selected_dates}}'=> $selected_dates,
						'{{vacant_dates}}'  => $vacant_dates
					];
					$socket = ($user->id==auth()->id()) ? false : true;
					actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
				}
				//-------------------------------------------//
				$users_id[] = $user->id;
			}

			Schedule::where('group_id',$request->group_id)
			->update([
				'leave_notified_to' => json_encode($users_id),
				'notified_group' => $leave->notified_group
			]);

			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_create') ,$leaves, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function companyLeave(Request $request)
	{
		DB::beginTransaction();
		try 
		{
			$validation = \Validator::make($request->all(), [
				'leaves'      => 'required|array',
			]);

			if ($validation->fails()) 
			{
				return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
			}
			$schedule_id = [];
			$dates = [];
			$leave_group_id = generateRandomNumber();
			if(ScheduleTemplate::where('status','1')->count()<=0)
			{
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_record_not_found'), ['Add Schedule Template First'],config('httpcodes.not_found'));
			}
			$schedule_template_id = ScheduleTemplate::where('status','1')->first()->id;
			foreach ($request->leaves as $key => $leave) 
			{
				if(!empty($leave['schedule_id']))
				{
					$schedule = Schedule::find($leave['schedule_id']);
					$dates[] = $schedule->shift_date;
					$schedule_id[] = $schedule->id;
					$vacation_duration = 0;
					if($request->leave_type=='vacation')
					{
						$vacation_duration = $schedule->scheduled_work_duration + $schedule->extra_work_duration + $schedule->ob_work_duration + $schedule->emergency_work_duration;
					}
					$schedule->update([
						'leave_applied' => '1',
						'leave_applied_date' => date('Y-m-d'),
						'leave_type' => $request->leave_type,
						'leave_reason' => $request->reason,
						'leave_group_id' => $leave_group_id,
						'leave_approved' => '1',
						'leave_approved_by' => Auth::id(), 
						'leave_approved_date_time' => date('Y-m-d H:i:s'),
						'slot_assigned_to' => @$leave['assign_emp'],
						'vacation_duration' => $vacation_duration,
					]);

					$shift_start_time = $schedule->shift_start_time;
					$shift_end_time = $schedule->shift_end_time;
					$shift_type = $schedule->shift_type;
					$result = scheduleWorkCalculation($schedule->shift_date,$shift_start_time,$shift_end_time,'extra',$shift_type,@$leave['assign_emp']);

					$assSchedule = new Schedule;
					$assSchedule->top_most_parent_id = $schedule->top_most_parent_id;
					$assSchedule->user_id = @$leave['assign_emp'];
					$assSchedule->patient_id = $schedule->patient_id;
					$assSchedule->shift_id = $schedule->shift_id;
					$assSchedule->parent_id = $schedule->id;
					$assSchedule->created_by = Auth::id();
					$assSchedule->slot_assigned_to = null;
					$assSchedule->employee_assigned_working_hour_id = $schedule->assignedWork_id;
					$assSchedule->schedule_template_id = $result['assignedWork_id'];
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
			        if(isset($leave['assign_emp']))
			        {
						$user = User::find($leave['assign_emp']);
						$data_id =  $assSchedule->id;
						if($user)
						{
							$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-assignment')->first();
							if($user)
							{
								$variable_data = [
									'{{name}}'          => aceussDecrypt($user->name),
									'{{schedule_title}}'=> $assSchedule->title,
									'{{date}}'          => $assSchedule->shift_date,
									'{{start_time}}'    => $assSchedule->shift_start_time,
									'{{end_time}}'      => $assSchedule->shift_end_time,
									'{{assigned_by}}'   => aceussDecrypt(Auth::User()->name)
								];
								$socket = ($user->id==auth()->id()) ? false : true;
								actionNotification($user,$data_id,$notification_template,$variable_data, null, $socket);
							}
						}
					}
			        //------------------------------------------//
				}
				else
				{
					$dates[] = $leave['date'];
					$shift_date = $leave['date'];
					$schedules = Schedule::where('shift_date',$shift_date)->where('user_id',$request->emp_id)->get();
					if($schedules->isEmpty() == false)
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
								'leave_applied_date' => date('Y-m-d'),
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
							$result = scheduleWorkCalculation($schedule->shift_date,$shift_start_time,$shift_end_time,'extra',$shift_type,$leave['assign_emp']);

							$assSchedule = new Schedule;
							$assSchedule->top_most_parent_id = $schedule->top_most_parent_id;
							$assSchedule->user_id = $leave['assign_emp'];
							$assSchedule->patient_id = $schedule->patient_id;
							$assSchedule->shift_id = $schedule->shift_id;
							$assSchedule->parent_id = $schedule->id;
							$assSchedule->created_by = Auth::id();
							$assSchedule->slot_assigned_to = null;
							$assSchedule->employee_assigned_working_hour_id = $result['assignedWork_id'];
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
							$schedule->ob_red_work_duration = $result['ob_red_work_duration'];
							$schedule->ob_red_start_time = $result['ob_red_start_time'];
							$schedule->ob_red_end_time = $result['ob_red_end_time'];

							$schedule->ob_weekend_work_duration = $result['ob_weekend_work_duration'];
							$schedule->ob_weekend_start_time = $result['ob_weekend_start_time'];
							$schedule->ob_weekend_end_time = $result['ob_weekend_end_time'];

							$schedule->ob_weekday_work_duration = $result['ob_weekday_work_duration'];
							$schedule->ob_weekday_start_time = $result['ob_weekday_start_time'];
							$schedule->ob_weekday_end_time = $result['ob_weekday_end_time'];
							$assSchedule->status = $schedule->status;
							$assSchedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
							$assSchedule->save();

					        //-------notify-employee-schedule-assigned-----------//
							$user = User::find($leave['assign_emp']);
							$data_id =  $assSchedule->id;

							$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-assignment')->first();
							if($user)
							{
								$variable_data = [
									'{{name}}'          => aceussDecrypt($user->name),
									'{{schedule_title}}'=> $assSchedule->title,
									'{{date}}'          => $assSchedule->shift_date,
									'{{start_time}}'    => $assSchedule->shift_start_time,
									'{{end_time}}'      => $assSchedule->shift_end_time,
									'{{assigned_by}}'   => aceussDecrypt(Auth::User()->name)
								];
								$socket = ($getUser->id==auth()->id()) ? false : true;
								actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, $socket);
							}
							
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
						$schedule->leave_applied_date = date('Y-m-d');
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
						$schedule->vacation_duration = 24*60;
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
			if($user)
			{
				$data_id =  $leave_group_id;
				$extra_param = ['employee_id'=>$user->id,'leave_group_id'=>$leave_group_id,'leave_object'=>Schedule::where('leave_group_id',$leave_group_id)->with('user:id,name')->get()];

				$notification_template = EmailTemplate::where('mail_sms_for', 'leave-applied-approved')->first();
			
				$variable_data = [
					'{{name}}' 	=> aceussDecrypt($user->name),
					'{{dates}}'	=> implode(',', $dates),
					'{{approved_by}}'=> aceussDecrypt(Auth::user()->name)
				];
				$socket = ($user->id==auth()->id()) ? false : true;
				actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param, null, $socket);
			}
    		//-----------------------------------------------------//

			$data = Schedule::where('leave_group_id',$leave_group_id)->with('user:id,name,gender,user_type_id,branch_id','user.userType:id,name','user.branch:id,branch_id,name,branch_name,company_type_id', 'leaves:id,leave_group_id,shift_date','leaveApprovedBy:id,name,branch_id,user_type_id','leaveApprovedBy.userType:id,name','leaveApprovedBy.branch:id,branch_id,name,branch_name,company_type_id')->get();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Leave','message_create_approve') ,$data, config('httpcodes.success')); 
		}
		catch (\Throwable $exception) 
		{
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function getCompanyLeaves()
	{
		try {
			if(Auth::user()->user_type_id == 2)
			{
				$query = Schedule::where('top_most_parent_id', Auth::user()->top_most_parent_id)->where('user_id','!=',Auth::id())->where('leave_applied',1)->get(['shift_date','id','shift_start_time','shift_end_time','leave_group_id','user_id','approved_by_company','verified_by_employee']);
			}
			else
			{
				$query = Schedule::whereJsonContains('leave_notified_to', Auth::id())->where('user_id','!=',Auth::id())->where('leave_applied',1)->get(['shift_date','id','shift_start_time','shift_end_time','leave_group_id','user_id','approved_by_company','verified_by_employee']);
			}
			return prepareResult(true,getLangByLabelGroups('Leave','message_list'),$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
	logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}
}
