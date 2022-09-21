<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\WelcomeMail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\EmailTemplate;
use App\Models\UserType;
use App\Models\RequestForApproval;
use App\Models\PatientImplementationPlan;
use App\Models\PersonalInfoDuringIp;
use App\Models\User;
use PDF;
use Str;


class RequestApprovalController extends Controller
{
     public function __construct()
    {

        $this->middleware('permission:requests-browse',['only' => ['approvalRequestList']]);
        $this->middleware('permission:requests-add', ['only' => ['requestForApproval']]);
       
    }

    public function requestForApproval(Request $request)
    {
        //$request->request_type
        /*
            1: for PDF approve (normal)
            2: BankID approve
            3: first login than approve
        */
        try {
            $user = getUser();
            $data = $request->all();
            $validator = Validator::make($request->all(),[   
                'request_type'     => 'required|exists:category_types,id',      
                'request_type_id' => 'required|array|min:1',      
                'requested_to' => 'required|array|min:1',           
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
            $ids = [];
            $group_token = Str::random(10);
            if(is_array($request->requested_to) && sizeof($request->requested_to) >0 )
            {
                foreach ($request->requested_to as $key => $value) 
                {
                    foreach ($request->request_type_id as $nkey => $requestTypeId) 
                    {
                        $checkExist = RequestForApproval::where('requested_by', $user->id)
                            ->where('requested_to', $value)
                            ->where('request_type', $request->request_type)
                            ->where('request_type_id', $requestTypeId)
                            ->first();
                        if($checkExist)
                        {
                            $addRequest = $checkExist;
                            $group_token = $addRequest->group_token;
                        }
                        else
                        {
                            $addRequest = new RequestForApproval;
                            $addRequest->group_token = $group_token;
                        }

                        $addRequest->requested_by = $user->id;
                        $addRequest->requested_to = $value; //personal_info_during_ip id
                        $addRequest->request_type = $request->request_type;
                        $addRequest->request_type_id = $requestTypeId;
                        $addRequest->reason_for_requesting = $request->reason_for_requesting;
                        $addRequest->approval_type = $request->approval_type;
                        if($request->approval_type == '1') 
                        {
                            $addRequest->status = 2; //approved
                        }
                        $addRequest->save();  
                        $ids[] = $addRequest->id;
                        if($request->request_type == '2')
                        {
                            $person = PersonalInfoDuringIp::where('id', $value)->first(); 
                            if(is_object($person))
                            {
                                $person->is_approval_requested  = '1';
                                $person->save();
                            }
                        }

                        if($request->approval_type =='1')
                        {
                            $patientImpPlan = PatientImplementationPlan::where('id', $requestTypeId)
                            ->update([
                                'status' => 1,
                                'approved_by' => $value,
                                'approved_date' => date('Y-m-d')
                            ]);
                        }

                        if($request->approval_type == '3') 
                        {
                            if($nkey==0)
                            {
                                if($request->request_type == '9' || $request->request_type == '2'){
                                    $ip = PatientImplementationPlan::where('id', $requestTypeId)->first();
                                }
                            
                                /*-----------Send notification---------------------*/

                                $data_id =  $addRequest->id;
                                $notification_template = EmailTemplate::where('mail_sms_for', 'request-approval')->first();

                                foreach ($request->requested_to as $key => $value) 
                                {
                                    $userRec = User::find($value);
                                    $variable_data = [
                                        '{{name}}'              => $userRec->name,
                                        '{{requested_by}}'      => Auth::User()->name,
                                        '{{ip_title}}'          => ($ip) ? $ip->title : $request->reason_for_requesting
                                    ];
                                    actionNotification($userRec,$data_id,$notification_template,$variable_data);
                                }
                            }
                        }
                    }
                }
                if($request->approval_type == '1') 
                {
                    $patientImpPlan = PatientImplementationPlan::whereIn('id', $request->request_type_id)->get();
                    $filename = $group_token."-".time().".pdf";
                    $data['ips'] = $patientImpPlan;
                    $pdf = PDF::loadView('print-ip', $data);
                    $pdf->save('reports/ip/'.$filename);
                    $url = env('CDN_DOC_URL').'reports/ip/'.$filename;
                    $requestApproved = RequestForApproval::whereIn('id',$ids)->update(['status'=>'2','other_info'=> $url]);
                    return prepareResult(true,'Download PDF' ,$url, config('httpcodes.success'));
                }
                elseif($request->approval_type == '2') 
                {
                    $url = [];
                    foreach ($request->requested_to as $key => $person) {
                        $getPersonalTableInfo = PersonalInfoDuringIp::find($person);
                        if(!empty($getPersonalTableInfo) && !empty($getPersonalTableInfo->user))
                        {
                            $getPersonalNumber = $getPersonalTableInfo->user;
                            if(!empty($getPersonalNumber->personal_number))
                            {
                                $top_most_parent_id = $getPersonalNumber->top_most_parent_id;
                                $response = bankIdVerification($getPersonalNumber->personal_number, $person, $group_token, $user->id, 'IP-approval', $top_most_parent_id, 1, null);
                                /*if($response['error']==1) 
                                {
                                    return prepareResult(false, $response,$response, config('httpcodes.internal_server_error'));
                                }*/
                                $url[] = $response;
                                $url[$key]['person_id'] = $person;
                                $url[$key]['group_token'] = $group_token;
                                $url[$key]['uniqueId'] = $user->unique_id;
                            }
                        }
                    }
                    return prepareResult(true,'Mobile BankID Link', $url, config('httpcodes.success'));
                }
                $getRequest = RequestForApproval::whereIn('id',$ids)->get();
               return prepareResult(true,'Send successfully' ,$getRequest, config('httpcodes.success'));
            
            } else {
              return prepareResult(false, 'Opps! Somthing went wrong',[], '500');
            }
        
        }
        catch(Exception $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    public function approvalRequestList(Request $request)
    {
        try {
            $query = RequestForApproval::with('RequestedBy:id,name','RequestedTo:id,name','ApprovedBy:id,name')->orderby('id','ASC');
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
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }

    public function approvedRequest(Request $request,$id)
    {
        try {
            $user = getUser();
            $approval_request =  RequestForApproval::where('id',$id)->first();
            $is_approved = false;
            if (!is_object($approval_request)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $group_token = $approval_request->group_token;

            if($approval_request->request_type == '2')
            {                
                //update status
                $requestForApproval = RequestForApproval::where('group_token', $group_token)
                ->where('requested_to', $user->id)
                ->update([
                    'status' => 2
                ]);

                $checkTotalPerson = RequestForApproval::select(\DB::raw('COUNT(id) as total_request_for_approve'),
            \DB::raw('COUNT(IF(status = 2, 0, NULL)) as total_approved'))
                ->where('group_token', $group_token)
                ->first();

                if($checkTotalPerson->total_request_for_approve == $checkTotalPerson->total_approved)
                {
                    $ip_ids = RequestForApproval::where('group_token', $group_token)
                        ->groupBy('request_type_id')
                        ->pluck('request_type_id');

                    //update IP as Approved
                    PatientImplementationPlan::whereIn('id', $ip_ids)
                    ->update([
                        'status' => 1,
                        'approved_date' => date('Y-m-d')
                    ]);
                }
            }

            if($approval_request->request_type == '9'){

                $approved = RequestForApproval::find($id);
                $approved->status ='2';
                $approved->approved_by = $user->id;
                $approved->approved_date = date('Y-m-d');
                $approved->save();
                $getUser = User::where('id',$approval_request->requested_by)->first();
                if($getUser){
                    $user_role = Role::where('id',$getUser->role_id)->first();
                    $user_role->givePermissionTo(['isCategoryEditPermission-edit']);
                }
            }
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_approve'),$approval_request,config('httpcodes.success'));
                
            }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    public function rejectRequest(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[   
                'id'     => 'required|exists:request_for_approvals,id',      
                'reason_for_rejection' => 'required',             
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
            $rejest_request =  RequestForApproval::where('id',$request->id)->first();
            $updateRequest = RequestForApproval::where('id',$request->id)->update(['status'=>'3','rejected_by'=>$user->id,'reason_for_rejection'=> $request->reason_for_rejection]);
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_reject'),$rejest_request,config('httpcodes.success'));
            
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';
        if (is_null($request->input('request_type')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "request_type = "."'" .$request->input('request_type')."'".")";
        }
        if (is_null($request->input('request_type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "request_type_id = "."'" .$request->input('request_type_id')."'".")";
        }
        if (is_null($request->input('requested_by')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "requested_by = "."'" .$request->input('requested_by')."'".")";
        }
        if (is_null($request->input('requested_to')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "requested_to = "."'" .$request->input('requested_to')."'".")";
        }
        return($w);
    }
}
