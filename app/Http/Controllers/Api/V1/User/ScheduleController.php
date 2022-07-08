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

			$shedule_ids = [];
			$group_id = generateRandomNumber();
			$assignedWork_id = null;
			$assignedWork = null;
			if(!empty(User::find($request->user_id)->assignedWork))
			{
				$assignedWork = User::find($request->user_id)->assignedWork;
				$assignedWork_id = $assignedWork->id;
			}
			foreach ($request->schedules as $key => $value) 
			{

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
					$schedule->user_id = $request->user_id;
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
					$schedule->status = $request->status ? $request->status :0;
					$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
					$schedule->save();

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
            if(!empty())
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
			$query = $query->get(['id','shift_id','schedule_template_id','schedule_type','patient_id','user_id','shift_date','shift_start_time','shift_end_time','group_id']);
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

			$filterData = Schedule::join('users', function ($join) {
				    $join->on('schedules.user_id', '=', 'users.id');
			})
			->where('schedules.top_most_parent_id', auth()->user()->top_most_parent_id)
			->orderBy('schedules.created_at','DESC')
			->where('schedules.is_active',1)
			->where('schedules.shift_date',">=" ,$request->shift_start_date)
			->where('schedules.shift_date',"<=" ,$request->shift_end_date)
			->withoutGlobalScope('top_most_parent_id');

			if(!empty($request->employee_type))
			{
				$filterData->where('users.employee_type' ,$request->employee_type);
			}
			if(!empty($request->user_name))
			{
				$filterData->where('users.name','like','%'.$request->name.'%');
			}
			if(!empty($request->schedule_template_id))
			{
				$filterData->where('schedules.schedule_template_id' ,$request->schedule_template_id);
			}
			if(!empty($request->user_ids))
			{
				$filterData->whereIn('schedules.user_id' ,$request->user_ids);
			}
			
			$filterData = $filterData->get(['user_id']);

			foreach ($filterData as $key => $value) {
				$users_ids[] = $value->user_id;
			}

			$users_ids = array_unique($users_ids);

			$users = User::whereIn('id',$users_ids);
			if($request->all_employee == true)
			{
				$users = User::where('top_most_parent_id',auth()->user()->top_most_parent_id)
				->where('status','1');
				if(empty($request->user_type_id))
				{
					$users->where('user_type_id' ,3);
				}
			}
			if(!empty($request->user_type_id))
			{
				$users->where('user_type_id' ,$request->user_type_id);
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

					$schedules = $schedules->get();
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
					
					$schedules = $schedules->get();
					$value->schedules = $schedules;
				}
			}			

			return prepareResult(true,"Schedule list",$users,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}

	}
}
