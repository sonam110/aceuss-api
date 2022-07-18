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
use App\Exports\EmployeeWorkingHourExport;

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
			$query = Schedule::orderBy('created_at', 'DESC')->with('user:id,name,gender')->where('is_active',1);

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
				return prepareResult(true,"Schedule list",$pagination,config('httpcodes.success'));
			}
			else
			{
				$query = $query->get();
			}

			return prepareResult(true,"Schedule list",$query,config('httpcodes.success'));
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
				'schedule_template_id' => 'required|exists:schedule_templates,id' ,
				'user_id' => 'required|exists:users,id'
			],
			[   
				'schedule_template_id' =>  getLangByLabelGroups('Schedule','message_schedule_template_id'),
				'user_id' =>  getLangByLabelGroups('Schedule','message_user_id'),
			]);
			if ($validator->fails()) 
			{
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}
			$user = User::find($request->user_id);
			$shedule_ids = [];
			$group_id = generateRandomNumber();
			$assignedWork_id = null;
			$assignedWork = null;
			if(!empty($user->assignedWork))
			{
				$assignedWork = $user->assignedWork;
				$assignedWork_id = $assignedWork->id;
			}
			foreach ($request->schedules as $key => $value) 
			{
				if($key == 0)
				{
					$start_date = $value['date'];
				}

				$date = date('Y-m-d',strtotime($value['date']));
				foreach($value['shifts'] as $key=>$shift)
				{
					$shift_name 		= null;
					$shift_color 		= null;
					$shift_type 		= null;
					if(!empty($shift['shift_id']))
					{
						$c_shift = CompanyWorkShift::find($shift['shift_id']);
						if (!is_object($c_shift)) {
							return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
						}
						$shift_name 		= $c_shift->shift_name;
						$shift_color 		= $c_shift->shift_color;
						$shift_type 		= $c_shift->shift_type;

					}

					$shift_start_time = $shift['shift_start_time'];
					$shift_end_time = $shift['shift_end_time'];

					$result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,$shift['schedule_type'],$shift_type);

					$schedule = new Schedule;
					$schedule->user_id = $user->id;
					$schedule->patient_id = $request->patient_id;
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

					if(!empty($request->patient_id))
					{
						$patientAssignedHours = AgencyWeeklyHour::where('user_id',$request->patient_id)
						->where('start_date','>=' ,$schedule->shift_date)
						->where('end_date','<=',$schedule->shift_date)
						->orderBy('id','desc')->first();
						if(empty($patientAssignedHours))
						{
							$patientAssignedHours = AgencyWeeklyHour::where('user_id',$request->patient_id)->orderBy('id','desc')->first();
						}
						$scheduledHours = $patientAssignedHours->scheduled_hours + $schedule->scheduled_work_duration + $schedule->emergency_work_duration + $schedule->ob_work_duration + $schedule->extra_work_duration;
						$patientAssignedHours->update(['scheduled_hours'=>$scheduledHours]);

					}
					$schedule_ids[] = $schedule->id;
				}
			}
			$datesData = UserScheduledDate::where('emp_id',$request->user_id)
			->where('schedule_template_id',$request->schedule_template_id)
			->where('start_date',"<=" ,$start_date)
			->where('end_date',">=" ,$start_date)
			->first();
			if(empty($datesData))
			{
				$datesData = new UserScheduledDate;
				$datesData->schedule_template_id = $request->schedule_template_id;
				$datesData->emp_id = $request->user_id;
				$datesData->start_date = $start_date;
				$datesData->end_date = date('Y-m-d', strtotime('+27 days',strtotime($start_date)));
				$datesData->save();

				$userUpdate = User::find($request->user_id)->update(['schedule_start_date'=>$start_date]);
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

	public function update(Request $request,$id){
		DB::beginTransaction();
		try 
		{
			$schedule = Schedule::find($id);
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), ['schedule not found'],config('httpcodes.not_found'));
			}

			$shift_name 		= $schedule->shift_name;
			$shift_color 		= $schedule->shift_color;

			if(!empty($request->shift_id))
			{
				$shift = CompanyWorkShift::find($request->shift_id);
				if (!is_object($shift)) {
					return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
				}

				$shift_name 		= $shift->shift_name;
				$shift_color 		= $shift->shift_color;
			}
			$shift_date = date('Y-m-d', strtotime($request->shift_date));
			$date = !empty($request->shift_date) ? $shift_date : $schedule->shift_date;
			
			$startEndTime = getStartEndTime($request->shift_start_time, $request->shift_end_time, $date);
			$shift_start_time = $startEndTime['start_time'];
			$shift_end_time = $startEndTime['end_time'];
			$assignedWork_id = null;
			$assignedWork = null;
			if(!empty(User::find($schedule->user_id)->assignedWork))
			{
				$assignedWork = User::find($schedule->user_id)->assignedWork;
				$assignedWork_id = $assignedWork->id;
			}

			$result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,$request->schedule_type);

			$schedule->top_most_parent_id = $request->top_most_parent_id;
			$schedule->user_id = $schedule->user_id;
			$schedule->patient_id = $request->patient_id;
			$schedule->branch_id = $request->branch_id;
			$schedule->shift_id = $request->shift_id;
			$schedule->parent_id = $request->parent_id;
			$schedule->created_by = Auth::id();
			$schedule->slot_assigned_to = $request->slot_assigned_to;
			$schedule->employee_assigned_working_hour_id = $assignedWork_id;
			$schedule->schedule_template_id = $request->schedule_template_id;
			$schedule->schedule_type = $request->schedule_type;
			$schedule->shift_date = $date;
			$schedule->group_id = $schedule->group_id;
			$schedule->shift_name = $shift_name;
			$schedule->shift_color = $shift_color;
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
			$schedule->ob_work_duration = $result['ob_work_duration'];
			$schedule->ob_type = $result['ob_type'];
			$schedule->status = $request->status ? $request->status :0;
			$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
			$schedule->save();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_update') ,$schedule, config('httpcodes.success'));
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
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
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
			$schedule= Schedule::with('user:id,name,gender','scheduleDates:id,group_id,shift_date,shift_start_time,shift_end_time')->groupBy('group_id')->where('id',$id)->first();
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,'View Schedule' ,$schedule, config('httpcodes.success'));
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
			return prepareResult(true,"Schedule list",$query,config('httpcodes.success'));
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
			$query = Schedule::orderBy('created_at', 'DESC')->where('is_active',1)->groupBy('user_id');

			if(!empty($request->user_ids))
			{
				$query->whereIn('user_id', $request->user_ids);
			}
            // if(!empty())
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

			return prepareResult(true,"Schedule Report",$data,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function schedulesDates(Request $request)
	{
		try 
		{
			$query = Schedule::orderBy('created_at', 'DESC')->with('user:id,name,gender')->where('is_active',1);

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
			return prepareResult(true,"Schedule list",$data,config('httpcodes.success'));
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

			$users = User::where('top_most_parent_id',auth()->user()->top_most_parent_id)->where('status','1');
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
				return prepareResult(true,"Schedule list",$pagination,config('httpcodes.success'));
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

			return prepareResult(true,"Schedule list",$users,config('httpcodes.success'));
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
		return prepareResult(true,"Schedule",$returnObj,config('httpcodes.success'));
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
					$query = Schedule::select([
						\DB::raw('SUM(scheduled_work_duration) as regular_hours'),
						\DB::raw('SUM(extra_work_duration) as extra_hours'),
						\DB::raw('SUM(ob_work_duration) as obe_hours'),
						\DB::raw('SUM(emergency_work_duration) as emergency_hours'),
						\DB::raw('SUM(vacation_duration) as vacation_hours')
					]);
					$query->whereDate('shift_date', $shift_date);
					$query->where('patient_id', $request->patient_id); 
					$query->where('status', 1);        
					$result = $query->first();
					if(!empty($result))
					{
						$total_hours = $result->regular_hours + $result->extra_hours + $result->obe_hours + $result->emergency_hours + $result->vacation_hours;
						$data['date'][] = ["date" => $shift_date,"hours" => $total_hours];
					}
				}
			}
			return prepareResult(true, 'Patient Hours.' ,$data, config('httpcodes.success'));
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

				foreach ($schedules as $key => $value) {
					$value->hours = $value->scheduled_work_duration + $value->emergency_work_duration + $value->ob_work_duration + $value->extra_work_duration;
				}

				$data[] = ["date" => $shift_date,"schedules" => $schedules];
			}
			return prepareResult(true, 'Employee Hours.' ,$data, config('httpcodes.success'));
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
			foreach ($ids as $key => $id) {
				$schedule= Schedule::find($id);
				if (!is_object($schedule)) {
					return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
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
					$schedule->update(['status'=>1,'approved_by_company'=>1,'verified_by_employee'=>1]);
				}
			}
			
			$data = Schedule::whereIn('id',$ids)->get();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_delete') ,$data, config('httpcodes.success'));
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
			foreach ($ids as $key => $id) {
				$schedule= Schedule::find($id);
				if (!is_object($schedule)) {
					return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
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
				$schedule->update(['status'=>1,'verified_by_employee'=>1]);
				$user = User::find(Auth::user()->top_most_parent_id);
				//----notify-company-schedule-verified----//
				$data_id =  $id;
				$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-verified')->first();
				$variable_data = [
					'{{name}}'  => $user->name,
					'{{schedule_title}}'=>$schedule->title,
					'{{date}}' => $schedule->shift_date,
					'{{start_time}}'=> $schedule->shift_start_time,
					'{{end_time}}'=> $schedule->shift_end_time,
					'{{verified_by}}'=> Auth::user()->name
				];
				actionNotification($user,$data_id,$notification_template,$variable_data);
	            //--------------------------------------//
			}
			$data = Schedule::whereIn('id',$ids)->get();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_delete') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}


	public function employeeWorkingHourExport(Request $request)
	{
		$rand = rand(0,1000);
		$dates = [$request->start_date,$request->end_date];
        $excel = Excel::store(new EmployeeWorkingHourExport($dates), 'export/schedule/'.$rand.'.xlsx' , 'export_path');
        
        return prepareResult(true,getLangByLabelGroups('schedule','message_employee_working_hour_export') ,['url' => env('APP_URL').'public/export/schedule'.$rand.'.xlsx'], config('httpcodes.success'));
	}
}
