<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Validator;
use Auth;
use DB;
use App\Models\User;
use App\Models\CompanyWorkShop;
use Exception;
use App\Models\Activity;
use PDF;
use App\Models\EmailTemplate;
use Str;

class ScheduleController extends Controller
{
	public function __construct()
	{

		$this->middleware('permission:schedule-browse',['except' => ['show']]);
		$this->middleware('permission:schedule-add', ['only' => ['store']]);
		$this->middleware('permission:schedule-edit', ['only' => ['update']]);
		$this->middleware('permission:schedule-read', ['only' => ['show']]);
		$this->middleware('permission:schedule-delete', ['only' => ['destroy']]);
        //$this->middleware('permission:schedule-print', ['only' => ['printSchedule']]);

	}
	

	public function schedules(Request $request)
	{
		try {
			$user = getUser();
			if(!empty($user->branch_id)) {
				$allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
			} else {
				$allChilds = userChildBranches(\App\Models\User::find($user->id));
			}

			$query = Schedule::select('schedules.*')
			->with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','ScheduleLogs','scheduleActions.scheduleActionLogs.editedBy', 'branch:id,name')
			->withCount('scheduleActions')
			->orderBy('schedules.date', 'DESC')->orderBy('schedules.time', 'DESC');

			if(in_array($user->user_type_id, [2,3,4,5,11]))
			{

			}
			else
			{
				$query->where('schedules.is_secret', '!=', 1);
			}

			if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
			{
				$query->where(function ($q) use ($user) {
					$q->where('schedules.patient_id', $user->id)
					->orWhere('schedules.patient_id', $user->parent_id);
				});
			}

			if($user->user_type_id !='2') {
				$query->whereIn('schedules.branch_id',$allChilds);
			}

			if(!empty($request->activity_id))
			{
				$query->where('schedules.activity_id', $request->activity_id);
			}

			if(!empty($request->branch_id))
			{
				$query->where('schedules.branch_id', $request->branch_id);
			}

			if(!empty($request->patient_id))
			{
				$query->where('schedules.patient_id', $request->patient_id);
			}

			if(!empty($request->emp_id))
			{
				$query->where('schedules.emp_id', $request->emp_id);
			}

			if(!empty($request->category_id))
			{
				$query->where('schedules.category_id', $request->category_id);
			}

			if(!empty($request->subcategory_id))
			{
				$query->where('schedules.subcategory_id', $request->subcategory_id);
			}

			if($request->is_secret=='yes')
			{
				$query->where('schedules.is_secret', 1);
			}
			elseif($request->is_secret=='no')
			{
				$query->where('schedules.is_secret', 0);
			}

			if($request->is_signed=='yes')
			{
				$query->where('schedules.is_signed', 1);
			}
			elseif($request->is_signed=='no')
			{
				$query->where('schedules.is_signed', 0);
			}

			if($request->is_active=='yes')
			{
				$query->where('schedules.is_active', 1);
			}
			elseif($request->is_active=='no')
			{
				$query->where('schedules.is_active', 0);
			}

			if($request->with_activity=='yes')
			{
				$query->whereNotNull('schedules.activity_id');
			}
			elseif($request->with_activity=='no')
			{
				$query->whereNull('schedules.activity_id');
			}

			if(!empty($request->data_of))
			{
				$date = date('Y-m-d',strtotime('-1'.$request->data_of.''));
				$query->where('schedules.created_at','>=', $date);
			}
			if(!empty($request->perPage))
			{
                ////////Counts
				$scheduleCounts = Schedule::select([
					\DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
					\DB::raw('COUNT(IF(is_active = 1, 0, NULL)) as total_active'),
					\DB::raw('COUNT(IF(is_secret = 1, 0, NULL)) as total_secret'),
					\DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
					\DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
				]);
				if(in_array($user->user_type_id, [2,3,4,5,11]))
				{

				}
				else
				{
					$scheduleCounts->where('is_secret', '!=', 1);
				}
				if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
				{
					$scheduleCounts->where(function ($q) use ($user) {
						$q->where('schedules.patient_id', $user->id)
						->orWhere('schedules.patient_id', $user->parent_id);
					});
				}

				if($user->user_type_id !='2') {
					$scheduleCounts->whereIn('schedules.branch_id',$allChilds);
				}

				if(!empty($request->activity_id))
				{
					$scheduleCounts->where('schedules.activity_id', $request->activity_id);
				}

				if(!empty($request->branch_id))
				{
					$scheduleCounts->where('schedules.branch_id', $request->branch_id);
				}

				if(!empty($request->patient_id))
				{
					$scheduleCounts->where('schedules.patient_id', $request->patient_id);
				}

				if(!empty($request->emp_id))
				{
					$scheduleCounts->where('schedules.emp_id', $request->emp_id);
				}

				if(!empty($request->category_id))
				{
					$scheduleCounts->where('schedules.category_id', $request->category_id);
				}

				if(!empty($request->subcategory_id))
				{
					$scheduleCounts->where('schedules.subcategory_id', $request->subcategory_id);
				}

				if(!empty($request->data_of))
				{
					$date = date('Y-m-d',strtotime('-1'.$request->data_of.''));
					$scheduleCounts->where('schedules.created_at','>=', $date);
				}
				$scheduleCounts = $scheduleCounts->first();

				$perPage = $request->perPage;
				$page = $request->input('page', 1);
				$total = $query->count();
				$result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

				$pagination =  [
					'data' => $result,
					'total' => $total,
					'current_page' => $page,
					'per_page' => $perPage,
					'last_page' => ceil($total / $perPage),
					'total_signed' => $scheduleCounts->total_signed,
					'total_active' => $scheduleCounts->total_active,
					'total_secret' => $scheduleCounts->total_secret,
					'total_with_activity' => $scheduleCounts->total_with_activity,
					'total_without_activity' => $scheduleCounts->total_without_activity,
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
		try {
			$user = getUser();
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

			$shift = CompanyWorkShift::find($shift_id);
			if (!is_object($shift)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}

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
				$schedule->is_on_leave = ($request->is_on_leave)? $request->is_on_leave :0;
				$schedule->status = ($request->status)? $request->status :0;
				$schedule->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
				$schedule->save();
			}



			/*-----------Send notification---------------------*/

			$user = User::select('id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',getBranchId())->first();
			$module =  "schedule";
			$data_id =  $schedule->id;
			$screen =  "detail";

			$title  = false;
			$body   = false;
			$getMsg = EmailTemplate::where('mail_sms_for', 'schedule')->first();

			if($getMsg)
			{
				$body = $getMsg->notify_body;
				$title = $getMsg->mail_subject;
				$arrayVal = [
					'{{name}}'              => $user->name,
					'{{created_by}}'        => Auth::User()->name,
				];
				$body = strReplaceAssoc($arrayVal, $body);
			}
			actionNotification($user,$title,$body,$module,$screen,$data_id,'info',1);

			DB::commit();

			$data = getSchedule($schedule->id);
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
			$user = getUser();

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

			$checkId = Schedule::where('id',$id)
			->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			}

			if($checkId->is_signed == 1)
			{
				$scheduleLog                     = new ScheduleLog;
				$scheduleLog->schedule_id         = $checkId->id;
				$scheduleLog->description        = $checkId->description;
				$scheduleLog->edited_by          = $user->id;
				$scheduleLog->reason_for_editing = $request->reason_for_editing;
				$scheduleLog->description_created_at =$checkId->edit_date;
				$scheduleLog->save();
			}

			$parent_id  = (is_null($checkId->parent_id)) ? $id : $checkId->parent_id;
			$schedule = Schedule::where('id',$id)->with('Category:id,name','Subcategory:id,name')->first();
			$schedule->shift_id = $request->shift_id;
			$schedule->user_id = $request->user_id;
			$schedule->parent_id = $request->parent_id;
			$schedule->shift_name = $shift->shift_name;
			$schedule->shift_start_time = $shift->shift_start_time;
			$schedule->shift_end_time = $shift->shift_end_time;
			$schedule->shift_color = $shift->shift_color;
			$schedule->shift_date = $date;
			$schedule->is_on_leave = ($request->is_on_leave)? $request->is_on_leave :0;
			$schedule->status = ($request->status)? $request->status :0;
			$schedule->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
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
			$checkId= Schedule::where('id',$id)->first();
			if (!is_object($checkId)) {
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
			$checkId= Schedule::with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','ScheduleLogs','scheduleActions.scheduleActionLogs.editedBy', 'branch:id,name')
			->withCount('scheduleActions')
			->where('id',$id)
			->first();
			if (!is_object($checkId)) {
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
