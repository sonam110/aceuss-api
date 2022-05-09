<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\PatientCashier;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Exception;

class PatientCashierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:patient_cashiers',['except' => ['show']]);
        $this->middleware('permission:patient_cashier-add', ['only' => ['store']]);
    }

    public function patientCashiers(Request $request)
    {
        try {
            $user = getUser();
            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = PatientCashier::with('Patient:id,name','CreatedBy:id,name','Branch:id,name');

            if($user->user_type_id !='2') {
                $query =  $query->whereIn('branch_id',$allChilds);
            }

            if(!empty($request->branch_id))
            {
                $query->where('branch_id', $request->branch_id);
            }

            if(!empty($request->receipt_no))
            {
                $query->where('receipt_no', 'LIKE' ,'%'.$request->receipt_no.'%');
            }

            if(!empty($request->patient_id))
            {
                $query->where('patient_id', $request->patient_id);
            }

            if(!empty($request->type))
            {
                $query->where('type', $request->type);
            }

            if(!empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereDate('date', '>=', $request->from_date)->whereDate('date', '<=', $request->end_date);
            }
            elseif(!empty($request->from_date) && empty($request->end_date))
            {
                $query->whereDate('date', $request->from_date);
            }
            elseif(empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereDate('date', '<=', $request->end_date);
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
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Patient Cashier list",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[  
                'patient_id' => 'required|exists:users,id',  
                'receipt_no' => 'required',  
                'date' => 'required|date',  
                'type' => 'required|in:1,2',       
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            
            $patient_cashier = new PatientCashier;
            $patient_cashier->branch_id = getBranchId();
            $patient_cashier->patient_id = $request->patient_id;
            $patient_cashier->receipt_no = $request->receipt_no;
            $patient_cashier->date = $request->date;
            $patient_cashier->type = $request->type;
            $patient_cashier->amount = $request->amount;
            $patient_cashier->file = $request->file;
            $patient_cashier->comment = $request->comment;
            $patient_cashier->created_by = auth()->id();
            $patient_cashier->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $patient_cashier->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Patient Cashier','create') ,$patient_cashier, config('httpcodes.success'));
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}