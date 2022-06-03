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
        //$this->middleware('permission:schedule-print', ['only' => ['printSchedule']]);

	}
	

	public function schedules(Request $request)
	{
		try 
		{
			$query = Schedule::select('schedules.*')->orderBy('created_at', 'DESC');
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

			$data = Schedule::whereIn('id',$schedule_ids)->get();

			DB::commit();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_create') ,$schedule, config('httpcodes.success'));
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
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}

			$shift = CompanyWorkShift::find($request->shift_id);
			if (!is_object($shift)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}

			$schedule->shift_id = $request->shift_id;
			$schedule->user_id = $request->user_id ? $request->user_id : $schedule->user_id;
			$schedule->parent_id = $request->parent_id;
			$schedule->shift_name = $shift->shift_name;
			$schedule->shift_start_time = $shift->shift_start_time;
			$schedule->shift_end_time = $shift->shift_end_time;
			$schedule->shift_color = $shift->shift_color;
			$schedule->shift_date = $request->date ? $request->date : $schedule->date;
			$schedule->leave_applied = ($request->leave_applied)? $request->leave_applied :0;
			$schedule->leave_approved = ($request->leave_approved)? $request->leave_approved :0;
			$schedule->status = ($request->status)? $request->status :0;
			$schedule->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
			$schedule->created_by = Auth::id();
			$schedule->save();

			DB::commit();

			$data = getSchedule($schedule->id);
			return prepareResult(true,getLangByLabelGroups('Schedule','message_update') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			\Log::error($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

	public function destroy($id){

		try {
			$user = getUser();
			$schedule= Schedule::where('id',$id)->first();
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			$schedule = Schedule::where('id',$id)->delete();
			return prepareResult(true,getLangByLabelGroups('Schedule','message_delete') ,[], config('httpcodes.success'));


		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));

		}
	}

	public function show($id){
		try {
			$user = getUser();
			$schedule= Schedule::with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','ScheduleLogs','scheduleActions.scheduleActionLogs.editedBy', 'branch:id,name')
			->withCount('scheduleActions')
			->where('id',$id)
			->first();
			if (!is_object($schedule)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}

			$data = getSchedule($id);
			return prepareResult(true,'View Schedule' ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}
	private function getWhereRawFromRequest(Request $request) {
		$w = '';
		if (is_null($request->input('status')) == false) {
			if ($w != '') {$w = $w . " AND ";}
			$w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
		}
		if (is_null($request->input('branch_id')) == false) {
			if ($w != '') {$w = $w . " AND ";}
			$w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
		}
		return($w);

	}

	public function actionSchedule(Request $request)
	{
		DB::beginTransaction();
		try {
			$user = getUser();
			$validator = Validator::make($request->all(),[
				'schedule_ids' => 'required|array|min:1',   
			],
			[
				'schedule_ids' =>  getLangByLabelGroups('Schedule','message_id'),   
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$schedule = Schedule::whereIn('id', $request->schedule_ids)->update([
				'is_signed' => $request->is_signed,
				'signed_by' => auth()->id(),
				'signed_date' => date('Y-m-d')
			]);
			DB::commit();
			$data = getSchedules($request->schedule_ids);
			return prepareResult(true,getLangByLabelGroups('Schedule','message_sign') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function printSchedule(Request $request)
	{
		try {
			$user = getUser();
			$checkUser = User::where('id',$request->patient_id)
			->first();
			if (!is_object($checkUser)) {
				return prepareResult(false,getLangByLabelGroups('Patient','id_not_found'), [],config('httpcodes.not_found'));
			}

			$schedules = Schedule::where('patient_id', $request->patient_id);
			if(!empty($request->from_date) && !empty($request->end_date))
			{
				$schedules->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->end_date);
			}
			elseif(!empty($request->from_date) && empty($request->end_date))
			{
				$schedules->whereDate('created_at', '>=', $request->from_date);
			}
			elseif(empty($request->from_date) && !empty($request->end_date))
			{
				$schedules->whereDate('created_at', '<=', $request->end_date);
			}
			if($request->print_with_secret=='yes')
			{
				$schedules->where('is_secret', 1);
			}
			else
			{
				$schedules->where('is_secret', 0);
			}
			$schedules = $schedules->where('is_signed', 1)->get();
			$filename = $request->patient_id."-".time().".pdf";
			$data['schedules'] = $schedules;
			$pdf = PDF::loadView('print-schedule', $data);
			$pdf->save('reports/schedules/'.$filename);
			$url = env('CDN_DOC_URL').'reports/schedules/'.$filename;
			return prepareResult(true,'Print schedule' ,$url, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			\Log::error($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

	public function isActiveSchedule(Request $request)
	{
		$validator = Validator::make($request->all(),[
			'schedule_id' => 'required',   
			'is_active' => 'required',   
		]);
		if ($validator->fails()) {
			return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
		}
		DB::beginTransaction();
		try {
			$user = getUser();

			$schedule = Schedule::where('id', $request->schedule_id)->where('is_signed', 1)->update([
				'is_active' => $request->is_active
			]);
			DB::commit();
			$data = getSchedule($request->schedule_id);
			return prepareResult(true,getLangByLabelGroups('Schedule','message_sign') ,$data, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

}
