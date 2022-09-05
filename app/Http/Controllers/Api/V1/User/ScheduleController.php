<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleTemplate;
use Validator;
use Auth;
use DB;
use App\Models\User;
use App\Models\CompanyWorkShift;
use App\Models\AgencyWeeklyHour;
use Exception;
use App\Models\OVHour;
use PDF;
use App\Models\EmailTemplate;
use Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UserScheduledDate;
use App\Exports\EmployeeWorkingHoursExport;
use App\Exports\PatientAssignedHoursExport;
use App\Models\PatientImplementationPlan;

class ScheduleController extends Controller
{
	public function __construct()
	{
		// $this->middleware('permission:schedule-browse',['except' => ['show']]);
		// $this->middleware('permission:schedule-add', ['only' => ['store']]);
		// $this->middleware('permission:schedule-edit', ['only' => ['update']]);
		// $this->middleware('permission:schedule-read', ['only' => ['show']]);
		// $this->middleware('permission:schedule-delete', ['only' => ['destroy']]);
	}
	

	public function schedules(Request $request)
	{
		try 
		{
			$query = Schedule::orderBy('created_at', 'DESC')->with('user:id,name,gender','patient:id,name,branch_id')->where('is_active',1);

			if($request->leave_applied == '0')
			{
				$query->where('leave_applied' ,0);
			}
			if($request->leave_applied == '1')
			{
				$query->where('leave_applied' ,1);
			}
			if(!empty($request->shift_id))
			{
				$query->where('shift_id' ,$request->shift_id);
			}
			if(!empty($request->schedule_template_id))
			{
				$query->where(function ($query) use ($request) {
                     $query->where('schedule_template_id' ,$request->schedule_template_id)->orWhere('leave_applied' ,1);
                });
			}
			if(!empty($request->schedule_type))
			{
				$query->where('schedule_type' ,$request->schedule_type);
			}

			if(!empty($request->patient_id))
			{
				$query->where('patient_id' ,$request->patient_id);
			}
			if(!empty($request->user_id))
			{
				$query->where('user_id' ,$request->user_id);
			}

			if(!empty($request->shift_date))
			{
				$query->where('shift_date',$request->shift_date);
			}
			
			if(!empty($request->shift_start_date))
			{
				$query->where('shift_date',">=" ,$request->shift_start_date);
			}
			if(!empty($request->shift_end_date))
			{
				$query->where('shift_date',"<=" ,$request->shift_end_date);
			}

			if(!empty($request->shift_start_time))
			{
				$query->where('shift_start_time',">=" ,$request->shift_start_time);
			}
			if(!empty($request->shift_end_time))
			{
				$query->where('shift_end_time',"<=" ,$request->shift_end_time);
			}
			if($request->group_id == 'yes')
			{
				$query->groupBy('group_id');
			}
			if (!empty($request->month)) 
			{
				$month = $request->month;
				$query->whereRaw('MONTH(shift_date) = '.$month);
			}

			if(!empty($request->leave_group_id))
			{
				$query->where('leave_group_id','like','%'.$request->leave_group_id.'%');
			}
			if($request->status == '0')
			{
				$query->where('status' ,0);
			}
			if($request->status == '1')
			{
				$query->where('status' ,1);
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

			return prepareResult(true,getLangByLabelGroups('Schedule','message_list'),$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function schedulesCopy(Request $request)
	{
		try 
		{
			$query = Schedule::orderBy('created_at', 'DESC')
			->with('user:id,name,gender','patient:id,name,branch_id')
			->where('is_active',1)
			->with('user:id,user_type_id,name,branch_id','user.branch:id,name','patient:id,user_type_id,name,branch_id','patient.branch:id,name');

			if(!empty($request->shift_id))
			{
				$query->where('shift_id' ,$request->shift_id);
			}
			if(!empty($request->schedule_template_id))
			{
				$query->where('schedule_template_id' ,$request->schedule_template_id);
			}
			if(!empty($request->schedule_type))
			{
				$query->where('schedule_type' ,$request->schedule_type);
			}

			if(!empty($request->patient_id))
			{
				$query->where('patient_id' ,$request->patient_id);
			}
			if(!empty($request->user_id))
			{
				$query->where('user_id' ,$request->user_id);
			}

			if(!empty($request->shift_date))
			{
				$query->where('shift_date',$request->shift_date);
			}
			
			if(!empty($request->shift_start_date))
			{
				$query->where('shift_date',">=" ,$request->shift_start_date);
			}
			if(!empty($request->shift_end_date))
			{
				$query->where('shift_date',"<=" ,$request->shift_end_date);
			}

			if(!empty($request->shift_start_time))
			{
				$query->where('shift_start_time',">=" ,$request->shift_start_time);
			}
			if(!empty($request->shift_end_time))
			{
				$query->where('shift_end_time',"<=" ,$request->shift_end_time);
			}
			if($request->group_id == 'yes')
			{
				$query->groupBy('group_id');
			}
			if (!empty($request->month)) 
			{
				$month = $request->month;
				$query->whereRaw('MONTH(shift_date) = '.$month);
			}

			if(!empty($request->leave_group_id))
			{
				$query->where('leave_group_id','like','%'.$request->leave_group_id.'%');
			}
			if($request->status == '0')
			{
				$query->where('status' ,0);
			}
			if($request->status == '1')
			{
				$query->where('status' ,1);
			}
			if($request->leave_applied == '0')
			{
				$query->where('leave_applied' ,0);
			}
			if($request->leave_applied == '1')
			{
				$query->where('leave_applied' ,1);
			}
			if($request->leave_approved == '0')
			{
				$query->where('leave_approved' ,0);
			}
			if($request->leave_approved == '1')
			{
				$query->where('leave_approved' ,1);
			}

			if(!empty($request->perPage))
			{

				$perPage = $request->perPage;
				$page = $request->input('page', 1);
				$total = $query->count();
				$result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get(['id','schedule_template_id','shift_id','shift_name','shift_color','shift_type','shift_date','shift_start_time','shift_end_time','rest_start_time','rest_end_time','schedule_type','patient_id','user_id','verified_by_employee','approved_by_company','leave_applied','leave_approved']);

				$pagination =  [
					'data' => $result,
					'total' => $total,
					'current_page' => $page,
					'per_page' => $perPage,
					'last_page' => ceil($total / $perPage)
				];
				return prepareResult(true,getLangByLabelGroups('Schedule','message_list'),$pagination,config('httpcodes.success'));
			}
			else
			{
				$query = $query->get(['id','schedule_template_id','shift_id','shift_name','shift_color','shift_type','shift_date','shift_start_time','shift_end_time','rest_start_time','rest_end_time','schedule_type','patient_id','user_id','verified_by_employee','approved_by_company','leave_applied','leave_approved']);
			}

			return prepareResult(true,getLangByLabelGroups('Schedule','message_list'),$query,config('httpcodes.success'));
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
			$validator = Validator::make($request->all(),[   
				'schedule_template_id' => 'required|exists:schedule_templates,id'
			],
			[   
				'schedule_template_id' =>  getLangByLabelGroups('Schedule','message_schedule_template_id')
			]);
			if ($validator->fails()) 
			{
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$schedule_ids = [];
			$group_id = generateRandomNumber();
			foreach ($request->schedules as $key => $value) 
			{
				$date = date('Y-m-d',strtotime($value['date']));
				if(!empty($value['type']) && $value['type'] == 'delete')
				{
					$schedules = Schedule::where('shift_date',$date)->where('schedule_template_id',$request->schedule_template_id)->delete();
				}
				else
				{
					$start_date = $value['date'];
					$assignedWork_id = null;
					$assignedWork = null;
					if(!empty($shift['user_id']))
					{
						$user = User::find($shift['user_id']);
						if(!empty($user->assignedWork))
						{
							$assignedWork = $user->assignedWork;
							$assignedWork_id = $assignedWork->id;
						}
					}
					foreach($value['shifts'] as $key=>$shift)
					{
						if(empty($shift['type']))
						{

						}
						elseif($shift['type'] == 'delete')
						{
							$schedules = Schedule::where('id',$shift['schedule_id'])->delete();
						}
						else
						{
							$shift_name 		= null;
							$shift_color 		= null;
							$shift_type 		= null;
							if(!empty($shift['shift_id']))
							{
								$c_shift = CompanyWorkShift::find($shift['shift_id']);
								if (!is_object($c_shift)) {
									return prepareResult(false,getLangByLabelGroups('CompanyWorkShift','message_record_not_found'), [],config('httpcodes.not_found'));
								}
								$shift_name 		= $c_shift->shift_name;
								$shift_color 		= $c_shift->shift_color;
								$shift_type 		= $c_shift->shift_type;
								$rest_start_time 	= $date.' '.$c_shift->rest_start_time;
								$rest_end_time 		= $date.' '.$c_shift->rest_end_time;
							}
							elseif(!empty($shift['rest_start_time']) && !empty($shift['rest_end_time']))
							{
								$rest_start_time 	= $shift['rest_start_time'];
								$rest_end_time 		= $shift['rest_end_time'];
							}
							else
							{
								$rest_start_time 	= null;
								$rest_end_time 		= null;
							}

							$shift_start_time = $shift['shift_start_time'];
							$shift_end_time = $shift['shift_end_time'];

							$result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,$shift['schedule_type'],$shift_type,$rest_start_time,$rest_end_time);
							if($shift['type'] == 'add')
							{
								$schedule = new Schedule;
							}
							else
							{
								$schedule = Schedule::find($shift['schedule_id']);
							}

							$schedule->user_id = $shift['employee_id'];
							$schedule->patient_id = $shift['patient_id'];
							$schedule->branch_id = $request->branch_id;
							$schedule->shift_id = $shift['shift_id'];
							$schedule->parent_id = $request->parent_id;
							$schedule->created_by = Auth::id();
							$schedule->slot_assigned_to = null;
							$schedule->employee_assigned_working_hour_id = $assignedWork_id;
							$schedule->schedule_template_id = $request->schedule_template_id;
							$schedule->schedule_type = $shift['schedule_type'];
							$schedule->shift_date = $date;
							$schedule->group_id = $group_id;
							$schedule->shift_name = $shift_name;
							$schedule->shift_type = $shift_type;
							$schedule->shift_color = $shift_color;
							$schedule->shift_start_time = $shift_start_time;
							$schedule->shift_end_time = $shift_end_time;
							$schedule->rest_start_time = $rest_start_time;
							$schedule->rest_end_time = $rest_end_time;
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
							$schedule->status = 0;
							$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
							$schedule->save();

							if(!empty($shift['user_id']))
							{
								$datesData = UserScheduledDate::where('emp_id',$shift['user_id'])
								->where('schedule_template_id',$request->schedule_template_id)
								->where('start_date',"<=" ,$start_date)
								->where('end_date',">=" ,$start_date)
								->first();
								if(empty($datesData))
								{
									$datesData = new UserScheduledDate;
									$datesData->schedule_template_id = $request->schedule_template_id;
									$datesData->emp_id = $shift['user_id'];
									$datesData->start_date = $start_date;
									$datesData->end_date = date('Y-m-d', strtotime('+27 days',strtotime($start_date)));
									$datesData->save();

									$userUpdate = User::find($shift['user_id'])->update(['schedule_start_date'=>$start_date]);
								}
							}

						    //----notify-emp-for-schedule-assigned---//
							if(!empty($shift['user_id']))
							{
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
							}
						    //----------------------------------------//

							// if(!empty($shift['patient_id']))
							// {
							// 	$patientAssignedHours = AgencyWeeklyHour::where('user_id',$shift['patient_id'])
							// 	->where('start_date','<=' ,$schedule->shift_date)
							// 	->where('end_date','>=',$schedule->shift_date)
							// 	->orderBy('id','desc')->first();
							// 	if(empty($patientAssignedHours))
							// 	{
							// 		$patientAssignedHours = AgencyWeeklyHour::where('user_id',$request->patient_id)->orderBy('id','desc')->first();
							// 	}
							// 	if(!empty($patientAssignedHours))
							// 	{
							// 		$scheduledHours = $patientAssignedHours->scheduled_hours + $schedule->scheduled_work_duration + $schedule->emergency_work_duration + $schedule->ob_work_duration + $schedule->extra_work_duration;
							// 		$patientAssignedHours->update(['scheduled_hours'=>$scheduledHours]);
							// 	}
							// }
							$schedule_ids[] = $schedule->id;
						}
					}
				}
			}
			DB::commit();
			$data = Schedule::whereIn('id',$schedule_ids)->with('user:id,name,gender','scheduleDates:group_id,shift_date')->groupBy('group_id')->get();
			
			return prepareResult(true,getLangByLabelGroups('Schedule','message_create') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			\Log::error($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function destroy($id)
	{
		try 
		{
			$schedule= Schedule::find($id);
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			$schedule->delete();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_delete') ,[], config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}

	public function show($id)
	{
		try 
		{
			// $schedule= Schedule::with('user:id,name,gender','scheduleDates:id,group_id,shift_date,shift_start_time,shift_end_time')->groupBy('group_id')->where('id',$id)->first();
			$schedule= Schedule::with('user:id,name,gender')->groupBy('group_id')->where('id',$id)->first();
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,getLangByLabelGroups('Schedule','message_show') ,$schedule, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function getUserSchedules($id)
	{
		try 
		{
			$query = Schedule::where('user_id', $id)->where('shift_date',date('Y-m-d'))->get();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_list'),$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function scheduleClones(Request $request)
	{
		$validator = Validator::make($request->all(),[   
			'old_template_id' => 'required|exists:schedule_templates,id',
		]);
		if ($validator->fails()) {
			return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
		}
		DB::beginTransaction();
		try 
		{
			if(!empty($request->new_template_name))
			{
				$scheduleTemplate = new ScheduleTemplate;
				$scheduleTemplate->title = $request->new_template_name;
				$scheduleTemplate->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
				$scheduleTemplate->status = 0;
				$scheduleTemplate->save();

				$new_template_id = $scheduleTemplate->id;
			}
			else
			{
				$new_template_id = $request->new_template_id;
			}

			$schedules = Schedule::where('schedule_template_id',$request->old_template_id)->where('shift_start_time','>',date('Y-m-d H:i:s'))->get();
			$group_id = generateRandomNumber();
			$schedule_ids = [];
			foreach ($schedules as $key => $schedule) {
				$newSchedule = $schedule->replicate();
				$newSchedule->schedule_template_id = $new_template_id;
				$newSchedule->group_id = $group_id;
				$newSchedule->is_active = 0;
				$newSchedule->entry_mode = $request->entry_mode ? $request->entry_mode : 'Web';
				$newSchedule->save();

				//----notify-emp-for-schedule-assigned---//
				$user = User::find($schedule->user_id);
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

				
				$schedule_ids[] = $newSchedule->id;
			}
			$data = Schedule::whereIn('id',$schedule_ids)->with('user:id,name,gender','scheduleDates:group_id,shift_date')->groupBy('group_id')->get();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_create') ,$data, config('httpcodes.success'));

		}
		catch(Exception $exception) {
			\Log::error($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function scheduleReports(Request $request)
	{
		try 
		{
			$query = Schedule::orderBy('created_at', 'DESC')->where('top_most_parent_id',Auth::user()->id)->where('is_active',1)->groupBy('user_id');

			if(!empty($request->user_ids))
			{
				$query->whereIn('user_id', $request->user_ids);
			}
			$query = $query->get();
			$data = [];

			foreach ($query as $key => $value) {
				$schduled = Schedule::where('user_id',$value->user_id)->where('is_active',1)->sum('scheduled_work_duration');
				$extra = Schedule::where('user_id',$value->user_id)->where('is_active',1)->sum('extra_work_duration');
				$obe = Schedule::where('user_id',$value->user_id)->where('is_active',1)->sum('ob_work_duration');
				$emergency = Schedule::where('user_id',$value->user_id)->where('is_active',1)->sum('emergency_work_duration');
				$vacation = Schedule::where('user_id',$value->user_id)->where('is_active',1)->sum('vacation_duration');

				$data['labels'][] = $value->user->name;
				$data['total_hours'][] = $schduled + $extra + $obe + $emergency;
				$data['regular_hours'][] = $schduled;
				$data['extra_hours'][] = $extra;
				$data['obe_hours'][] = $obe;
				$data['emergency_hours'][] = $emergency;
				$data['vacation_hours'][] = $vacation;
			}
			return prepareResult(true,getLangByLabelGroups('Schedule','message_report'),$data,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function schedulesDates(Request $request)
	{
		try 
		{
			$query = Schedule::orderBy('created_at', 'DESC')->with('user:id,name,gender')->where('top_most_parent_id',Auth::user()->id)->where('is_active',1);

			if(!empty($request->shift_id))
			{
				$query->where('shift_id' ,$request->shift_id);
			}
			if(!empty($request->schedule_template_id))
			{
				$query->where('schedule_template_id' ,$request->schedule_template_id);
			}
			if(!empty($request->schedule_type))
			{
				$query->where('schedule_type' ,$request->schedule_type);
			}

			if(!empty($request->patient_id))
			{
				$query->where('patient_id' ,$request->patient_id);
			}
			if(!empty($request->user_id))
			{
				$query->where('user_id' ,$request->user_id);
			}

			if(!empty($request->shift_date))
			{
				$query->where('shift_date',$request->shift_date);
			}
			
			if(!empty($request->shift_start_date))
			{
				$query->where('shift_date',">=" ,$request->shift_start_date);
			}
			if(!empty($request->shift_end_date))
			{
				$query->where('shift_date',"<=" ,$request->shift_end_date);
			}

			if(!empty($request->shift_start_time))
			{
				$query->where('shift_start_time',">=" ,$request->shift_start_time);
			}
			if(!empty($request->shift_end_time))
			{
				$query->where('shift_end_time',"<=" ,$request->shift_end_time);
			}
			if($request->group_id == 'yes')
			{
				$query->groupBy('group_id');
			}
			if (!empty($request->month)) 
			{
				$month = $request->month;
				$query->whereRaw('MONTH(shift_date) = '.$month);
			}
			$query = $query->get(['id','shift_id','schedule_template_id','schedule_type','patient_id','user_id','shift_date','shift_start_time','shift_end_time','group_id','approved_by_company','verified_by_employee']);
			$data = [];
			foreach ($query as $key => $value) {
				$data[$key]['id'] = $value->id;
				$data[$key]['shift_date'] = $value->shift_date;
			}
			return prepareResult(true,getLangByLabelGroups('Schedule','message_dates_list'),$data,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}

	}

	public function scheduleFilter(Request $request)
	{
		try 
		{
			$users_ids = [];

			$users = User::where('top_most_parent_id',auth()->user()->top_most_parent_id)->where('top_most_parent_id',Auth::user()->id)->where('status','1');
			if(!empty($request->user_type_id))
			{
				$users->where('user_type_id' ,$request->user_type_id);
			}
			else
			{
				$users->where('user_type_id' ,3);
			}
			if(!empty($request->user_ids))
			{
				$users->whereIn('id' ,$request->user_ids);
			}
			if(!empty($request->employee_type))
			{
				$users->where('employee_type' ,$request->employee_type);
			}
			if(!empty($request->user_name))
			{
				$users->where('name','like','%'.$request->name.'%');
			}
			if(!empty($request->branch_id))
			{
				$users->where('branch_id' ,$request->branch_id);
			}
			if(!empty($request->sort_by_name))
			{
				$users->orderBy('name' ,$request->sort_by_name);
			}
			if($request->status == '0')
			{
				$query->where('status' ,0);
			}
			if($request->status == '1')
			{
				$query->where('status' ,1);
			}
			

			if(!empty($request->perPage))
			{
				$perPage = $request->perPage;
				$page = $request->input('page', 1);
				$total = $users->count();
				$users = $users->offset(($page - 1) * $perPage)->limit($perPage)->get(['id','name','employee_type']);
				foreach ($users as $key => $value) {
					$schedules = Schedule::orderBy('created_at','DESC')
					->where('user_id',$value->id)
					->where('is_active',1)
					->where('shift_date',">=" ,$request->shift_start_date)
					->where('shift_date',"<=" ,$request->shift_end_date);

					if(!empty($request->schedule_template_id))
					{
						$schedules = $schedules->where('schedule_template_id' ,$request->schedule_template_id);
					}

					$schedules = $schedules->get(["leave_applied","leave_type","id","leave_reason","ob_type","ob_start_time","ob_end_time","patient_id","schedule_type","shift_date","shift_end_time","shift_start_time","shift_name","emergency_work_duration","extra_work_duration","scheduled_work_duration","vacation_duration","ob_work_duration","shift_type","shift_id",'approved_by_company','verified_by_employee']);
					$value->schedules = $schedules;
				}

				$pagination =  [
					'data' => $users,
					'total' => $total,
					'current_page' => $page,
					'per_page' => $perPage,
					'last_page' => ceil($total / $perPage)
				];
				return prepareResult(true,getLangByLabelGroups('Schedule','message_list'),$pagination,config('httpcodes.success'));
			}
			else
			{
				$users = $users->get(['id','name','employee_type']);
				foreach ($users as $key => $value) {
					$schedules = Schedule::orderBy('created_at','DESC')
					->where('user_id',$value->id)
					->where('is_active',1)
					->where('shift_date',">=" ,$request->shift_start_date)
					->where('shift_date',"<=" ,$request->shift_end_date);

					if(!empty($request->schedule_template_id))
					{
						$schedules = $schedules->where('schedule_template_id' ,$request->schedule_template_id);
					}
					
					$schedules = $schedules->get(["leave_applied","leave_type","id","leave_reason","ob_type","ob_start_time","ob_end_time","patient_id","schedule_type","shift_date","shift_end_time","shift_start_time","shift_name","emergency_work_duration","extra_work_duration","scheduled_work_duration","vacation_duration","ob_work_duration","shift_type","shift_id",'approved_by_company','verified_by_employee']);
					$value->schedules = $schedules;
				}
			}			

			return prepareResult(true,getLangByLabelGroups('Schedule','message_list'),$users,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}

	}

	public function scheduleStats(Request $request)
	{
		$request_for = !empty($request->request_for) ? $request->request_for : 7;
		$datelabels = [];
		$total_hours = [];
		$regular_hours = [];
		$extra_hours = [];
		$obe_hours = [];
		$emergency_hours = [];
		$vacation_hours = [];
		if(!empty($request->start_date) && !empty($request->end_date)) 
		{
			$date1 = strtotime($request->start_date);
			$date2 = strtotime($request->end_date);
			for($curDate=$date1; $curDate<=$date2; $curDate += (86400))
			{
				$date = date('Y-m-d', $curDate);
				$datelabels[] = $date;

				$query = Schedule::select([
					\DB::raw('SUM(scheduled_work_duration) as regular_hours'),
					\DB::raw('SUM(extra_work_duration) as extra_hours'),
					\DB::raw('SUM(ob_work_duration) as obe_hours'),
					\DB::raw('SUM(emergency_work_duration) as emergency_hours'),
					\DB::raw('SUM(vacation_duration) as vacation_hours')
				]);
				$query->whereDate('shift_date', $date); 
				$query->where('status', 1); 
				$query->where('user_id', $request->user_id);        
				$result = $query->first();

				$total = $result->regular_hours + $result->extra_hours + $result->obe_hours + $result->emergency_hours + $result->vacation_hours;

				$total_hours[] = $total;
				$regular_hours[] = $result->regular_hours;
				$extra_hours[] = $result->extra_hours;
				$obe_hours[] = $result->obe_hours;
				$emergency_hours[] = $result->emergency_hours;
				$vacation_hours[] = $result->vacation_hours;
			}
		}
		else
		{
			for($i = $request_for; $i>=1; $i--)
			{
				$date = date('Y-m-d',strtotime('-'.($i-1).' days'));
				$datelabels[] = $date;

				$query = Schedule::select([
					\DB::raw('SUM(scheduled_work_duration) as regular_hours'),
					\DB::raw('SUM(extra_work_duration) as extra_hours'),
					\DB::raw('SUM(ob_work_duration) as obe_hours'),
					\DB::raw('SUM(emergency_work_duration) as emergency_hours'),
					\DB::raw('SUM(vacation_duration) as vacation_hours')
				]);
				$query->whereDate('shift_date', $date); 
				$query->where('status', 1); 
				$query->where('user_id', $request->user_id);        
				$result = $query->first();

				$total = $result->regular_hours + $result->extra_hours + $result->obe_hours + $result->emergency_hours + $result->vacation_hours;

				$total_hours[] = $total;
				$regular_hours[] = $result->regular_hours;
				$extra_hours[] = $result->extra_hours;
				$obe_hours[] = $result->obe_hours;
				$emergency_hours[] = $result->emergency_hours;
				$vacation_hours[] = $result->vacation_hours;
			}
		}


		$returnObj = [
			'labels' => $datelabels,
			'total_hours' => $total_hours,
			'regular_hours' => $regular_hours,
			'extra_hours' => $extra_hours,
			'obe_hours' => $obe_hours,
			'emergency_hours' => $emergency_hours,
			'vacation_hours' => $vacation_hours,
		];
		return prepareResult(true,getLangByLabelGroups('Schedule','message_statistics'),$returnObj,config('httpcodes.success'));
	}

	public function patientCompletedHours(Request $request)
	{
		try 
		{
			$dates = [];
			$data = [];
			if(!empty($request->start_date) && !empty($request->end_date))
			{
				$date1 = strtotime($request->start_date);
				$date2 = strtotime($request->end_date);
				for($curDate=$date1; $curDate<=$date2; $curDate += (86400))
				{
					$dates[] = date('Y-m-d', $curDate);
				}
			}
			elseif(!empty($request->dates))
			{
				$dates = $request->dates;
			}
			else
			{
				$schedules = Schedule::where('patient_id',$request->patient_id)->get(['shift_date']);
				foreach ($schedules as $key => $value) {
					$dates[] = $value->shift_date;
				}
			}

			foreach ($dates as $key => $shift_date) 
			{
				if(Schedule::where('patient_id',$request->patient_id)->where('shift_date',$shift_date)->count() > 0)
				{
					$allSch = Schedule::where('patient_id',$request->patient_id)->where('shift_date',$shift_date)->get();
					$check_status = [];
					foreach ($allSch as $key => $value) {
						$check_status[] = $value->status;
					}
					$query = Schedule::select([
						\DB::raw('SUM(scheduled_work_duration) as regular_hours'),
						\DB::raw('SUM(extra_work_duration) as extra_hours'),
						\DB::raw('SUM(ob_work_duration) as obe_hours'),
						\DB::raw('SUM(emergency_work_duration) as emergency_hours'),
						\DB::raw('SUM(vacation_duration) as vacation_hours')
					]);
					$query->whereDate('shift_date', $shift_date);
					$query->where('patient_id', $request->patient_id);
					$query->where('leave_applied', 0);  
					// $query->where('status', 1);        
					$result = $query->first();
					$patientAssignedHours = AgencyWeeklyHour::where('user_id',$request->patient_id)->sum('assigned_hours') * 60;
					$patientSchedules = Schedule::select([
						\DB::raw('SUM(scheduled_work_duration) as regular_hours'),
						\DB::raw('SUM(extra_work_duration) as extra_hours'),
						\DB::raw('SUM(ob_work_duration) as obe_hours'),
						\DB::raw('SUM(emergency_work_duration) as emergency_hours'),
						\DB::raw('SUM(vacation_duration) as vacation_hours')
					])
					->whereDate('shift_date','<=', $shift_date)
					->where('patient_id', $request->patient_id) 
					->where('leave_applied', 0) 
					->first();
					if(!empty($result))
					{
						$total_hours = $result->regular_hours + $result->extra_hours + $result->obe_hours + $result->emergency_hours + $result->vacation_hours;
						$patientCompletedHours = $patientSchedules->regular_hours + $patientSchedules->extra_hours + $patientSchedules->obe_hours + $patientSchedules->emergency_hours + $patientSchedules->vacation_hours;
						if(in_array(0, $check_status))
						{
							$status = 0;
						}
						else
						{
							$status = 1;
						}
						$data['date'][] = ["date" => $shift_date,"minutes" => $total_hours,"status" => $status,'remaining_minutes'=>$patientAssignedHours-$patientCompletedHours];
					}
				}
			}
			return prepareResult(true, getLangByLabelGroups('Schedule','message_patient_hours'),$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function employeeDatewiseWork(Request $request)
	{
		try 
		{
			$dates = [];
			$data = [];
			if(!empty($request->start_date) && !empty($request->end_date))
			{
				$date1 = strtotime($request->start_date);
				$date2 = strtotime($request->end_date);
				for($curDate=$date1; $curDate<=$date2; $curDate += (86400))
				{
					$dates[] = date('Y-m-d', $curDate);
				}
			}
			elseif(!empty($request->dates))
			{
				$dates = $request->dates;
			}
			else
			{
				$schedules = Schedule::where('user_id',$request->user_id)->where('is_active',1)->get(['shift_date']);
				foreach ($schedules as $key => $value) {
					$dates[] = $value->shift_date;
				}
			}

			foreach ($dates as $key => $shift_date) 
			{
				$schedules = Schedule::where('shift_date',$shift_date)->where('user_id',$request->user_id)->where('is_active',1)->get(['id','shift_id','schedule_template_id','schedule_type','patient_id','shift_start_time','shift_end_time','scheduled_work_duration','extra_work_duration','ob_work_duration','emergency_work_duration','vacation_duration','approved_by_company','verified_by_employee']);

				// foreach ($schedules as $key => $value) {
				// 	$value->hours = $value->scheduled_work_duration + $value->emergency_work_duration + $value->ob_work_duration + $value->extra_work_duration;
				// }
				$scheduled_work_duration = Schedule::where('shift_date',$shift_date)->where('user_id',$request->user_id)->where('is_active',1)->sum('scheduled_work_duration');
				$extra_work_duration = Schedule::where('shift_date',$shift_date)->where('user_id',$request->user_id)->where('is_active',1)->sum('extra_work_duration');
				$emergency_work_duration = Schedule::where('shift_date',$shift_date)->where('user_id',$request->user_id)->where('is_active',1)->sum('emergency_work_duration');
				$ob_work_duration = Schedule::where('shift_date',$shift_date)->where('user_id',$request->user_id)->where('is_active',1)->sum('ob_work_duration');
				$vacation_duration = Schedule::where('shift_date',$shift_date)->where('user_id',$request->user_id)->where('is_active',1)->sum('vacation_duration');
				$total_hours = $scheduled_work_duration + $extra_work_duration + $emergency_work_duration + $ob_work_duration + $vacation_duration;

				$data[] = [
					"date" => $shift_date,
					"schedules" => $schedules,
					"scheduled work duration" => $scheduled_work_duration,
					"extra_work_duration" => $extra_work_duration,
					"ob_work_duration" => $ob_work_duration,
					"emergency_work_duration" => $emergency_work_duration,
					"vacation_duration" => $vacation_duration,
					"total_hour"=>$total_hours
				];
			}
			return prepareResult(true,  getLangByLabelGroups('Schedule','message_employee_hours'),$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function scheduleApprove(Request $request)
	{
		DB::beginTransaction();
		try 
		{
			$ids = $request->schedule_ids;
			if($request->signed_method=='bankid' && !empty(auth()->user()->personal_number))
            {
                $userInfo = getUser();
                $top_most_parent_id = $userInfo->top_most_parent_id;
                $response = bankIdVerification($userInfo->personal_number, $userInfo->id, json_encode($ids), $userInfo->id, 'schedule-company-approval', $top_most_parent_id);
                /*if($response['error']==1) 
                {
                    return prepareResult(false, $response,$response, config('httpcodes.internal_server_error'));
                }*/
                $url[] = $response;
                $url[0]['person_id'] = $userInfo->id;
                $url[0]['group_token'] = $ids;
                $url[0]['uniqueId'] = $userInfo->unique_id;
                return prepareResult(true,'Mobile BankID Link', $url, config('httpcodes.success'));
            }
            else
            {
				foreach ($ids as $key => $id) 
				{
					$schedule= Schedule::find($id);
					if (!is_object($schedule)) {
						return prepareResult(false,getLangByLabelGroups('Schedule','message_record_not_found'), [],config('httpcodes.not_found'));
					}
					$user = User::find($schedule->user_id);
					if($user->report_verify == 'yes')
					{
			            //----notify-employee-schedule-approved----//
						$data_id =  $id;
						$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-approved')->first();
						$variable_data = [
							'{{name}}'  => $user->name,
							'{{schedule_title}}'=>$schedule->title,
							'{{date}}' => $schedule->shift_date,
							'{{start_time}}'=> $schedule->shift_start_time,
							'{{end_time}}'=> $schedule->shift_end_time,
							'{{approved_by}}'=> Auth::user()->name
						];
						actionNotification($user,$data_id,$notification_template,$variable_data);
			            //--------------------------------------//
						$schedule->update(['approved_by_company'=>1]);
					}
					else
					{
						if(!empty($schedule->patient_id))
						{
							$patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)
							->where('start_date','>=' ,$schedule->shift_date)
							->where('end_date','<=',$schedule->shift_date)
							->orderBy('id','desc')->first();
							if(empty($patientAssignedHours))
							{
								$patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)->orderBy('id','desc')->first();
							}
							$workedHours = $schedule->scheduled_work_duration + $schedule->emergency_work_duration + $schedule->ob_work_duration + $schedule->extra_work_duration;
							$completedHours = $patientAssignedHours->completed_hours + $workedHours;
							$remainingHours = $patientAssignedHours->remaining_hours - $workedHours;
							$patientAssignedHours->update(['completed_hours'=>$completedHours,'remaining_hours'=>$remainingHours]);
						}
						$schedule->update(['status'=>1,'approved_by_company'=>1]);
					}
				}
			}
			
			$data = Schedule::whereIn('id',$ids)->get();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_approve') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}


	public function scheduleVerify(Request $request)
	{
		DB::beginTransaction();
		try 
		{
			$ids = $request->schedule_ids;
			if($request->signed_method=='bankid' && !empty(auth()->user()->personal_number))
            {
                $userInfo = getUser();
                $top_most_parent_id = $userInfo->top_most_parent_id;
                $response = bankIdVerification($userInfo->personal_number, $userInfo->id, json_encode($ids), $userInfo->id, 'schedule-employee-approval', $top_most_parent_id);
                /*if($response['error']==1) 
                {
                    return prepareResult(false, $response,$response, config('httpcodes.internal_server_error'));
                }*/
                $url[] = $response;
                $url[0]['person_id'] = $userInfo->id;
                $url[0]['group_token'] = $ids;
                $url[0]['uniqueId'] = $userInfo->unique_id;
                return prepareResult(true,'Mobile BankID Link', $url, config('httpcodes.success'));
            }
            else
            {
				foreach ($ids as $key => $id) 
				{
					$schedule= Schedule::find($id);
					if (!is_object($schedule)) {
						return prepareResult(false,getLangByLabelGroups('Schedule','message_record_not_found'), [],config('httpcodes.not_found'));
					}

					if(!empty($schedule->patient_id))
					{
						$patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)
						->where('start_date','>=' ,$schedule->shift_date)
						->where('end_date','<=',$schedule->shift_date)
						->orderBy('id','desc')->first();
						if(empty($patientAssignedHours))
						{
							$patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)->orderBy('id','desc')->first();
						}
						$workedHours = $schedule->scheduled_work_duration + $schedule->emergency_work_duration + $schedule->ob_work_duration + $schedule->extra_work_duration;
						$completedHours = $patientAssignedHours->completed_hours + $workedHours;
						$remainingHours = $patientAssignedHours->remaining_hours - $workedHours;
						$patientAssignedHours->update(['completed_hours'=>$completedHours,'remaining_hours'=>$remainingHours]);
					}
					if(Auth::user()->verification_method =='normal')
					{
						$schedule->update(['status'=>1,'verified_by_employee'=>1]);
					}
					else
					{
						//add code for verification by bank_id
						$schedule->update(['status'=>1,'verified_by_employee'=>1]);
					}
					$company = User::find(Auth::user()->top_most_parent_id);
					//----notify-company-schedule-verified----//
					$exra_param = ['employee_id'=>$schedule->user_id, 'shift_date' => $schedule->shift_date,
						'shift_start_time'=> $schedule->shift_start_time,
						'shift_end_time'=> $schedule->shift_end_time,];
					$data_id =  $id;
					$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-verified')->first();
					$variable_data = [
						'{{name}}'  => $company->name,
						'{{schedule_title}}'=>$schedule->title,
						'{{date}}' => $schedule->shift_date,
						'{{start_time}}'=> $schedule->shift_start_time,
						'{{end_time}}'=> $schedule->shift_end_time,
						'{{verified_by}}'=> Auth::user()->name
					];
					actionNotification($company,$data_id,$notification_template,$variable_data,$exra_param);
		            //--------------------------------------//
				}
			}
			$data = Schedule::whereIn('id',$ids)->get();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_verify') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}


	public function employeeWorkingHoursExport(Request $request)
	{
		try{

			$rand = rand(0,1000);
			$dates = [$request->start_date,$request->end_date];
			$user_id  = $request->user_id;
			$excel = Excel::store(new EmployeeWorkingHoursExport($dates,$user_id), 'export/schedule/'.$rand.'.xlsx' , 'export_path');

			return prepareResult(true,getLangByLabelGroups('schedule','message_employee_working_hours_export') ,['url' => env('APP_URL').'public/export/schedule/'.$rand.'.xlsx'], config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}

	public function patientAssignedHoursExport(Request $request)
	{
		try{
			$rand = rand(0,1000);
			$dates = [$request->start_date,$request->end_date];
			$patient_id  = $request->patient_id;
			$excel = Excel::store(new PatientAssignedHoursExport($dates,$patient_id), 'export/schedule/'.$rand.'.xlsx' , 'export_path');

			return prepareResult(true,getLangByLabelGroups('schedule','message_patient_assigned_hours_export') ,['url' => env('APP_URL').'public/export/schedule'.$rand.'.xlsx'], config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}


	public function getSchedulesData(Request $request)
	{
		try{
			$validator = Validator::make($request->all(),[   
				'schedule_template_id' => 'required|exists:schedule_templates,id',
				// 'user_id' => 'required|exists:users,id',
				'date' => 'required|date'
			]);
			if ($validator->fails()) 
			{
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$ts = strtotime($request->date);
			$start_date = date('Y-m-01', $ts); 
			$end_date = date('Y-m-t', $ts); 

			$user = User::find($request->user_id);
			$data = [];
			$emp_ids = [];
			if(!empty($request->user_id))
			{
				$emp_ids[] = $request->user_id;
			}
			else
			{
				$data_sets = UserScheduledDate::where('start_date','<=',$end_date)->where('end_date','>',$start_date)->get();
				foreach ($data_sets as $key => $data_set) {
					$emp_ids[] = $data_set->emp_id;
				}
				$emp_ids = array_unique($emp_ids);
			}
			
			foreach ($emp_ids as $key => $emp_id) 
			{
				$user = User::where('id',$emp_id)->first(['id','name']);
				if (!is_object($user)) {
					$data[] = 'user not found';
				}
				else
				{
					$assignedWork = $user->assignedWork;
					$data[] = $user;

					$user_data_sets = UserScheduledDate::where('start_date','<=',$end_date)->where('end_date','>',$start_date)->where('emp_id',$user->id)->get();
					$user->data_sets = $user_data_sets;
					foreach ($user_data_sets as $key => $user_data_set) {
						// $user_schedules = Schedule::where('user_id',$user->id)->whereBetween('shift_date',[$user_data_set->start_date,$user_data_set->end_date])->where('schedule_template_id',$request->schedule_template_id)->get();


						if(strtotime($user_data_set->end_date) > strtotime(date('Y-m-d')))
						{
							$user_latest_schedule = Schedule::where('user_id',$user->id)->whereBetween('shift_date',[$user_data_set->start_date,date('Y-m-d')])->where('schedule_template_id',$request->schedule_template_id)->orderBy('id','desc')->where('leave_applied',0)->first();
							$scheduled_hours = Schedule::select([
								\DB::raw('SUM(scheduled_work_duration) + SUM(extra_work_duration) + SUM(ob_work_duration) + SUM(emergency_work_duration) as total_sum_hours')
							])->where('user_id',$user->id)->whereBetween('shift_date',[$user_data_set->start_date,date('Y-m-d')])->where('schedule_template_id',$request->schedule_template_id)->where('is_active',1)->where('leave_applied',0)->first()->total_sum_hours;
						}
						else
						{
							$user_latest_schedule = Schedule::where('user_id',$user->id)->whereBetween('shift_date',[$user_data_set->start_date,$user_data_set->end_date])->where('schedule_template_id',$request->schedule_template_id)->orderBy('id','desc')->where('leave_applied',0)->first();

							$scheduled_hours = Schedule::select([
								\DB::raw('SUM(scheduled_work_duration) + SUM(extra_work_duration) + SUM(ob_work_duration) + SUM(emergency_work_duration) as total_sum_hours')
							])->where('user_id',$user->id)->whereBetween('shift_date',[$user_data_set->start_date,$user_data_set->end_date])->where('schedule_template_id',$request->schedule_template_id)->where('is_active',1)->where('leave_applied',0)->first()->total_sum_hours;
						}

						if(!empty($assignedWork))
						{
							$assigned_hours = ($assignedWork->assigned_working_hour_per_week * 4) * $user_data_set->working_percent / 100;
							$remaining_hours = $assigned_hours - $scheduled_hours;
						}
						else
						{
							$assigned_hours = 0;
							$remaining_hours = 0;
						}
						// $user_data_set->schedules = $user_schedules;
						$user_data_set->latest_schedule = $user_latest_schedule;
						$user_data_set->assigned_hours = ($assigned_hours)*60;
						$user_data_set->scheduled_hours = ($scheduled_hours)*60;
						$user_data_set->remaining_hours = ($remaining_hours)*60;
					}
				}
			}		
			return prepareResult(true,getLangByLabelGroups('Schedule','message_list') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}

	public function getPatientsData(Request $request)
	{
		try 
		{
			$validator = Validator::make($request->all(),[   
				'schedule_template_id' => 'required|exists:schedule_templates,id',
				// 'user_id' => 'required|exists:users,id',
				// 'patient_id' => 'required'
			]);
			if ($validator->fails()) 
			{
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$template = ScheduleTemplate::find($request->schedule_template_id);
			$activation_date = $template->activation_date;
			$deactivation_date = $template->deactivation_date;
			if($activation_date == null)
			{
				// if($deactivation_date == null)
				// {
				// 	return prepareResult(false,getLangByLabelGroups('Schedule','message_schedule_not_activated'), ['Schedule has not been activated since created'],config('httpcodes.not_found'));
				// }
				// $activation_date = \Carbon\Carbon::parse(User::find($request->patient_id)->created_at)->format('Y-m-d');
				$activation_date = date('Y-m-d',strtotime('+1 days'));
			}
			if($deactivation_date == null || $deactivation_date >= date('Y-m-d'))
			{
				$deactivation_date = date('Y-m-d');
			}
			$patient_ids = [];
			if(!empty($request->patient_id))
			{
				$patient_ids[] = $request->patient_id;
			}
			else
			{
				$patients = Schedule::where('schedule_template_id', $request->schedule_template_id)
				->where('patient_id','!=',null)
				->get(['patient_id']);

				foreach ($patients as $key => $value) {
					$patient_ids[] = $value->patient_id;
				}
			}

			$data = [];
			foreach ($patient_ids as $key => $patient_id) {
				// $patient_name = User::find($patient_id)->name;
				$data[$patient_id]['patient_assigned_hours'] = (AgencyWeeklyHour::where('user_id',$patient_id)
					->sum('assigned_hours'))*60;
				$data[$patient_id]['patient_completed_hours'] = (Schedule::select([
					\DB::raw('SUM(scheduled_work_duration) + SUM(extra_work_duration) + SUM(ob_work_duration) + SUM(emergency_work_duration)  + SUM(vacation_duration) as completed_hours')
				])
				->where('schedule_template_id', $request->schedule_template_id)
				->where('patient_id', $request->patient_id) 
				->where('shift_date', '>=',$activation_date)
				->where('shift_date', '<=',$deactivation_date) 
				->where('leave_applied', 0) 
				->where('status', 1)      
				->first()->completed_hours)*60;

				$data[$patient_id]['patient_planned_hours'] = (Schedule::select([
					\DB::raw('SUM(scheduled_work_duration) + SUM(extra_work_duration) + SUM(ob_work_duration) + SUM(emergency_work_duration)  + SUM(vacation_duration) as planned_hours')
				])
				->where('schedule_template_id', $request->schedule_template_id)
				->where('patient_id', $request->patient_id)
				->where('is_active', 1) 
				->where('leave_applied', 0)
				->where('shift_date', '>',date('Y-m-d'))       
				->first()->planned_hours)*60;
				
				$data[$patient_id]['ips'] = PatientImplementationPlan::where('user_id',$patient_id)
				->where('is_latest_entry',1)
				->where('status',1)
				->get(['id','title','start_date','end_date']);
			}
			
			return prepareResult(true, getLangByLabelGroups('Schedule','message_patients_data') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}
}
