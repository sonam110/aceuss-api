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
			$query = Schedule::orderBy('created_at', 'DESC')->with('user:id,name');
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
				'shift_id' => 'required|exists:company_work_shifts,id' ,
				'user_id' => 'required|exists:users,id'
			],
			[   
				'shift_id' =>  getLangByLabelGroups('Schedule','message_shift_id'),
				'user_id' =>  getLangByLabelGroups('Schedule','message_user_id'),
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$shift = CompanyWorkShift::find($request->shift_id);
			if (!is_object($shift)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}

			$shedule_ids = [];

			foreach($request->shift_dates as $key=>$shift_date)
			{
				$date = date('Y-m-d',strtotime($shift_date));
				$schedule = new Schedule;
				$schedule->shift_id = $request->shift_id;
				$schedule->user_id = $request->user_id;
				$schedule->parent_id = $request->parent_id;
				$schedule->shift_name = $shift->shift_name;
				$schedule->shift_start_time = $shift->shift_start_time;
				$schedule->shift_end_time = $shift->shift_end_time;
				$schedule->shift_color = $shift->shift_color;
				$schedule->shift_date = $date;
				$schedule->leave_applied = ($request->leave_applied)? $request->leave_applied :0;
				$schedule->leave_approved = ($request->leave_approved)? $request->leave_approved :0;
				$schedule->status = ($request->status)? $request->status :0;
				$schedule->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
				$schedule->created_by = Auth::id();
				$schedule->save();
				$schedule_ids[] = $schedule->id;
			}

			$data = Schedule::whereIn('id',$schedule_ids)->with('user:id,name')->get();

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
			$validator = Validator::make($request->all(),[   
				'shift_id' => 'required|exists:company_work_shifts,id' 
			],
			[   
				'shift_id' =>  getLangByLabelGroups('Schedule','message_shift_id')
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$schedule = Schedule::find($id);
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), ['schedule not found'],config('httpcodes.not_found'));
			}

			$shift = CompanyWorkShift::find($request->shift_id);
			if (!is_object($shift)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), ["shift not found"],config('httpcodes.not_found'));
			}

			$date = date('Y-m-d',strtotime($request->shift_date));

			$schedule->shift_id 		= $request->shift_id;
			$schedule->user_id 			= $request->user_id ? $request->user_id : $schedule->user_id;
			$schedule->parent_id 		= $request->parent_id;
			$schedule->shift_name 		= $shift->shift_name;
			$schedule->shift_start_time	= $shift->shift_start_time;
			$schedule->shift_end_time 	= $shift->shift_end_time;
			$schedule->shift_color 		= $shift->shift_color;
			$schedule->shift_date 		= $request->shift_date ? $date : $schedule->shift_date;
			$schedule->leave_applied 	= ($request->leave_applied)? $request->leave_applied :0;
			$schedule->leave_approved 	= ($request->leave_approved)? $request->leave_approved :0;
			$schedule->status 			= ($request->status)? $request->status :0;
			$schedule->entry_mode 		=  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
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
			$schedule= Schedule::with('user:id,name')->where('id',$id)->first();
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,'View Schedule' ,$schedule, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

}
