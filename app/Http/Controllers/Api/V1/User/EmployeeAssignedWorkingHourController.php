<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeAssignedWorkingHour;
use App\Models\UserScheduledDate;
use Validator;
use Auth;
use Exception;
use DB;
class EmployeeAssignedWorkingHourController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:employee-assigned-working-hours-browse',['except' => ['show']]);
    //     $this->middleware('permission:employee-assigned-working-hours-add', ['only' => ['store']]);
    //     $this->middleware('permission:employee-assigned-working-hours-edit', ['only' => ['update']]);
    //     $this->middleware('permission:employee-assigned-working-hours-read', ['only' => ['show']]);
    //     $this->middleware('permission:employee-assigned-working-hours-delete', ['only' => ['destroy']]);
        
    // }

	public function employeeAssignedWorkingHours(Request $request)
    {
        try 
        {
	        $query = EmployeeAssignedWorkingHour::orderBy('id', 'DESC');

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
                return prepareResult(true,"EmployeeAssignedWorkingHour list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"EmployeeAssignedWorkingHour list",$query,config('httpcodes.success'));
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
        		'emp_id' => 'required',   
	        ],
		    [
            'emp_id.required' =>  getLangByLabelGroups('EmployeeAssignedWorkingHour','message_emp_id'),
            ]);

	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}

            $assWorking = $request->assigned_working_hour_per_week;
            $workingPercent = $request->working_percent;

            $actWorking = $assWorking * $workingPercent / 100;

	        $empAssWorkHour = new EmployeeAssignedWorkingHour;
		 	$empAssWorkHour->emp_id = $request->emp_id;
            $empAssWorkHour->assigned_working_hour_per_week = $assWorking;
		 	$empAssWorkHour->working_percent = $workingPercent;
            $empAssWorkHour->actual_working_hour_per_week = $actWorking;
            $empAssWorkHour->created_by = Auth::id();
            $empAssWorkHour->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$empAssWorkHour->save();

            $data_set_update = UserScheduledDate::where('end_date','>',date('Y-m-d'))->where('emp_id',$request->emp_id)->update(['working_percent'=>$workingPercent]);
              DB::commit();
	        return prepareResult(true,getLangByLabelGroups('EmployeeAssignedWorkingHour','message_create') ,$empAssWorkHour, config('httpcodes.success'));
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function show($id)
    { 
        try 
        {
            $checkId= EmployeeAssignedWorkingHour::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('EmployeeAssignedWorkingHour','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            
            $employeeAssignedWorkingHour = EmployeeAssignedWorkingHour::where('id',$id)->with('employee:id,name')->first();
            return prepareResult(true,'View EmployeeAssignedWorkingHour',$employeeAssignedWorkingHour, config('httpcodes.success'));
                 
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try 
        {
	    	$validator = Validator::make($request->all(),[
                'emp_id' => 'required',   
            ],
            [
            'emp_id.required' =>  getLangByLabelGroups('EmployeeAssignedWorkingHour','message_emp_id'),
            ]);

            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $assWorking = $request->assigned_working_hour_per_week;
            $workingPercent = $request->working_percent;

            $actWorking = $assWorking * $workingPercent / 100;

            $empAssWorkHour = EmployeeAssignedWorkingHour::find($id);
            $empAssWorkHour->emp_id = $request->emp_id;
            $empAssWorkHour->assigned_working_hour_per_week = $assWorking;
            $empAssWorkHour->working_percent = $workingPercent;
            $empAssWorkHour->actual_working_hour_per_week = $actWorking;
            $empAssWorkHour->created_by = Auth::id();
            $empAssWorkHour->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $empAssWorkHour->save();

            $data_set_update = UserScheduledDate::where('end_date','>',date('Y-m-d'))->where('emp_id',$request->emp_id)->update(['working_percent'=>$workingPercent]);
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('EmployeeAssignedWorkingHour','message_update'),$empAssWorkHour, config('httpcodes.success'));
			    
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
        	$checkId = EmployeeAssignedWorkingHour::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('EmployeeAssignedWorkingHour','message_id_not_found'), [],config('httpcodes.not_found'));
            }
        	$checkId->delete();
         	return prepareResult(true,getLangByLabelGroups('EmployeeAssignedWorkingHour','message_delete'),[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }
}
