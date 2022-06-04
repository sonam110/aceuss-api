<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
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
			$query = Stampling::orderBy('created_at', 'DESC');
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
				'schedule_id' => 'required|exists:company_work_shifts,id',
			],
			[   
				'schedule_id' =>  getLangByLabelGroups('Stampling','message_shift_id'),
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$schedule = Schedule::find($request->schedule_id);
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Stampling','message_id_not_found'), [],config('httpcodes.not_found'));
			}

			$shedule_ids = [];

			foreach($request->shift_dates as $key=>$shift_date)
			{
				$date = date('Y-m-d',strtotime($shift_date));
				$stampling = new Stampling;
				$stampling->shedule_id 					= $request->shedule_id;
				$stampling->user_id 					= Auth::id();
				$stampling->shift_name 					= $request->shift_name;
				$stampling->in_time 					= $request->in_time;
				$stampling->out_time 					= $request->out_time;
				$stampling->in_location 				= $request->in_location;
				$stampling->out_location 				= $request->out_location;
				$stampling->extra_hours 				= $request->extra_hours;
				$stampling->reason_for_extra_hours 		= $request->reason_for_extra_hours;
				$stampling->is_extra_hours_approved 	= 0;
				$stampling->is_scheduled_hours_ov_hours = $is_scheduled_hours_ov_hours;
				$stampling->is_extra_hours_ov_hours 	= $is_extra_hours_ov_hours;
				$stampling->scheduled_hours_rate 		= $scheduled_hours_rate;
				$stampling->extra_hours_rate 			= $request->extra_hours_rate;
				$stampling->scheduled_hours_sum 		= $request->scheduled_hours_sum;
				$stampling->extra_hours_sum 			= $request->extra_hours_sum;
				$stampling->total_sum 					= $request->total_sum;
				$stampling->entry_mode 					= (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
				$stampling->save();
			}

			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Stampling','message_create') ,$stampling, config('httpcodes.success'));
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
			$stampling= Stampling::find($id);
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
