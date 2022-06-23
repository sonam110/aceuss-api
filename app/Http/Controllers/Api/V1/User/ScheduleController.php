<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Validator;
use Auth;
use DB;
use App\Models\User;
use App\Models\CompanyWorkShift;
use Exception;
use App\Models\Activity;
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
			$query = Schedule::orderBy('created_at', 'DESC')->with('user:id,name,gender','scheduleDates:id,group_id,shift_date,shift_start_time,shift_end_time')->groupBy('group_id');

			if(!empty($request->shift_id))
			{
				$query->where('shift_id' ,$request->shift_id);
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

	public function store(Request $request){
		DB::beginTransaction();
		try 
		{
			$validator = Validator::make($request->all(),[   
				// 'schedule_template_id' => 'required|exists:schedule_templates,id' ,
				'user_id' => 'required|exists:users,id'
			],
			[   
				// 'schedule_template_id' =>  getLangByLabelGroups('Schedule','message_schedule_template_id'),
				'user_id' =>  getLangByLabelGroups('Schedule','message_user_id'),
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$shift_name 		= null;
			$shift_color 		= null;

			if(!empty($request->shift_id))
			{
				$shift = CompanyWorkShift::find($request->shift_id);
				if (!is_object($shift)) {
					return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
				}
				$shift_name 		= $shift->shift_name;
				$shift_color 		= $shift->shift_color;
			}
			

			if($request->is_range == 1)
			{
				$start_date = $request->shift_dates[0];
				$end_date = $request->shift_dates[1];
				$is_repeat = 1;
				$every_week = $request->every_week;
				$week_days = $request->week_days;

				$from = \Carbon\Carbon::parse($start_date);
				$to =   (!empty($end_date)) ? \Carbon\Carbon::parse($end_date) : \Carbon\Carbon::parse($start_date);
				$start_from = $from->format('Y-m-d');
				$end_to = $to->format('Y-m-d');

				$dates = [];

				if($request->is_repeat == 1)
				{
					$dates = calculateDates($start_date,$end_date,$every_week,$week_days);
				}
				else
				{
					$date1 = strtotime($start_date);
					$date2 = strtotime($end_date);
					for ($currentDate=$date1; $currentDate<=$date2; $currentDate += (86400)) 
					{                                   
						$dates[] = date('Y-m-d', $currentDate);
					}
				}         
			}
			else
			{
				$dates = $request->shift_dates;
			}
			$start_time = $request->shift_start_time;
			$end_time = $request->shift_end_time;
			$emergency_start_time = $request->emergency_start_time;
			$emergency_end_time = $request->emergency_end_time;
			$shedule_ids = [];
			$assignedWork_id = null;
			$assignedWork = null;
			if(!empty(User::find($request->user_id)->assignedWork))
			{
				$assignedWork = User::find($request->user_id)->assignedWork;
				$assignedWork_id = $assignedWork->id;
			}
			$group_id = generateRandomNumber();
			$schedule_ids = [];
			foreach($dates as $key=>$shift_date)
			{
				$date = date('Y-m-d',strtotime($shift_date));

				$startEndTime = getStartEndTime($start_time, $end_time, $date);
				$schedule = new Schedule;
				$schedule->top_most_parent_id = $request->top_most_parent_id;
				$schedule->user_id = $request->user_id;
				$schedule->patient_id = $request->patient_id;
				$schedule->shift_id = $request->shift_id;
				$schedule->parent_id = $request->parent_id;
				$schedule->created_by = Auth::id();
				$schedule->slot_assigned_to = $request->slot_assigned_to;
				$schedule->employee_assigned_working_hour_id = $assignedWork_id;
				$schedule->schedule_template_id = $request->schedule_template_id;
				$schedule->schedule_type = $request->schedule_type;
				$schedule->shift_date = $date;
				$schedule->group_id = $group_id;
				$schedule->shift_name = $shift_name;
				$schedule->shift_color = $shift_color;
				$schedule->shift_start_time = $startEndTime['start_time'];
				$schedule->shift_end_time = $startEndTime['end_time'];
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
				$schedule->scheduled_work_duration = $request->scheduled_work_duration;
				$schedule->extra_work_duration = $request->extra_work_duration;
				$schedule->status = $request->status ? $request->status :0;
				$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
				$schedule->save();
				$schedule_ids[] = $schedule->id;
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

	public function update(Request $request,$id){
		DB::beginTransaction();
		try 
		{
			// $validator = Validator::make($request->all(),[   
			// 	'shift_id' => 'required|exists:company_work_shifts,id' 
			// ],
			// [   
			// 	'shift_id' =>  getLangByLabelGroups('Schedule','message_shift_id')
			// ]);
			// if ($validator->fails()) {
			// 	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			// }

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

			$schedule->shift_id 		= $request->shift_id;
			$schedule->user_id 			= $request->user_id ? $request->user_id : $schedule->user_id;
			$schedule->parent_id 		= $request->parent_id;
			$schedule->shift_name 		= $shift_name;
			$schedule->shift_start_time = $startEndTime['start_time'];
			$schedule->shift_end_time 	= $startEndTime['end_time'];
			$schedule->patient_id 		= $request->patient_id;
			$schedule->shift_color 		= $shift_color;
			$schedule->shift_date 		= $date;
			$schedule->leave_applied 	= $request->leave_applied ? $request->leave_applied :0;
			$schedule->leave_approved 	= $request->leave_approved ? $request->leave_approved :0;
			$schedule->status 			= $request->status ? $request->status :0;
			$schedule->entry_mode 		= $request->entry_mode ? $request->entry_mode :'Web';
			$schedule->created_by 		= Auth::id();
			$schedule->employee_assigned_working_hour_id = $request->employee_assigned_working_hour_id;
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


	
}
