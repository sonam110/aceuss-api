<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Schedule;

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

			$query = Leave::orderBy('id', 'DESC')->with('user:id,name');
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
    					$leave->date = $date;
    					$leave->reason = $value['reason'];
    					$leave->entry_mode = $request->entry_mode?$request->entry_mode:'web';
    					$leave->save();
    					$leave_ids[] = $leave->id;
    				}   
    			}  
    		}

    		$data = Leave::whereIn('id',$leave_ids)->with('user:id,name')->get();
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
    		$checkId= Leave::where('id',$id)->with('user:id,name')->first();
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
    		$leave = Leave::where('id',$id)->with('user:id,name')->first();
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
        $validation = \Validator::make($request->all(), [
            'leave_ids'      => 'required',
        ]);

        if ($validation->fails()) {
            return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        } 

        DB::beginTransaction();
        try {
            $leave = Leave::whereIn('id',$request->leave_ids)->update(['is_approved' => '1','approved_by' => Auth::id(), "approved_date" => date('Y-m-d'), 'approved_time' => date('H:i'), 'status' => 1]);

            $data = Leave::whereIn('id',$request->leave_ids)->with('user:id,name')->get();

            foreach ($data as $key => $value) {
                $schedule = Schedule::where('shift_date',$value->date)->where('user_id',$value->user_id)->first();
                if(Schedule::where('shift_date',$value->date)->where('user_id',$value->user_id)->count() > 0)
                {
                    $schedule->update(['leave_approved' => '1']);
                }
            }
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Leave','message_approve') ,$data, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
