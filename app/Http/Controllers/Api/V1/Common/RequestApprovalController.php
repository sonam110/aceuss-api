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
use App\Models\RequestForApproval;
use App\Models\PatientImplementationPlan;
use App\Models\PersonalInfoDuringIp;
use App\Models\User;
use PDF;

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
                'data.*.request_type'     => 'required|exists:category_types,id',      
                'data.*.request_type_id' => 'required',      
                'data.*.requested_to' => 'required',           
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
            $ids = [];
            if(is_array($request->requested_to) && sizeof($request->requested_to) >0 ){
                foreach ($request->requested_to as $key => $value) {
                    if(!empty($request->request_type))
                    {
                        $checkExist = RequestForApproval::where('requested_by', $user->id)->where('requested_to', $value)->where('request_type', $request->request_type)->where('request_type_id', $request->request_type_id)->first();
                        if($checkExist)
                        {
                            $addRequest = $checkExist;
                        }
                        else
                        {
                            $addRequest = new RequestForApproval;
                        }

                        $addRequest->requested_by = $user->id;
                        $addRequest->requested_to = $value;
                        $addRequest->request_type = $request->request_type;
                        $addRequest->request_type_id = $request->request_type_id;
                        $addRequest->reason_for_requesting = $request->reason_for_requesting;
                        $addRequest->approval_type = $request->approval_type;
                        $addRequest->save();  
                        $ids[] = $addRequest->id;
                        if($request->request_type == '2'){
                               $person = PersonalInfoDuringIp::where('id',$value)->first(); 
                               if(is_object($person)){
                                    $person->is_approval_requested  = '1';
                                    $person->save(); 
                                    if($request->approval_type =='2'){
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
                               
                                        if(auth()->user()->user_type_id=='1'){
                                            $top_most_parent_id = auth()->user()->id;
                                        }
                                        elseif(auth()->user()->user_type_id=='2')
                                        {
                                            $top_most_parent_id = auth()->user()->id;
                                        } else {
                                            $top_most_parent_id = auth()->user()->top_most_parent_id;
                                        }
                                        $getUser = User::where('email',$person->email)->withTrashed()->first();
                                        if(empty($getUser)){
                                            $userSave = new User;
                                            $userSave->unique_id = generateRandomNumber();
                                            $userSave->user_type_id = $user_type_id;
                                            $userSave->branch_id = getBranchId();
                                            $userSave->role_id =  $user_type_id;
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
                                               $role = Role::where('id',$user_type_id)->first();
                                               $userSave->assignRole($role->name);
                                            }     
                                            if(env('IS_MAIL_ENABLE',false) == true){ 
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
                                $patientImpPlan = PatientImplementationPlan::where('id',$request->request_type_id)->first();
                                $patientImpPlan->status ="1";
                                $patientImpPlan->approved_by = $value;
                                $patientImpPlan->approved_date = date('Y-m-d');
                                $patientImpPlan->save();
                            }
                        }

                        if($request->approval_type == '2') {
                            $companyObj = companySetting($user->top_most_parent_id);
                            if($request->request_type == '9' || $request->request_type == '2'){
                                $ip = PatientImplementationPlan::where('id',$request->request_type_id)->first();
                            }
                            $obj  =[
                                "type"=> 'request-approval',
                                "user_id"=> $value,
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
                                pushNotification('request-approval',$companyObj,$obj,'1','',$addRequest->id,'');
                            }
                        }
                    }
                }
                if($request->approval_type == '1') {
                    $filename = $patientImpPlan->id."-".time().".pdf";
                    $data['ip'] = $patientImpPlan;
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
                                $url[] = bankIdVerification($getPersonalNumber->personal_number, $person);
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
                return prepareResult(false,getLangByLabelGroups('IP','id_not_found'), [],config('httpcodes.not_found'));
            }
            if($approval_request->request_type == '2'){
                $ip  = PatientImplementationPlan::where('id',$approval_request->request_type_id)->first();
                $checktotalCount = RequestForApproval::where('request_type_id',$approval_request->request_type_id)->count();
                $checkAprrovalCount = RequestForApproval::where('request_type_id',$approval_request->request_type_id)->where('status','2')->count();
                if($checktotalCount == $checkAprrovalCount ){
                    $updateRequest = RequestForApproval::where('request_type_id',$approval_request->request_type_id)->update(['status'=>'2','approved_by'=>$user->id,'approved_date'=>date('Y-m-d')]);
                    $ip->status ='2';
                    $ip->approved_by = $approval_request->requested_to;
                    $ip->approved_date = date('Y-m-d');
                    $ip->save();
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
