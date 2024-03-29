<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScheduleTemplate;
use App\Models\Schedule;
use App\Models\ScheduleTemplateData;
use Validator;
use Auth;
use DB;
use App\Models\User;
use App\Models\CompanyWorkShift;
use App\Models\AgencyWeeklyHour;
use Exception;
use App\Models\EmailTemplate;
use Str;
use App\Models\UserScheduledDate;

class ScheduleTemplateController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:schedule-template-browse',['except' => ['show']]);
    //     $this->middleware('permission:schedule-template-add', ['only' => ['store']]);
    //     $this->middleware('permission:schedule-template-edit', ['only' => ['update']]);
    //     $this->middleware('permission:schedule-template-read', ['only' => ['show']]);
    //     $this->middleware('permission:schedule-template-delete', ['only' => ['destroy']]);
    // }
	

	public function scheduleTemplates(Request $request)
	{
		try 
		{
			$user = getUser();
			$query = ScheduleTemplate::orderBy('id', 'DESC')
				->with('templateData');
			if(!empty($request->hide_deactivated))
			{
				$query->whereNull('deactivation_date');
			}

			if(!empty($request->title))
			{
				$query->where('title','like', '%'.$request->title.'%');
			}
			if($request->status == 'active')
			{
				$query->where('status', 1);
			}
			if($request->status == 'inactive')
			{
				$query->where('status', 0);
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
					'last_page' => ceil($total / $perPage),
				];
				return prepareResult(true,"ScheduleTemplate list",$pagination,config('httpcodes.success'));
			}
			else
			{
				$query = $query->get();
			}

			return prepareResult(true,"ScheduleTemplate list",$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function store(Request $request)
	{
		DB::beginTransaction();
		try 
		{
			$validator = Validator::make($request->all(),[ 
				'title' => 'required', 
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}
			$activation_date =  NULL;
			if($request->status == 1)
			{
				$activation_date =  date('Y-m-d');
			}
			$scheduleTemplate = new ScheduleTemplate;
			$scheduleTemplate->title = $request->title;
			$scheduleTemplate->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
			$scheduleTemplate->status =  $request->status;
			$scheduleTemplate->activation_date =  $request->activation_date;
			$scheduleTemplate->save();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_create') ,$scheduleTemplate, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function update(Request $request,$id){
		DB::beginTransaction();
		try 
		{
			$validator = Validator::make($request->all(),[ 
				'title' => 'required', 
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$checkId = ScheduleTemplate::where('id',$id) ->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_record_not_found'), [],config('httpcodes.not_found'));
			}

			$scheduleTemplate = ScheduleTemplate::where('id',$id)->first();
			$activation_date =  $scheduleTemplate->activation_date;
			if($request->status == 1 && $scheduleTemplate->status == 0)
			{
				$activation_date =  date('Y-m-d');
			}
			$scheduleTemplate->title = $request->title;
			$scheduleTemplate->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
			$scheduleTemplate->status =  $request->status;
			$scheduleTemplate->activation_date =  $request->activation_date;
			$scheduleTemplate->save();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_update') ,$scheduleTemplate, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function destroy($id)
	{
		try 
		{
			$checkId= ScheduleTemplate::where('id',$id)->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			$checkId->delete();
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_delete') ,[], config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}

	public function show($id)
	{
		try 
		{
			$checkId= ScheduleTemplate::where('id',$id)->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_view') ,$checkId, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

	public function changeStatus(Request $request,$id)
	{
		DB::beginTransaction();
		try 
		{
			$scheduleTemplate = ScheduleTemplate::find($id);
			if($scheduleTemplate->deactivation_date != null)
			{
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_already_deactivated'), ['Once deactivated can not be Activated.'],config('httpcodes.bad_request'));
			}
			$activation_date =  $scheduleTemplate->activation_date;
			$deactivation_date = $scheduleTemplate->deactivation_date;
			if($request->status == 1 && $scheduleTemplate->status == 0)
			{
				$activation_date =  date('Y-m-d');
			}
			if($request->status == 0 && $scheduleTemplate->status == 1)
			{
				$deactivation_date =  date('Y-m-d');
			}
			$scheduleTemplate->status = $request->status;
			$scheduleTemplate->activation_date = $activation_date;
			$scheduleTemplate->deactivation_date = $deactivation_date;
			$scheduleTemplate->save();

			$messages = [];

			if(!empty($request->replacing_template_id))
			{
				Schedule::where('schedule_template_id',$request->replacing_template_id)->update(['is_active' => 0]);
			}  

			if($request->status == 1)
			{
				if(Schedule::where('schedule_template_id',$id)->where('user_id','!=',null)->count() < 1)
				{
					return prepareResult(false, getLangByLabelGroups('ScheduleTemplate','message_cannot_activate'),['can not activate as it has not user assigned schedules.'], config('httpcodes.internal_server_error'));
				}
				$schedules = Schedule::where('schedule_template_id',$id)->where('shift_start_time','>',date('Y-m-d H:i:s'))->get();

				foreach ($schedules as $key => $schedule) {
					$check = Schedule::where('user_id',$schedule->user_id)
					->where('user_id','!=',null)
					->where('shift_date',$schedule->shift_date)
					->where('is_active',1)
					->where('id',"!=",$schedule->id)
					// ->where(function($query) use($schedule) {
					// 	$query->where(function($query) use ($schedule) {
					// 		$query->where('shift_start_time',">=",$schedule->shift_start_time)
					// 		->where('shift_start_time',"<=",$schedule->shift_end_time);
					// 	})
					// 	->orWhere(function($query) use ($schedule) {
					// 		$query->where('shift_end_time',">=",$schedule->shift_start_time)
					// 		->where('shift_end_time',"<=",$schedule->shift_end_time);
					// 	});
					// })
					->first();
					if(!empty($check))
					{
						$messages[] = [
							"schedule_id"=>$check->id,
							"schedule_template_id"=>$check->schedule_template_id,
							"schedule_template_name"=>$check->scheduleTemplate->title,
							"user_id"=>$check->user_id,
							"emp_name"=>$check->user ? $check->user->name : '',
							"patient_name"=>$check->patient ? $check->patient->name : "",
							"start_time"=>$check->shift_start_time,
							"end_time"=>$check->shift_end_time
						];
					}
				}
				if(!empty($messages))
				{
					return prepareResult(false,$messages,[], config('httpcodes.bad_request'));
				}
				Schedule::where('schedule_template_id',$id)->update(['is_active' => $request->status]);
			}
			else
			{
				if(ScheduleTemplate::where('status',1)->where('id','!=',$request->schedule_template_id)->count() <1)
				{
					return prepareResult(false, getLangByLabelGroups('ScheduleTemplate','message_cannot_deactivate'),['can not deactivate as it is only active template'], config('httpcodes.internal_server_error'));
				}
				Schedule::where('schedule_template_id',$id)->update(['is_active' => $request->status]);
			}
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_change_status') ,$scheduleTemplate, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}
}
