<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalaryDetail;
use Validator;
use Auth;
use Exception;
use DB;
class SalaryController extends Controller
{
    public function updateSalaryDetail(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'user_id' => 'required|exists:users,id',     
        		'salary_per_month' => 'required',     
        		'salary_package_start_date' => 'required',   
        		'salary_package_end_date' => 'required|after:salary_package_start_date',   
	        ],
            [
            'user_id.required' => getLangByLabelGroups('Salary','user_id'),
            'salary_per_month.emrequiredail' => getLangByLabelGroups('Salary','salary_per_month'),
            'salary_package_start_date.required' =>  getLangByLabelGroups('Salary','salary_package_start_date'),
            'salary_package_end_date.required' =>  getLangByLabelGroups('Salary','salary_package_end_date'),
            'salary_package_end_date.after' =>  getLangByLabelGroups('Salary','salary_package_end_date_after'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkAlready = SalaryDetail::where('user_id',$request->user_id)->first(); 
        	if($checkAlready) {
              	$salaryDetail = SalaryDetail::find($checkAlready->id);
        	} else{
        		$salaryDetail = new SalaryDetail;
        	}
	        
		 	$salaryDetail->user_id = $request->user_id;
		 	$salaryDetail->salary_per_month = $request->salary_per_month;
		 	$salaryDetail->salary_package_start_date = $request->salary_package_start_date;
		 	$salaryDetail->salary_package_end_date = $request->salary_package_end_date;
            $salaryDetail->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$salaryDetail->save();
	        return prepareResult(true, getLangByLabelGroups('Salary','update') ,$salaryDetail, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function salaryDetail(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'user_id' => 'required|exists:users,id',        
            ],
            [
            'user_id.required' => getLangByLabelGroups('Salary','user_id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkAlready = SalaryDetail::where('user_id',$request->user_id)->first(); 
            if (!is_object($checkAlready)) {
                return prepareResult(false,'User not found', [],$this->not_found);
            }
            $salaryDetail = SalaryDetail::where('user_id',$request->user_id)->with('User:id,name')->first(); 
            return prepareResult(true, getLangByLabelGroups('Salary','update') ,$salaryDetail, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
}
