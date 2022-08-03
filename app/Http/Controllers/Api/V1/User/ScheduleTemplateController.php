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
			$query = ScheduleTemplate::orderBy('id', 'DESC')->with('templateData');
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

			// $user = User::find($request->user_id);
			// $shedule_ids = [];
			// $group_id = generateRandomNumber();
			// $assignedWork_id = null;
			// $assignedWork = null;
			// if(!empty($user && $user->assignedWork))
			// {
			// 	$assignedWork = $user->assignedWork;
			// 	$assignedWork_id = $assignedWork->id;
			// }

			// foreach ($request->schedule_template_data as $key => $value) 
			// {
			// 	if($key == 0)
			// 	{
			// 		$start_date = $value['date'];
			// 	}

			// 	$date = date('Y-m-d',strtotime($value['date']));
			// 	foreach($value['shifts'] as $key=>$shift)
			// 	{
			// 		$shift_name 		= null;
			// 		$shift_color 		= null;
			// 		$shift_type 		= null;
			// 		if(!empty($shift['shift_id']))
			// 		{
			// 			$c_shift = CompanyWorkShift::find($shift['shift_id']);
			// 			if (!is_object($c_shift)) {
			// 				return prepareResult(false,getLangByLabelGroups('Schedule','message_id_not_found'), [],config('httpcodes.not_found'));
			// 			}
			// 			$shift_name 		= $c_shift->shift_name;
			// 			$shift_color 		= $c_shift->shift_color;
			// 			$shift_type 		= $c_shift->shift_type;
			// 		}

			// 		$shift_start_time = $shift['shift_start_time'];
			// 		$shift_end_time = $shift['shift_end_time'];
			// 		if(!empty($request->user_id))
			// 		{
			// 			if(!empty($user->assignedWork))
			// 			{
			// 				$assignedWork = $user->assignedWork;
			// 				$assignedWork_id = $assignedWork->id;
			// 			}
			// 			$result = scheduleWorkCalculation($date,$shift_start_time,$shift_end_time,$shift['schedule_type'],$shift_type);

			// 			$schedule = new Schedule;
			// 			$schedule->user_id = $user->id;
			// 			$schedule->patient_id = $request->patient_id;
			// 			$schedule->branch_id = $request->branch_id;
			// 			$schedule->shift_id = $shift['shift_id'];
			// 			$schedule->parent_id = NULL;
			// 			$schedule->created_by = Auth::id();
			// 			$schedule->slot_assigned_to = null;
			// 			$schedule->employee_assigned_working_hour_id = $assignedWork_id;
			// 			$schedule->schedule_template_id = $scheduleTemplate->id;
			// 			$schedule->schedule_type = $shift['schedule_type'];
			// 			$schedule->shift_date = $date;
			// 			$schedule->group_id = $group_id;
			// 			$schedule->shift_name = $shift_name;
			// 			$schedule->shift_type = $shift_type;
			// 			$schedule->shift_color = $shift_color;
			// 			$schedule->shift_start_time = $shift_start_time;
			// 			$schedule->shift_end_time = $shift_end_time;
			// 			$schedule->leave_applied = 0;
			// 			$schedule->leave_group_id = null;
			// 			$schedule->leave_type = null;
			// 			$schedule->leave_reason = null;
			// 			$schedule->leave_approved = 0;
			// 			$schedule->leave_approved_by = null;
			// 			$schedule->leave_approved_date_time = null;
			// 			$schedule->leave_notified_to = null;
			// 			$schedule->notified_group = null;
			// 			$schedule->is_active = 1;
			// 			$schedule->scheduled_work_duration = $result['scheduled_work_duration'];
			// 			$schedule->extra_work_duration = $result['extra_work_duration'];
			// 			$schedule->emergency_work_duration = $result['emergency_work_duration'];
			// 			$schedule->ob_work_duration = $result['ob_work_duration'];
			// 			$schedule->ob_type = $result['ob_type'];
			// 			$schedule->ob_start_time = $result['ob_start_time'];
			// 			$schedule->ob_end_time = $result['ob_end_time'];
			// 			$schedule->status = $request->status ? $request->status :0;
			// 			$schedule->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
			// 			$schedule->save();

			// 	    	//----notify-emp-for-schedule-assigned---//

			// 			$data_id =  $schedule->id;
			// 			$notification_template = EmailTemplate::where('mail_sms_for', 'schedule-assignment')->first();
			// 			$variable_data = [
			// 				'{{name}}'          => $user->name,
			// 				'{{schedule_title}}'=> $schedule->title,
			// 				'{{date}}'          => $schedule->shift_date,
			// 				'{{start_time}}'    => $schedule->shift_start_time,
			// 				'{{end_time}}'      => $schedule->shift_end_time,
			// 				'{{assigned_by}}'   => Auth::User()->name
			// 			];
			// 			actionNotification($user,$data_id,$notification_template,$variable_data);
			// 	        //----------------------------------------//

			// 			if(!empty($request->patient_id))
			// 			{
			// 				$patientAssignedHours = AgencyWeeklyHour::where('user_id',$request->patient_id)
			// 				->where('start_date','>=' ,$schedule->shift_date)
			// 				->where('end_date','<=',$schedule->shift_date)
			// 				->orderBy('id','desc')->first();
			// 				if(empty($patientAssignedHours))
			// 				{
			// 					$patientAssignedHours = AgencyWeeklyHour::where('user_id',$request->patient_id)->orderBy('id','desc')->first();
			// 				}
			// 				$scheduledHours = $patientAssignedHours->scheduled_hours + $schedule->scheduled_work_duration + $schedule->emergency_work_duration + $schedule->ob_work_duration + $schedule->extra_work_duration;
			// 				$patientAssignedHours->update(['scheduled_hours'=>$scheduledHours]);
			// 			}
			// 			$schedule_ids[] = $schedule->id;
			// 		}
			// 		$templateData = new ScheduleTemplateData;
			// 		$templateData->schedule_template_id = $scheduleTemplate->id;
			// 		$templateData->shift_id = $shift['shift_id'];
			// 		$templateData->schedule_type = $shift['schedule_type'];
			// 		$templateData->shift_date = $date;
			// 		$templateData->shift_name = $shift_name;
			// 		$templateData->shift_type = $shift_type;
			// 		$templateData->shift_color = $shift_color;
			// 		$templateData->shift_start_time = $shift_start_time;
			// 		$templateData->shift_end_time = $shift_end_time;
			// 		$templateData->created_by = Auth::id();
			// 		$templateData->is_active = $request->status ? $request->status :0;
			// 		$templateData->entry_mode = $request->entry_mode?$request->entry_mode:'Web';
			// 		$templateData->save();
			// 	}
			// }
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_create') ,$scheduleTemplate, config('httpcodes.success'));
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
				'title' => 'required', 
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$checkId = ScheduleTemplate::where('id',$id) ->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_id_not_found'), [],config('httpcodes.not_found'));
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
			\Log::error($exception);
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
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			$checkId->delete();
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_delete') ,[], config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}

	public function show($id)
	{
		try 
		{
			$checkId= ScheduleTemplate::where('id',$id)->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_id_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,'View ScheduleTemplate' ,$checkId, config('httpcodes.success'));
		}
		catch(Exception $exception) {
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
			return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_create') ,$scheduleTemplate, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			\Log::error($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}
}
