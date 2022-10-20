<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\EmployeeBranch;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Exception;
use Mail;
use Str;

class EmployeeBranchController extends Controller
{
    protected $top_most_parent_id;

    public function __construct()
    {
        $this->middleware('permission:users-add');
        
        $this->middleware(function ($request, $next) {
            $this->top_most_parent_id = auth()->user()->top_most_parent_id;
            return $next($request);
        });
    }

    public function assignEmployeeToBranches(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(),[
                'branch_ids' => 'required|array|min:1|exists:users,id',
                'employee_id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $removedPreAssigned = EmployeeBranch::where('employee_id', $request->employee_id)->delete();

            foreach ($request->branch_ids as $key => $branch_id) 
            {
                if($key==0)
                {
                    $employee = User::find($request->employee_id);
                    $employee->branch_id = $branch_id;
                    $employee->save();
                }
                $assignBranch = new EmployeeBranch;
                $assignBranch->employee_id = $request->employee_id;
                $assignBranch->branch_id = $branch_id;
                $assignBranch->save();
                $assignBranch['branch'] = $assignBranch->branch()->select('id', 'name', 'branch_name')->get()->makeHidden(['company_types','patient_types','on_vacation']);
                $assignBranch['employee'] = $assignBranch->employee()->select('id', 'name')->get()->makeHidden(['company_types','patient_types','on_vacation']);
                $data[] = $assignBranch;
            }

            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_assign') ,$data, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function assignedEmployeeToBranches(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'employee_id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $assignedBranches = EmployeeBranch::select('id','employee_id','branch_id')
                ->where('employee_id', $request->employee_id)
                ->with('branch:id,name,branch_name,branch_email', 'employee:id,name')
                ->get();

            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list') ,$assignedBranches, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function assignedBranchToEmployees(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'branch_id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $assignedBranches = EmployeeBranch::select('id','employee_id','branch_id')
                ->where('branch_id', $request->branch_id)
                ->with('branch:id,name,branch_name,branch_email', 'employee:id,name,email')
                ->get();

            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list') ,$assignedBranches, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function switchBranch(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'branch_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $checkBranch = EmployeeBranch::where('branch_id', $request->branch_id)
                ->where('employee_id', $user->id)
                ->count();
            if($checkBranch>0 && $user->user_type_id==3)
            {
                $user->branch_id = $request->branch_id;
                $user->save();
                return prepareResult(true,getLangByLabelGroups('BcCommon','message_update') ,[], config('httpcodes.success'));
            }
            return prepareResult(false, getLangByLabelGroups('BcCommon','branch_not_assigned_to_you_cant_access_data'),[], config('httpcodes.internal_server_error'));
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
