<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Schedule;
use App\Models\User;
use App\Models\EmailTemplate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;
use Auth;

class LeaveController extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware('permission:Leave-browse',['except' => ['show']]);
    //     $this->middleware('permission:Leave-add', ['only' => ['store']]);
    //     $this->middleware('permission:Leave-edit', ['only' => ['update']]);
    //     $this->middleware('permission:Leave-read', ['only' => ['show']]);
    //     $this->middleware('permission:Leave-delete', ['only' => ['destroy']]);

    // }

	public function Leaves(Request $request)
	{
		try {

			$query = Leave::orderBy('id', 'DESC')->with('user:id,name,user_type_id,branch_id','user.userType','user.branch', 'leaves:id,group_id,date')->groupBy('group_id');
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
				return prepareResult(true,"Leave list",$pagination,config('httpcodes.success'));
			}
			else
			{
				$query = $query->get();
			}
			return prepareResult(true,"Leave list",$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
    	DB::beginTransaction();
    	try {
    		$leave_ids = [];

    		if($request->is_repeat == 1)
    		{

    			$validation = \Validator::make($request->all(), [
    				'start_date'      => 'required|date',
    				'end_date'      => 'required|date',
    			]);

    			if ($validation->fails()) {
    				return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    			}

    			$group_id = generateRandomNumber();

    			$start_date = $request->start_date;
    			$end_date = $request->end_date;
    			$every_week = $request->every_week;
                $week_days = $request->week_days;

                if(empty($request->week_days) && (empty($request->every_week) || $request->every_week == 1) )
                {
                    $date1 = strtotime($start_date);
                    $date2 = strtotime($end_date);
                    for ($currentDate=$date1; $currentDate<=$date2; $currentDate += (86400)) 
                    {                                   
                        $dates[] = date('Y-m-d', $currentDate);
                    }
                }
                else
                {
                    $dates = calculateDates($start_date,$end_date,$every_week,$week_days);
                }

    			foreach ($dates as $key => $date) 
    			{
                    $schedule_id = null;
                    $schedule = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->first();

                    if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
                    {
                        $schedule_id = $schedule->id;
                        $schedule->update(['leave_applied' => '1']);
                    }

    				$leave = new Leave;
    				$leave->user_id = Auth::id();
    				$leave->schedule_id = $schedule_id;
    				$leave->group_id = $group_id;
    				$leave->date = $date;
    				$leave->reason = $request->reason;
    				$leave->entry_mode = $request->entry_mode ? $request->entry_mode : 'web';
                    $leave->status = $request->status ? $request->status : 0;
                    $leave->approved_by = $request->approved_by;
    				$leave->save();
    				$leave_ids[] = $leave->id;

                    
    			}        
    		}
    		else
    		{
    			$validation = \Validator::make($request->all(), [
    				'leaves'      => 'required|array',
    			]);

    			if ($validation->fails()) {
    				return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    			}
                $group_id = generateRandomNumber();

    			foreach ($request->leaves as $key => $value) 
    			{
    				foreach ($value['dates'] as $key => $date) 
    				{
                        $schedule_id = null;
                        $schedule = Schedule::where('shift_date',$date)->where('user_id',Auth::id())->first();

                        if(Schedule::where('shift_date',$date)->where('user_id',Auth::id())->count() > 0)
                        {
                            $schedule_id = $schedule->id;
                            $schedule->update(['leave_applied' => '1']);
                        }

    					$leave = new Leave;
    					$leave->user_id = Auth::id();
    					$leave->schedule_id = $schedule_id;
                        $leave->group_id = $group_id;
    					$leave->date = $date;
    					$leave->reason = $value['reason'];
    					$leave->entry_mode = $request->entry_mode?$request->entry_mode:'web';
    					$leave->save();
    					$leave_ids[] = $leave->id;
    				}   
    			}  
    		}

    		$data = Leave::whereIn('id',$leave_ids)->with('user:id,name,user_type_id,branch_id','user.userType','user.branch', 'leaves:id,group_id,date')->groupBy('group_id')->get();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_create') ,$data, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    	try 
    	{
    		$checkId= Leave::where('id',$id)->with('user:id,name,user_type_id','user.userType', 'leaves:id,group_id,date')->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('Leave','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		return prepareResult(true,'View Leave' ,$checkId, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request,$id)
    {
    	$validation = \Validator::make($request->all(), [
    		'reason'      => 'required',
    	]);

    	if ($validation->fails()) {
    		return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    	} 

    	DB::beginTransaction();
    	try {
    		$leave = Leave::where('id',$id)->with('user:id,name,user_type_id','user.userType')->first();
    		$leave->reason = $request->reason;
    		$leave->entry_mode = $request->entry_mode ? $request->entry_mode : 'web';
    		$leave->save();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_create') ,$leave, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Leave $leave
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */

    public function destroy($id)
    {
    	try 
    	{
    		$checkId= Leave::find($id);
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('Leave','message_id_not_found'), [],config('httpcodes.not_found'));
    		}
    		Leave::where('id',$id)->delete();
    		return prepareResult(true,getLangByLabelGroups('Leave','message_delete') ,[], config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    public function getUserLeaves($id)
    {
    	try {

    		$query = Leave::where('user_id', $id)->get(['date']);
    		$dates = [];
    		foreach ($query as $key => $value) {
    			$dates[] = $value->date;
    		}

    		return prepareResult(true,"Leave list",$dates,config('httpcodes.success'));
    	}
    	catch(Exception $exception) {
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

    	}
    }

    public function leavesApprove(Request $request)
    {
        DB::beginTransaction();
        try 
        {
            if(!empty($request->group_id))
            {
            	$leave = Leave::where('group_id',$request->group_id)->first();
                $update = Leave::where('group_id',$request->group_id)
    				->update([
    					'is_approved' => '1',
    					'approved_by' => Auth::id(), 
    					'approved_date' => date('Y-m-d'), 
    					'approved_time' => date('H:i'), 
    					'status' => 1
    				]);
                $dates = [];
                $leaves = Leave::where('group_id',$request->group_id)->with('user:id,name,user_type_id,branch_id','user.userType','user.branch', 'leaves:id,group_id,date')->groupBy('group_id')->get();
                foreach ($leaves as $key => $value) {
                	$dates[] = $value->date;
                }
                $dates = implode(',', $dates);
                $users = User::whereIn('employee_type',$request->employee_type)->get();
                if($request->notify_employees == true)
                {
                	foreach ($users as $key => $user) 
                	{
                		//---------------------Notification------------------//

        				$title = "";
        				$body = "";
        				$module =  "leave";
        				$id =  $request->group_id;
        				$screen =  "list";
        				
        				$getMsg = EmailTemplate::where('mail_sms_for', 'schedule-request')->first();
        				if($getMsg )
        				{
        					$body = $getMsg->notify_body;
        					$title = $getMsg->mail_subject;

        					$arrayVal = [
        						'{{name}}'  		=> $user->name,
        						'{{requested_by}}' 	=> Auth::User()->name,
        						'{{dates}}'			=> $dates
        					];
        					$body = strReplaceAssoc($arrayVal, $body);
        				}
        				actionNotification($user,$title,$body,$module,$screen,$id,'info',1);
                	}

                }
                
            }
            elseif(!empty($request->leaves))
            {
            	$leave_ids = [];
            	foreach ($request->leaves as $key => $value) 
            	{
            		$leave_ids[] = $value['leave_id']; 
            		$leave = Leave::find($value['leave_id']);
        			$schedule = Schedule::where('shift_date',$leave->date)->where('user_id',$leave->user_id)->first();
        			$user = User::find($leave['user_id']);
        			if(!empty($schedule))
        			{
        				$assignSchedule = new Schedule;
        				$assignSchedule->shift_id = $schedule->shift_id;
        				$assignSchedule->user_id = $leave['user_id'];
        				$assignSchedule->parent_id = $schedule->id;
        				$assignSchedule->shift_name = $schedule->shift_name;
        				$assignSchedule->shift_start_time = $schedule->shift_start_time;
        				$assignSchedule->shift_end_time = $schedule->shift_end_time;
        				$assignSchedule->shift_color = $schedule->shift_color;
        				$assignSchedule->shift_date = $schedule->shift_date;
        				$assignSchedule->leave_applied = 0;
        				$assignSchedule->leave_approved = 0;
        				$assignSchedule->status = 0;
        				$assignSchedule->entry_mode = $request->entry_mode ? $request->entry_mode :'Web';
        				$assignSchedule->created_by = Auth::id();
        				$assignSchedule->save();

        				$schedule->update(['leave_approved' => '1']);

        				//---------------------Notification------------------//

        				$title = "";
        				$body = "";
        				$module =  "schedule";
        				$id =  $assignSchedule->id;
        				$screen =  "detail";
        				
        				$getMsg = EmailTemplate::where('mail_sms_for', 'schedule-assignment')->first();
        				if($getMsg )
        				{
        					$body = $getMsg->notify_body;
        					$title = $getMsg->mail_subject;

        					$arrayVal = [
        						'{{name}}'  		=> $user->name,
        						'{{assigned_by}}' 	=> Auth::User()->name,
        						'{{schedule_title}}'=> $assignSchedule->shift_name,
        						'{{date}}' 			=> $assignSchedule->shift_date,
        						'{{start_time}}' 	=> $assignSchedule->shift_start_time,
        						'{{end_time}}' 		=> $assignSchedule->shift_end_time,

        					];
        					$body = strReplaceAssoc($arrayVal, $body);
        				}
        				actionNotification($user,$title,$body,$module,$screen,$id,'info',1);
        			}

        			$update = $leave->update([
						'is_approved' => '1',
						'approved_by' => Auth::id(), 
						'approved_date' => date('Y-m-d'), 
						'approved_time' => date('H:i'), 
						'status' => 1
					]);
			        if($request->notify_employees == true)
			        {
						$title = "";
						$body = "";
						$module =  "leave";
						$id =  $leave->group_id;
						$screen =  "detail";
						
						$getMsg = EmailTemplate::where('mail_sms_for', 'schedule-request')->first();
						if($getMsg )
						{
							$body = $getMsg->notify_body;
							$title = $getMsg->mail_subject;

							$arrayVal = [
								'{{name}}'  		=> $user->name,
								'{{requested_by}}' 	=> Auth::User()->name,
								'{{dates}}'			=> $leave->date
							];
							$body = strReplaceAssoc($arrayVal, $body);
						}
						actionNotification($user,$title,$body,$module,$screen,$id,'info',1);
			        	
			        }
        			$leaves = Leave::whereIn('id',$leave_ids)->with('user:id,name,user_type_id,branch_id','user.userType','user.branch', 'leaves:id,group_id,date')->groupBy('group_id')->get();
                }
            }


            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$leaves, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
