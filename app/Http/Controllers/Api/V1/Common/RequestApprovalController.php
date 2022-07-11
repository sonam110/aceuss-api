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
                        $addRequest->requested_to = $value;
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
                            $person = PersonalInfoDuringIp::where('id',$value)->first(); 
                            if(is_object($person))
                            {
                                $person->is_approval_requested  = '1';
                                $person->save(); 
                                if($request->approval_type =='2')
                                {
                                    $user_type_id ='8';
                                    if($person->is_family_member == true){
                                        $user_type_id ='8';
                                    }
                                    if($person->is_caretaker == true){
                                        $user_type_id ='7';
                                    }
                                    if(($person->is_caretaker == true) && ($person->is_family_member == true )){
                                        $user_type_id ='10';
                                    }
                                    if($person->is_contact_person == true){
                                        $user_type_id ='9';
                                    }
                                    if($person->is_guardian == true){
                                        $user_type_id ='12';
                                    }
                                    if($person->is_other == true){
                                        $user_type_id ='15';
                                    }
                                    if($person->is_presented == true){
                                        $user_type_id ='13';
                                    }
                                    if($person->is_participated == true){
                                        $user_type_id ='14';
                                    }
                           
                                    $top_most_parent_id = auth()->user()->top_most_parent_id;
                                    $getUser = User::where('email',$person->email)->withTrashed()->first();
                                    if(empty($getUser))
                                    {
                                        $getUserType = UserType::find($user_type_id);
                                        $roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);
                                        
                                        $userSave = new User;
                                        $userSave->unique_id = generateRandomNumber();
                                        $userSave->user_type_id = $user_type_id;
                                        $userSave->branch_id = getBranchId();
                                        $userSave->role_id =  $roleInfo->id;
                                        $userSave->parent_id = $user->id;
                                        $userSave->top_most_parent_id = $top_most_parent_id;
                                        $userSave->name = $person->name ;
                                        $userSave->email = $person->email ;
                                        $userSave->password = Hash::make('12345678');
                                        $userSave->contact_number = $person->contact_number;
                                        $userSave->country_id = $person->country_id;
                                        $userSave->city = $person->city;
                                        $userSave->postal_area = $person->postal_area;
                                        $userSave->zipcode = $person->zipcode;
                                        $userSave->full_address = $person->full_address ;
                                        $userSave->save(); 

                                        if(!empty($user_type_id))
                                        {
                                           $role = $roleInfo;
                                           $userSave->assignRole($role->name);
                                        }     
                                        if(env('IS_MAIL_ENABLE',false) == true)
                                        { 
                                            $content = ([
                                                'company_id' => $userSave->top_most_parent_id,
                                                'name' => $userSave->name,
                                                'email' => $userSave->email,
                                                'id' => $userSave->id,
                                            ]);    
                                            Mail::to($userSave->email)->send(new WelcomeMail($content));
                                        }
                                    }
                                        
                                } 
                            }
                            if($request->approval_type =='1'){
                                $patientImpPlan = PatientImplementationPlan::where('id', $requestTypeId)
                                ->update([
                                    'status' => 1,
                                    'approved_by' => $value,
                                    'approved_date' => date('Y-m-d')
                                ]);
                            }
                        }

                        if($request->approval_type == '2') 
                        {
                            if($nkey==0)
                            {
                                if($request->request_type == '9' || $request->request_type == '2'){
                                    $ip = PatientImplementationPlan::where('id', $requestTypeId)->first();
                                }
                            
                                /*-----------Send notification---------------------*/

                                $data_id =  $addRequest->id;
                                $notification_template = EmailTemplate::where('mail_sms_for', 'request-approval')->first();

                                foreach ($request->requested_to as $key => $value) {
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

                    //update userId 
                    getPersonUserId($value);
                }
                if($request->approval_type == '1') {
                    $patientImpPlan = PatientImplementationPlan::whereIn('id', $request->request_type_id)->get();
                    $filename = $group_token."-".time().".pdf";
                    $data['ips'] = $patientImpPlan;
                    $pdf = PDF::loadView('print-ip', $data);
                    $pdf->save('reports/ip/'.$filename);
                    $url = env('CDN_DOC_URL').'reports/ip/'.$filename;
                    $requestApproved = RequestForApproval::whereIn('id',[$ids])->update(['status'=>'2','other_info'=> $url]);
                    return prepareResult(true,'Download PDF' ,$url, config('httpcodes.success'));
                }
                elseif($request->approval_type == '2') {
                    $url = [];
                    foreach ($request->requested_to as $key => $person) {
                        $getPersonalNumber = PersonalInfoDuringIp::find($person);
                        if($getPersonalNumber)
                        {
                            if(!empty($getPersonalNumber->personal_number))
                            {
                                $url[] = bankIdVerification($getPersonalNumber->personal_number, $person, $group_token);
                                $url[$key]['person_id'] = $person;
                                $url[$key]['group_token'] = $group_token;
                            }
                        }
                    }
                    return prepareResult(true,'Mobile BankID Link' ,$url, config('httpcodes.success'));
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
                return prepareResult(true,"All Approval Request  list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"All Approval Request  list",$query,config('httpcodes.success'));
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
                return prepareResult(false,getLangByLabelGroups('IP','message_id_not_found'), [],config('httpcodes.not_found'));
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
            return prepareResult(true,"Approved successfully",$approval_request,config('httpcodes.success'));
                
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
            return prepareResult(true,"Reject successfully",$rejest_request,config('httpcodes.success'));
            
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
