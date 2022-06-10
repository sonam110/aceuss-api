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
			$query = Schedule::orderBy('created_at', 'DESC')->with('user:id,name,gender','scheduleDates:group_id,shift_date')->groupBy('group_id');

			if(!empty($request->shift_id))
			{
			    $query->where('shift_id' ,$request->shift_id);
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
			    $query->where('shift_date' ,$request->shift_date);
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
				// 'shift_id' => 'required|exists:company_work_shifts,id' ,
				'user_id' => 'required|exists:users,id'
			],
			[   
				// 'shift_id' =>  getLangByLabelGroups('Schedule','message_shift_id'),
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
				$everyWeek = $request->every_week;
				$week_days = $request->week_days;

				$from = \Carbon\Carbon::parse($start_date);
				$to =   (!empty($end_date)) ? \Carbon\Carbon::parse($end_date) : \Carbon\Carbon::parse($start_date);
				$start_from = $from->format('Y-m-d');
				$end_to = $to->format('Y-m-d');

				$dates = [];

				if($request->is_repeat == 1)
				{
					for($w = $from; $w->lte($to); $w->addWeeks($everyWeek)) {
						$date = \Carbon\Carbon::parse($w);
						$startWeek = $w->startOfWeek()->format('Y-m-d');
						$weekNumber = $date->weekNumberInMonth;
						$start = \Carbon\Carbon::createFromFormat("Y-m-d", $startWeek);
						$end = $start->copy()->endOfWeek()->format('Y-m-d');
						for($p = $start; $p->lte($end); $p->addDays()) {
							if(strtotime($start_from) <= strtotime($p) && strtotime($end_to) >= strtotime($p) ) {
								if(in_array($p->dayOfWeek, $week_days)){
									array_push($dates, $p->copy()->format('Y-m-d'));
								}
							}
						}
					}
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

			$shedule_ids = [];
			foreach($request->user_id as $key=>$value)
			{
				$group_id = generateRandomNumber();
				foreach($dates as $key=>$shift_date)
				{
					$date = date('Y-m-d',strtotime($shift_date));

					$schedule = new Schedule;
					$schedule->shift_id 		= $request->shift_id;
					$schedule->user_id 			= $value;
					$schedule->parent_id 		= $request->parent_id;
					$schedule->shift_start_time = $request->shift_start_time;
					$schedule->shift_end_time 	= $request->shift_end_time;
					$schedule->patient_id 		= $request->patient_id;
					$schedule->group_id 		= $group_id;
					$schedule->shift_name 		= $shift_name;
					$schedule->shift_color 		= $shift_color;
					$schedule->shift_date 		= $date;
					$schedule->leave_applied 	= $request->leave_applied? $request->leave_applied :0;
					$schedule->leave_approved 	= $request->leave_approved? $request->leave_approved :0;
					$schedule->status 			= $request->status? $request->status :0;
					$schedule->entry_mode 		=  $request->entry_mode ? $request->entry_mode :'Web';
					$schedule->created_by 		= Auth::id();
					$schedule->save();
					$schedule_ids[] = $schedule->id;
				}
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

			$date = date('Y-m-d',strtotime($request->shift_date));

			$schedule->shift_id 		= $request->shift_id;
			$schedule->user_id 			= $request->user_id ? $request->user_id : $schedule->user_id;
			$schedule->parent_id 		= $request->parent_id;
			$schedule->shift_name 		= $shift_name;
			$schedule->shift_start_time = $request->shift_start_time;
			$schedule->shift_end_time 	= $request->shift_end_time;
			$schedule->patient_id 		= $request->patient_id;
			$schedule->shift_color 		= $shift_color;
			$schedule->shift_date 		= $request->shift_date ? $date : $schedule->shift_date;
			$schedule->leave_applied 	= $request->leave_applied ? $request->leave_applied :0;
			$schedule->leave_approved 	= $request->leave_approved ? $request->leave_approved :0;
			$schedule->status 			= $request->status ? $request->status :0;
			$schedule->entry_mode 		= $request->entry_mode ? $request->entry_mode :'Web';
			$schedule->created_by 		= Auth::id();
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
			$schedule= Schedule::with('user:id,name,gender')->groupBy('group_id')->where('id',$id)->first();
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
