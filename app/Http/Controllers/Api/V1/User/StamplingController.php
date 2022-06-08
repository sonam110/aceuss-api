<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use App\Models\OVHour;
use App\Models\Stampling;
use App\Models\EmailTemplate;
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
			$query = Stampling::orderBy('created_at', 'DESC')->with('user:id,name');
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

	public function store(Request $request){
		DB::beginTransaction();
		try 
		{
			$validator = Validator::make($request->all(),[   
				'type' => 'required',
			],
			[   
				'type' =>  getLangByLabelGroups('Stampling','message_type'),
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$user = Auth::User();
			$date = date('Y-m-d');
			if($request->type == "IN")
			{
				$schedule = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->first();
				if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
				{
				    $schedule_id = $schedule->id;
				}
				else
				{
					$schedule_id = null;
				}
				if(Stampling::where('date',$date)->where('user_id',Auth::id())->count() > 0)
				{
					$stampling = Stampling::where('date',$date)->where('user_id',Auth::id())->first();
				}
				else
				{
					$stampling = new Stampling;
				}
				$stampling->schedule_id 				= $schedule_id;
				$stampling->user_id 					= Auth::id();
				$stampling->date 						= date('Y-m-d');
				$stampling->in_time 					= date('Y-m-d H:i:s');
				$stampling->out_time 					= null;
				$stampling->in_location 				= $request->location;
				$stampling->out_location 				= null;
				$stampling->extra_hours 				= 0;
				$stampling->reason_for_extra_hours 		= null;
				$stampling->is_extra_hours_approved 	= 0;
				$stampling->is_scheduled_hours_ov_hours = 0;
				$stampling->is_extra_hours_ov_hours 	= 0;
				$stampling->scheduled_hours_rate 		= 0;
				$stampling->extra_hours_rate 			= 0;
				$stampling->scheduled_hours_sum 		= 0;
				$stampling->extra_hours_sum 			= 0;
				$stampling->total_sum 					= 0;
				$stampling->entry_mode 					= $request->entry_mode ? $request->entry_mode :'Web';
				$stampling->status 						= $request->status ? $request->status : 0;
				$stampling->save();
			}
			elseif($request->type == "OUT")
			{
				$stampling = Stampling::where('date',$date)->where('user_id',Auth::id())->first();
				if($stampling->schedule_id != null)
				{
					$schedule = Schedule::find($stampling->schedule_id);
					$scheduled_shift_duration = getTimeDifference($schedule->shift_start_time,$schedule->shift_end_time);
					$scheduled_hours = getHours($schedule->shift_start_time,$schedule->shift_end_time,0);
				}
				else
				{
					$scheduled_hours_rate = "08";
				}
				$in_time = $stampling->in_time;
				$out_time = date('Y-m-d H:i:s');
				
				$worked_duration = getTimeDifference($in_time,$out_time);
				
				$worked_hours = getHours($in_time,$out_time,0);
				$extra_hours =  0;
				if($worked_hours > $scheduled_hours)
				{
					$extra_hours =  $worked_hours - $scheduled_hours;
				}

				$ov = OVHour::where('date',$date)->orWhere('date','')->latest()->first();
				if($ov)
				{
					$ov_start_time = ($ov->start_time);
					$ov_end_time = $ov->end_time;
					$ov_hours = getOVHours($in_time,$out_time,$ov_start_time,$ov_end_time);
				}
				else
				{
					$ov_hours = 0;
				}

				$is_scheduled_hours_ov_hours = 0;
				$is_extra_hours_ov_hours = 0;
				$scheduled_hours_rate = $user->contract_value;
				$companySetting = companySetting($user->top_most_parent_id);
				$extra_hours_rate = $companySetting ? $companySetting['extra_hour_rate'] : 0;
				if($is_scheduled_hours_ov_hours == 1)
				{
					$scheduled_hours_rate = $companySetting ? $companySetting['ov_hour_rate'] : 0;
				}
				if($is_extra_hours_ov_hours == 1)
				{
					$extra_hours_rate = $companySetting ? $companySetting['ov_hour_rate'] : 0;
				}
			
				$scheduled_hours_sum = $scheduled_hours_rate * $worked_hours;
				$extra_hours_sum = $extra_hours_rate * $extra_hours;
				$total_sum = $scheduled_hours_sum + $extra_hours_sum;

				$stampling->out_time 					= $out_time;
				$stampling->out_location 				= $request->location;
				$stampling->extra_hours 				= $extra_hours;
				$stampling->reason_for_extra_hours 		= $request->reason_for_extra_hours;
				$stampling->is_extra_hours_approved 	= 0;
				$stampling->is_scheduled_hours_ov_hours = $is_scheduled_hours_ov_hours;
				$stampling->is_extra_hours_ov_hours 	= $is_extra_hours_ov_hours;
				$stampling->scheduled_hours_rate 		= $scheduled_hours_rate;
				$stampling->extra_hours_rate 			= $extra_hours_rate;
				$stampling->scheduled_hours_sum 		= $scheduled_hours_sum;
				$stampling->extra_hours_sum 			= $extra_hours_sum;
				$stampling->total_sum 					= $total_sum;
				$stampling->entry_mode 					= (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
				$stampling->status 						= $request->status ? $request->status : 0;
				$stampling->save();

				$stampling = Stampling::where('id',$stampling->id)->with('user:id,name')->first();
			}
			else
			{
				return prepareResult(true,getLangByLabelGroups('Stampling','message_Invalid_Type') ,['type invalid'], config('httpcodes.success'));
			}

			$stampling = Stampling::where('id',$stampling->id)->with('user:id,name')->first();

			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Stampling','message_create') ,$stampling, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			\Log::error($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

	public function update(Request $request,$id)
	{
		DB::beginTransaction();
		try 
		{
			$validator = Validator::make($request->all(),[   
				'shift_id' => 'required|exists:company_work_shifts,id' 
			],
			[   
				'shift_id' =>  getLangByLabelGroups('Stampling','message_shift_id')
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$stampling = Stampling::find($id);
			if (!is_object($stampling)) {
				return prepareResult(false,getLangByLabelGroups('Stampling','message_id_not_found'), ['stampling not found'],config('httpcodes.not_found'));
			}

			$shift = CompanyWorkShift::find($request->shift_id);
			if (!is_object($shift)) {
				return prepareResult(false,getLangByLabelGroups('Stampling','message_id_not_found'), ["shift not found"],config('httpcodes.not_found'));
			}

			$date = date('Y-m-d',strtotime($request->shift_date));

			$stampling->shift_id 		= $request->shift_id;
			$stampling->user_id 			= $request->user_id ? $request->user_id : $stampling->user_id;
			$stampling->parent_id 		= $request->parent_id;
			$stampling->shift_name 		= $shift->shift_name;
			$stampling->shift_in_time	= $shift->shift_in_time;
			$stampling->shift_end_time 	= $shift->shift_end_time;
			$stampling->shift_color 		= $shift->shift_color;
			$stampling->shift_date 		= $request->shift_date ? $date : $stampling->shift_date;
			$stampling->leave_applied 	= ($request->leave_applied)? $request->leave_applied :0;
			$stampling->leave_approved 	= ($request->leave_approved)? $request->leave_approved :0;
			$stampling->status 			= ($request->status)? $request->status :0;
			$stampling->entry_mode 		=  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
			$stampling->created_by 		= Auth::id();
			$stampling->save();

			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Stampling','message_update') ,$stampling, config('httpcodes.success'));
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
			$stampling = Stampling::where('id',$id)->with('user:id,name')->first();
			if (!is_object($stampling)) {
				return prepareResult(false,getLangByLabelGroups('Stampling','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,'View Stampling' ,$stampling, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}
}
