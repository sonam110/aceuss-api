<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use App\Models\OVHour;
use App\Models\Stampling;
use App\Models\EmailTemplate;
use App\Models\ScheduleTemplate;
use App\Models\CompanyWorkShift;
use Exception;
use PDF;
use Validator;
use Auth;
use DB;
use Str;

class StamplingController extends Controller
{
	public function __construct()
	{

		// $this->middleware('permission:stampling-browse',['except' => ['show']]);
		// $this->middleware('permission:stampling-add', ['only' => ['store']]);
		// $this->middleware('permission:stampling-edit', ['only' => ['update']]);
		// $this->middleware('permission:stampling-read', ['only' => ['show']]);
		// $this->middleware('permission:stampling-delete', ['only' => ['destroy']]);

	}
	

	public function stamplings(Request $request)
	{
		try 
		{
			$child_ids = [Auth::id()];
			foreach (Auth::user()->childs as $key => $value) {
			    $child_ids[] = $value->id;
			}

			$query = Stampling::orderBy('created_at', 'DESC')->whereIn('user_id',$child_ids)->with('user:id,name,gender,branch_id,user_type_id','user.userType:id,name','user.branch:id,branch_id,company_type_id','schedule');

			// $query = Stampling::orderBy('created_at', 'DESC')->with('user:id,name,gender,branch_id,user_type_id','user.userType:id,name','user.branch');
			if(!empty($request->user_id))
			{
			    $query->where('user_id', $request->user_id);
			}
			if(!empty($request->date))
			{
			    $query->where('date' ,$request->date);
			}
			if(!empty($request->in_time))
			{
			    $query->where('in_time',">=" ,$request->in_time);
			}
			if(!empty($request->out_time))
			{
			    $query->where('out_time',"<=" ,$request->out_time);
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
				return prepareResult(true,"Stampling list",$pagination,config('httpcodes.success'));
			}
			else
			{
				$query = $query->get();
			}

			return prepareResult(true,"Stampling list",$query,config('httpcodes.success'));
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
				'type' => 'required',
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}
			
			$user = Auth::User();
			$relaxation_time = companySetting($user->top_most_parent_id)['relaxation_time'];
			if(empty($relaxation_time))
			{
				$relaxation_time = 15;
			}
			$date = date('Y-m-d');
			if($request->type == "IN")
			{
				$in_time = date('Y-m-d H:i:s');
				if(!empty($request->schedule_id))
				{
					$schedule_id = $request->schedule_id;
					$schedule = Schedule::find($schedule_id);
					$stampling_type = 'scheduled';
					$punch_in_time = strtotime($in_time);
					$timeWithRelaxation = timeWithRelaxation($schedule->shift_start_time,$relaxation_time);

					if(($punch_in_time >= $timeWithRelaxation['before']) && ($punch_in_time <= $timeWithRelaxation['after']))
					{
						$in_time = $schedule->shift_start_time;
					}

					if(($punch_in_time < $timeWithRelaxation['before']) && ($request->punch_at_scheduled == true))
					{
						$in_time = $schedule->shift_start_time;
					}

					$rest_start_time = $schedule->rest_start_time;
					$rest_end_time = $schedule->rest_end_time;
				}
				else
				{
					$validator = Validator::make($request->all(),[  
						'expected_out_time'=>'required'
					]);
					if ($validator->fails()) {
						return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
					}

					$rest_start_time = $request->rest_start_time;
					$rest_end_time = $request->rest_end_time;

					$stampling_type = 'walkin';
					$result = scheduleWorkCalculation($date,$in_time,$request->expected_out_time,'extra',null,$rest_start_time,$rest_end_time);

					$schedule 							= new Schedule;
					$schedule->top_most_parent_id 		= $user->top_most_parent_id;
					$schedule->user_id 					= Auth::id();
					$schedule->patient_id 				= NULL;
					$schedule->shift_id 				= NULL;
					$schedule->parent_id 				= NULL;
					$schedule->created_by 				= Auth::id();
					$schedule->slot_assigned_to 		= null;
					$schedule->employee_assigned_working_hour_id= NULL;
					$schedule->schedule_template_id 	= ScheduleTemplate::where('status','1')->first()->id;
					$schedule->schedule_type 			= 'extra';
					$schedule->shift_date 				= $date;
					$schedule->group_id 				= generateRandomNumber();
					$schedule->shift_name 				= null;
					$schedule->shift_color 				= null;
					$schedule->shift_start_time 		= $in_time;
					$schedule->shift_end_time 			= $request->expected_out_time;
					$schedule->rest_start_time 			= $rest_start_time;
					$schedule->rest_end_time 			= $rest_end_time;
					$schedule->leave_applied 			= 0;
					$schedule->leave_group_id 			= null;
					$schedule->leave_type 				= null;
					$schedule->leave_reason 			= null;
					$schedule->leave_approved 			= 0;
					$schedule->leave_approved_by		= null;
					$schedule->leave_approved_date_time = null;
					$schedule->leave_notified_to		= null;
					$schedule->notified_group 			= null;
					$schedule->is_active 				= 1;
					$schedule->scheduled_work_duration 	= $result['scheduled_work_duration'];
					$schedule->extra_work_duration 		= $result['extra_work_duration'];
					$schedule->emergency_work_duration 	= $result['emergency_work_duration'];
					$schedule->ob_work_duration 		= $result['ob_work_duration'];
					$schedule->ob_type 					= $result['ob_type'];
					$schedule->status 					= $request->status ? $request->status :0;
					$schedule->entry_mode 				= $request->entry_mode?$request->entry_mode:'Web';
					$schedule->save();

					$schedule_id = $schedule->id;
				}

				$scheduled_hours_rate = $user->contract_value;
				$companySetting = companySetting($user->top_most_parent_id);
				$extra_hours_rate = $companySetting ? $companySetting['extra_hour_rate'] : 0;
				$ob_hours_rate = $companySetting ? $companySetting['ob_hour_rate'] : 0;

				$stampling = new Stampling;
				$stampling->schedule_id 				= $schedule_id;
				$stampling->user_id 					= Auth::id();
				$stampling->date 						= date('Y-m-d');
				$stampling->stampling_type 				= $stampling_type;
				$stampling->in_time 					= $in_time;
				$schedule->rest_start_time 				= $rest_start_time;
				$schedule->rest_end_time 				= $rest_end_time;
				$stampling->in_location 				= json_encode($request->location);
				$stampling->reason_for_early_in 		= $request->reason_for_early_in;
				$stampling->reason_for_late_in 			= $request->reason_for_late_in;
				$stampling->is_extra_hours_approved 	= "0";
				$stampling->scheduled_hours_rate 		= $scheduled_hours_rate;
				$stampling->extra_hours_rate 			= $extra_hours_rate;
				$stampling->ob_hours_rate 				= $ob_hours_rate;
				$stampling->total_schedule_hours 		= "0";
				$stampling->total_extra_hours 			= "0";
				$stampling->total_ob_hours 				= "0";
				$stampling->entry_mode 					= $request->entry_mode ? $request->entry_mode :'Web';
				$stampling->save();

				$upcomingSchedule = Schedule::where('user_id',Auth::id())->where('shift_start_time','>',$schedule->shift_start_time)->where('is_active',1)->orderBy('shift_start_time','asc')->first();
			}
			elseif($request->type == "OUT")
			{
				$validator = Validator::make($request->all(),[  
					'punchin_id'=>'required'
				]);
				if ($validator->fails()) {
					return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
				}
				$stampling = Stampling::find($request->punchin_id);
				if (!is_object($stampling)) 
				{
	                return prepareResult(false,getLangByLabelGroups('Stampling','message_id_not_found'), ['Not Logged In'],config('httpcodes.not_found'));
	            }
	            $in_time = $stampling->in_time;
	            $out_time = date('Y-m-d H:i:s');
	            
	            $schedule = Schedule::find($stampling->schedule_id);

				$punch_out_time = strtotime($out_time);
				$timeWithRelaxation = timeWithRelaxation($schedule->shift_end_time,$relaxation_time);

				if(($punch_out_time >= $timeWithRelaxation['before']) && ($punch_out_time <= $timeWithRelaxation['after']))
				{
					$out_time = $schedule->shift_end_time;
				}

				if(($punch_out_time > $timeWithRelaxation['after']) && ($request->punch_at_scheduled == true))
				{
					$out_time = $schedule->shift_end_time;
				}

				$rest_start_time = $stampling->rest_start_time;
				$rest_end_time = $stampling->rest_end_time;


	            

				// return getOVHours('2022-06-22 19:30:00','2022-06-23 08:10:00','2022-06-22 20:30','2022-06-23 05:30');

				// $data = basicScheduleTimeCalculation($schedule->shift_start_time,$schedule->shift_end_time,$relaxation_time,$in_time,$out_time, $ob_start_time, $ob_end_time, false);

				// $data = basicScheduleTimeCalculation('2022-06-22 22:20:00','2022-06-23 06:50:00',$relaxation_time,'2022-06-22 19:30:00','2022-06-23 08:10:00', '2022-06-22 11:30', '2022-06-23 05:30', true);
				// return $data;
				$ob = getObDuration($date,$in_time,$out_time,$rest_start_time,$rest_end_time);
				$ob_duration = $ob['duration'];

				$rest_duration = 0;

				if(($rest_start_time != null) && ($rest_end_time != null) && ($rest_end_time < $out_time))
				{
				    $rest_duration = timeDifference($rest_start_time,$rest_end_time);
				}

				$scheduled_duration = timeDifference($schedule->shift_start_time,$schedule->shift_end_time)-$rest_duration;
				$total_worked_duration = timeDifference($in_time,$out_time) - $rest_duration;
				$countable_scheduled_duration = $total_worked_duration - $ob_duration;
				$extra_duration =  0;
				if($countable_scheduled_duration > $scheduled_duration)
				{
					$extra_duration =  $countable_scheduled_duration - $scheduled_duration;
				}

				$total_schedule_hours = $countable_scheduled_duration/60;
				$total_ob_hours = $ob_duration/60;
				$total_extra_hours = $extra_duration/60;

				$working_percent = calculatePercentage($total_worked_duration, $scheduled_duration);

				$stampling->out_time 				= $out_time;
				$stampling->out_location 			= json_encode($request->location);
				$stampling->reason_for_early_out 	= $request->reason_for_early_out;
				$stampling->reason_for_late_out 	= $request->reason_for_late_out;
				$stampling->is_extra_hours_approved = "0";
				$stampling->total_schedule_hours 	= $total_schedule_hours;
				$stampling->total_extra_hours		= $total_extra_hours;
				$stampling->total_ob_hours 			= $total_ob_hours;
				$stampling->ob_type 				= $ob['type'];
				$stampling->working_percent 		= $working_percent;
            	$stampling->logout_by               = 'self';
				$stampling->entry_mode 				= $request->entry_mode ? $request->entry_mode : 'Web';
				$stampling->save();

				$upcomingSchedule = Schedule::where('user_id',Auth::id())->where('shift_start_time','>',$stampling->out_time)->where('is_active',1)->orderBy('shift_start_time','asc')->first();
			}
			else
			{
				return prepareResult(true,getLangByLabelGroups('Stampling','message_Invalid_Type') ,['type invalid'], config('httpcodes.success'));
			}

			$stampling = Stampling::where('id',$stampling->id)->with('user:id,name,gender,branch_id,user_type_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id')->first();
			$stampling->upcoming_schedule = $upcomingSchedule;

			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Stampling','message_create') ,$stampling, config('httpcodes.success'));
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
			$stampling= Stampling::find($id);
			if (!is_object($stampling)) {
				return prepareResult(false,getLangByLabelGroups('Stampling','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			$stampling->delete();
			return prepareResult(true,getLangByLabelGroups('Stampling','message_delete') ,[], config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}

	public function show($id)
	{
		try 
		{
			$stampling = Stampling::where('id',$id)->with('user:id,name,gender,branch_id,user_type_id','user.userType:id,name','user.branch:id,branch_id,name,company_type_id')->first();
			if (!is_object($stampling)) {
				return prepareResult(false,getLangByLabelGroups('Stampling','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,'View Stampling' ,$stampling, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

	public function stampInData()
	{
		try {

    		$query = Stampling::where('user_id', Auth::id())->where('out_time',null)->orderBy('created_at','desc')->get(['id','in_time','in_location']);

    		return prepareResult(true,"Stampling list",$query,config('httpcodes.success'));
    	}
    	catch(Exception $exception) {
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

    	}
	}

	public function stamplingReports(Request $request)
	{
		try 
		{
			$stamplings = Stampling::where('user_id',$request->user_id)->where('out_time','!=',null)->get(['id','schedule_id','in_time','out_time','total_ob_hours','stampling_type','date']);
			foreach ($stamplings as $key => $value) {
				$schedule = Schedule::find($value->schedule_id);
				$scheduled_duration = timeDifference($schedule->shift_start_time,$schedule->shift_end_time);
				$total_worked_duration = timeDifference($value->in_time,$value->out_time);
				$extra_work = 0;
				$remaining_work = 0;
				if($total_worked_duration > $scheduled_duration)
				{
					$extra_work = $total_worked_duration - $scheduled_duration;
				}
				else
				{
					$remaining_work = -$total_worked_duration + $scheduled_duration;
				}

				$value->shift_start_time = $schedule->shift_start_time;
				$value->shift_end_time = $schedule->shift_end_time;
				$value->shift_hours = $scheduled_duration;
				$value->stampling_duration = $total_worked_duration;
				$value->extra_work = $extra_work;
				if($schedule->schedule_type == 'emergency')
				{
					$value->emergency_work_duration = $schedule->emergency_work_duration - $value->total_ob_hours;
				}
				else
				{
					$value->emergency_work_duration = 0;
				}
				// $value->remaining_work = number_format($remaining_work,2);
			}
			return prepareResult(true,  getLangByLabelGroups('Schedule','message_employee_hours'),$stamplings, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}
}
