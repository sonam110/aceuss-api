<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use Exception;
use App\Models\RequestForApproval;
use App\Models\PatientImplementationPlan;
class RequestApprovalController extends Controller
{
    public function requestForApproval(Request $request)
    {
        try {
        		$user = getUser();
                $validator = Validator::make($request->all(),[   
                    'request_type'     => 'required|exists:category_types,id',  
                    'request_type_id' => 'required',      
                    'requested_to' => 'required',      
                    'reason_for_requesting' => 'required',      
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], '422'); 
                }
               
                $addRequest = new RequestForApproval;
                $addRequest->requested_by = $user->id;
                $addRequest->requested_to = (!empty($request->requested_to)) ? $request->requested_to: $user->top_most_parent_id;
                $addRequest->request_type = $request->request_type;
                $addRequest->request_type_id = $request->request_type_id;
                $addRequest->reason_for_requesting = $request->reason_for_requesting;
                $addRequest->approval_type = $request->approval_type;
                $addRequest->save();
                if($addRequest) {
	                $id = $addRequest->id;
	                $companyObj = companySetting($user->top_most_parent_id);
	                if($request->request_type == '9' || $request->request_type == '2'){
	                	$ip = PatientImplementationPlan::where('id',$request->request_type_id)->first();
	                }
	                $obj  =[
	                    "type"=> 'request-approval',
	                    "user_id"=> $request->requested_to,
	                    "name"=> $user->name,
	                    "email"=> $user->email,
	                    "user_type"=> $user->user_type_id,
	                    "title"=> ($ip) ? $ip->title : $request->reason_for_requesting,
	                    "patient_id"=>  null,
	                    "start_date"=> '',
	                    "start_time"=> '',
	                    "company"=>  $companyObj,
	                    "company_id"=>  $user->top_most_parent_id,

	                ];
	                if(env('IS_NOTIFICATION_ENABLE')== true){
	                    pushNotification('request-approval',$companyObj,$obj,'1','',$id,'');
	                }
	               return prepareResult(true,'Added successfully' ,$addRequest, $this->success);
                } else {
               	  return prepareResult(false, 'Opps! Somthing went wrong',[], '500');
                }
            
            }
            catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),[], '500');
                
            }
       
    }

    public function approvalRequestList(Request $request){
        try {
        $query = RequestForApproval::with('RequestedBy:id,name','RequestedTo:id,name')->orderby('id','ASC');
        $whereRaw = $this->getWhereRawFromRequest($request);
        if($whereRaw != '') { 
        	$query->whereRaw($whereRaw);

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
                return prepareResult(true,"All Approval Request  list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"All Approval Request  list",$query,$this->success);
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
                
        }
        
    }

     private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('request_type')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "request_type = "."'" .$request->input('request_type')."'".")";
        }
        if (is_null($request->input('request_type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "request_type_id = "."'" .$request->input('request_type_id')."'".")";
        }
        
        return($w);

    }
}
