<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\IpAssigneToEmployee;
use App\Models\IpTemplate;
use App\Models\IpFollowUpCreation;
use App\Models\PersonalInfoDuringIp;
use App\Models\Activity;
use Validator;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\WelcomeMail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\EmailTemplate;
use PDF;

class PatientController extends Controller
{
    public function ipsList(Request $request)
    {
        try {
            $user = getUser();
            $whereRaw = $this->getWhereRawFromRequest($request);
            $parent_id = PatientImplementationPlan::whereNotNull('parent_id')->orderBy('id','DESC')->groupBy('parent_id')->pluck('parent_id')->implode(',');
            $child_id  = [];
            $ip_without_parent = PatientImplementationPlan::whereNull('parent_id')->whereNotIn('id',explode(',',$parent_id))->pluck('id')->all();
            foreach (explode(',',$parent_id) as $key => $value) {
              $lastChild = PatientImplementationPlan::where('parent_id',$value)->orderBy('id','DESC')->first();
              $child_id[] = (!empty($value)) ? $lastChild->id : null;
            }
            $ip_ids = array_merge($ip_without_parent,$child_id);
            if($whereRaw != '') { 
                
                $query =PatientImplementationPlan::whereIn('id',$ip_ids)->whereRaw($whereRaw)
                ->with('patient','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name')
                ->orderBy('id', 'DESC');
            } else {
                $query =PatientImplementationPlan::whereIn('id',$ip_ids)->with('patient','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name')
                ->orderBy('id', 'DESC');
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
                return prepareResult(true,"Ip list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Ip list",$query,$this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
        
    }
    public function store(Request $request){
        try {
           
            $user = getUser();
            
            $data = [ 'data' => $request->all() ];
            $validator = Validator::make($data,[ 
                'data.*.category_id' => 'required|exists:category_masters,id',   
                'data.*.subcategory_id' => 'required|exists:category_masters,id',   
                'data.*.title' => 'required',       
 
            ],
            [   
            '*.category_id' =>  getLangByLabelGroups('IP','category_id'),   
            '*.subcategory_id' =>  getLangByLabelGroups('IP','subcategory_id'),             
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $ids = null;
            $impPlan_ids = [];
            
            if(is_array($data['data']) ){
                foreach ($data['data'] as $key => $patient) {
                    $patientPlan = new PatientImplementationPlan;
                    $patientPlan->user_id = $patient['user_id'];
                    $patientPlan->branch_id = getBranchId();
                    $patientPlan->category_id = $patient['category_id'];
                    $patientPlan->subcategory_id = $patient['subcategory_id'];
                    $patientPlan->title = $patient['title'];
                    $patientPlan->goal = $patient['goal'];
                    $patientPlan->limitations = $patient['limitations'];
                    $patientPlan->limitation_details = $patient['limitation_details'];
                    $patientPlan->how_support_should_be_given = $patient['how_support_should_be_given'];
                    $patientPlan->who_give_support =  json_encode($patient['who_give_support']);
                    $patientPlan->sub_goal = $patient['sub_goal'];
                    $patientPlan->sub_goal_details = $patient['sub_goal_details'];
                    $patientPlan->sub_goal_selected = $patient['sub_goal_selected'];
                    $patientPlan->overall_goal = $patient['overall_goal'];
                    $patientPlan->overall_goal_details = $patient['overall_goal_details'];
                    $patientPlan->body_functions = $patient['body_functions'];
                    $patientPlan->personal_factors = $patient['personal_factors'];
                    $patientPlan->health_conditions = $patient['health_conditions'];
                    $patientPlan->other_factors = $patient['other_factors'];
                    $patientPlan->treatment = $patient['treatment'];
                    $patientPlan->working_method = $patient['working_method'];
                    $patientPlan->start_date = $patient['start_date'];
                    $patientPlan->end_date = $patient['end_date'];
                    $patientPlan->save_as_template = ($patient['save_as_template']) ? 1:0;
                    $patientPlan->documents = json_encode($patient['documents']);
                    $patientPlan->created_by = $user->id;
                    $patientPlan->save();

                    $impPlan_ids[] = $patientPlan->id;
                    $ids = implode(', ',$impPlan_ids);
                    if($patient['save_as_template'] == true){
                        
                        if (empty($patient['title'])) {
                            return prepareResult(false,'Title field is required',[], $this->unprocessableEntity); 
                        }
                        $ipTemplate = new IpTemplate;
                        $ipTemplate->ip_id = $patientPlan->id;
                        $ipTemplate->template_title = $patient['title'];
                        $ipTemplate->created_by = $user->id;
                        $ipTemplate->save();
                    }
                    /*-----------IP assigne to employee*/
                    if(!empty($patient['emp_id']) ){
                        $ipAssigne = new IpAssigneToEmployee;
                        $ipAssigne->user_id = $patient['emp_id'];
                        $ipAssigne->ip_id = $patientPlan->id;
                        $ipAssigne->status = '1';
                        $ipAssigne->save();


                    }
                    /*-----------------Persons Informationn ----------------*/
                    if(is_array($patient['persons']) ){
                        foreach ($patient['persons'] as $key => $value) {
                            $is_user = false;
                            if($value['is_family_member'] == true){
                                $user_type_id ='8';
                                $is_user = true;
                            }
                            if($value['is_caretaker'] == true){
                                $user_type_id ='7';
                                $is_user = true;
                            }
                            if(($value['is_caretaker'] == true) && ($value['is_family_member'] == true )){
                                $user_type_id ='10';
                                $is_user = true;
                            }
                            if($value['is_contact_person'] == true){
                                $user_type_id ='9';
                                $is_user = true;
                            }


                            if(is_null($value['id']) == false){
                                $personalInfo = PersonalInfoDuringIp::find($value['id']);
                                $getperson = PersonalInfoDuringIp::where('id',$value['id'])->first();
                                $getUser = User::where('email',$getperson->email)->first();
                            } else{
                                $personalInfo = new PersonalInfoDuringIp;
                            }
                            $personalInfo->patient_id = $patient['user_id'];
                            $personalInfo->ip_id =$patientPlan->id;
                            $personalInfo->name = $value['name'] ;
                            $personalInfo->email = $value['email'] ;
                            $personalInfo->contact_number = $value['contact_number'];
                            $personalInfo->country_id = $value['country_id'];
                            $personalInfo->city = $value['city'];
                            $personalInfo->postal_area = $value['postal_area'];
                            $personalInfo->zipcode = $value['zipcode'];
                            $personalInfo->full_address = $value['full_address'] ;
                            $personalInfo->is_family_member = ($value['is_family_member'] == true) ? $value['is_family_member'] : 0 ;
                            $personalInfo->is_caretaker = ($value['is_caretaker'] == true) ? $value['is_caretaker'] : 0 ;
                            $personalInfo->is_contact_person = ($value['is_contact_person'] == true) ? $value['is_contact_person'] : 0 ;
                            $personalInfo->is_guardian = ($value['is_guardian'] == true) ? $value['is_guardian'] : 0 ;
                            $personalInfo->is_other = ($value['is_other'] == true) ? $value['is_other'] : 0 ;
                            $personalInfo->is_presented = ($value['is_presented'] == true) ? $value['is_presented'] : 0 ;
                            $personalInfo->is_participated = ($value['is_participated'] == true) ? $value['is_participated'] : 0 ;
                            $personalInfo->how_helped = $value['how_helped'];
                            $personalInfo->is_other_name = $value['is_other_name'];
                            $personalInfo->save() ;
                            /*-----Create Account /Entry in user table*/
                            if($is_user == true) {
                                if(auth()->user()->user_type_id=='1'){
                                    $top_most_parent_id = auth()->user()->id;
                                }
                                elseif(auth()->user()->user_type_id=='2')
                                {
                                    $top_most_parent_id = auth()->user()->id;
                                } else {
                                    $top_most_parent_id = auth()->user()->top_most_parent_id;
                                }
                                $checkAlreadyUser = User::where('email',$value['email'])->first();
                                if(empty($checkAlreadyUser)) {
                                    if(!empty($getUser)){
                                        $userSave = User::find($getUser->id);
                                    } else {
                                        $userSave = new User;
                                        $userSave->unique_id = generateRandomNumber();
                                    }
                                   
                                    $userSave->user_type_id = $user_type_id;
                                    $userSave->branch_id = getBranchId();
                                    $userSave->role_id =  $user_type_id;
                                    $userSave->parent_id = $user->id;
                                    $userSave->top_most_parent_id = $top_most_parent_id;
                                    $userSave->name = $value['name'] ;
                                    $userSave->email = $value['email'] ;
                                    $userSave->password = Hash::make('12345678');
                                    $userSave->contact_number = $value['contact_number'];
                                    $userSave->country_id = $value['country_id'];
                                    $userSave->city = $value['city'];
                                    $userSave->postal_area = $value['postal_area'];
                                    $userSave->zipcode = $value['zipcode'];
                                    $userSave->full_address = $value['full_address'] ;
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
                    }
                }
                $patientImpPlan = PatientImplementationPlan::whereIn('id',$impPlan_ids)->get();
                return prepareResult(true,getLangByLabelGroups('IP','create') ,$patientImpPlan, $this->success);
            } else {
                return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            }
            
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
            $user = getUser();
            $data = [ 'data' => $request->all() ];
            $validator = Validator::make($data,[  
                'data.*.category_id' => 'required|exists:category_masters,id',   
                'data.*.subcategory_id' => 'required|exists:category_masters,id',   
                'data.*.title' => 'required',      
                'data.*.reason_for_editing' => 'required',      
            ],
            [    
            '*.category_id' =>  getLangByLabelGroups('IP','category_id'),   
            '*.subcategory_id' =>  getLangByLabelGroups('IP','subcategory_id'),        
            ]);
           
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkId = PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('IP','id_not_found'), [],$this->not_found);
            }
            $ids = null;
            $impPlan_ids = [];
            $parent_id  = (empty($checkId->parent_id)) ? $id : $checkId->parent_id;

            if(!$user->hasPermissionTo('isCategoryEditPermission-edit')){
               return prepareResult(false,'You are not authorized to edit IP', [],$this->not_found);     
            } 
            if(is_array($data['data']) ){
                foreach ($data['data'] as $key => $patient) {
                    $patientPlan = new PatientImplementationPlan;
                    $patientPlan->user_id = $patient['user_id'];
                    $patientPlan->parent_id = $parent_id;
                    $patientPlan->branch_id = getBranchId();
                    $patientPlan->category_id = $patient['category_id'];
                    $patientPlan->subcategory_id = $patient['subcategory_id'];
                    $patientPlan->title = $patient['title'];
                    $patientPlan->goal = $patient['goal'];
                    $patientPlan->limitations = $patient['limitations'];
                    $patientPlan->limitation_details = $patient['limitation_details'];
                    $patientPlan->how_support_should_be_given = $patient['how_support_should_be_given'];
                    $patientPlan->who_give_support =  json_encode($patient['who_give_support']);
                    $patientPlan->sub_goal = $patient['sub_goal'];
                    $patientPlan->sub_goal_details = $patient['sub_goal_details'];
                    $patientPlan->sub_goal_selected = $patient['sub_goal_selected'];
                    $patientPlan->overall_goal = $patient['overall_goal'];
                    $patientPlan->overall_goal_details = $patient['overall_goal_details'];
                    $patientPlan->body_functions = $patient['body_functions'];
                    $patientPlan->personal_factors = $patient['personal_factors'];
                    $patientPlan->health_conditions = $patient['health_conditions'];
                    $patientPlan->other_factors = $patient['other_factors'];
                    $patientPlan->treatment = $patient['treatment'];
                    $patientPlan->working_method = $patient['working_method'];
                    $patientPlan->reason_for_editing = $patient['reason_for_editing'];
                    $patientPlan->start_date = $patient['start_date'];
                    $patientPlan->end_date = $patient['end_date'];
                    $patientPlan->save_as_template = ($patient['save_as_template']) ? 1:0;
                    $patientPlan->documents = json_encode($patient['documents']);
                    $patientPlan->edited_by = $user->id;
                    $patientPlan->save();

                    $impPlan_ids[] = $patientPlan->id;
                    $ids = implode(', ',$impPlan_ids);
                    if($patient['save_as_template'] == true){
                        if (empty($patient['title'])) {
                            return prepareResult(false,'Title field is required',[], $this->unprocessableEntity); 
                        }
                        $ipTemplate = new IpTemplate;
                        $ipTemplate->ip_id = $patientPlan->id;
                        $ipTemplate->template_title = $patient['title'];
                        $ipTemplate->created_by = $user->id;
                        $ipTemplate->save();
                    }
                    /*-----------IP assigne to employee*/
                    if(!empty($patient['emp_id']) ){
                        $ipAssigne = new IpAssigneToEmployee;
                        $ipAssigne->user_id = $patient['emp_id'];
                        $ipAssigne->ip_id = $patientPlan->id;
                        $ipAssigne->status = '1';
                        $ipAssigne->save();


                    }

                    /*------Check ip behalf actvity-----*/

                    $checkActivity = Activity::where('ip_id',$id)->get();
                    if(!empty($checkActivity)){
                        foreach ($checkActivity as $key => $activity) {
                            $updateCat = Activity::find($activity->id);
                            $updateCat->category_id = $patient['category_id'];
                            $updateCat->subcategory_id = $patient['subcategory_id'];
                            $updateCat->save();
                        }
                    }
                    /*-----------------Persons Informationn ----------------*/
                    if(is_array($patient['persons']) ){
                        foreach ($patient['persons'] as $key => $value) {
                            $is_user = false;
                            if($value['is_family_member'] == true){
                                $user_type_id ='8';
                                $is_user = true;
                            }
                            if($value['is_caretaker'] == true){
                                $user_type_id ='7';
                                $is_user = true;
                            }
                            if(($value['is_caretaker'] == true) && ($value['is_family_member'] == true )){
                                $user_type_id ='10';
                                $is_user = true;
                            }
                            if($value['is_contact_person'] == true){
                                $user_type_id ='9';
                                $is_user = true;
                            }
                            if(is_null($value['id']) == false){
                                $personalInfo = PersonalInfoDuringIp::find($value['id']);
                                $getperson = PersonalInfoDuringIp::where('id',$value['id'])->first();
                                $getUser = User::where('email',$getperson->email)->first();
                            } else{
                                $personalInfo = new PersonalInfoDuringIp;
                            }
                            $personalInfo->patient_id = $patient['user_id'];
                            $personalInfo->ip_id =$patientPlan->id;
                            $personalInfo->name = $value['name'] ;
                            $personalInfo->email = $value['email'] ;
                            $personalInfo->contact_number = $value['contact_number'];
                            $personalInfo->country_id = $value['country_id'];
                            $personalInfo->city = $value['city'];
                            $personalInfo->postal_area = $value['postal_area'];
                            $personalInfo->zipcode = $value['zipcode'];
                            $personalInfo->full_address = $value['full_address'] ;
                            $personalInfo->is_family_member = ($value['is_family_member'] == true) ? $value['is_family_member'] : 0 ;
                            $personalInfo->is_caretaker = ($value['is_caretaker'] == true) ? $value['is_caretaker'] : 0 ;
                            $personalInfo->is_contact_person = ($value['is_contact_person'] == true) ? $value['is_contact_person'] : 0 ;
                            $personalInfo->is_guardian = ($value['is_guardian'] == true) ? $value['is_guardian'] : 0 ;
                            $personalInfo->is_other = ($value['is_other'] == true) ? $value['is_other'] : 0 ;
                            $personalInfo->is_presented = ($value['is_presented'] == true) ? $value['is_presented'] : 0 ;
                            $personalInfo->is_participated = ($value['is_participated'] == true) ? $value['is_participated'] : 0 ;
                            $personalInfo->how_helped = $value['how_helped'];
                            $personalInfo->is_other_name = $value['is_other_name'];
                            $personalInfo->save();
                            /*-----Create Account /Entry in user table*/
                            if($is_user == true) {
                                if(auth()->user()->user_type_id=='1'){
                                    $top_most_parent_id = auth()->user()->id;
                                }
                                elseif(auth()->user()->user_type_id=='2')
                                {
                                    $top_most_parent_id = auth()->user()->id;
                                } else {
                                    $top_most_parent_id = auth()->user()->top_most_parent_id;
                                }
                                $checkAlreadyUser = User::where('email',$value['email'])->first();
                                if(empty($checkAlreadyUser)) {
                                    if(!empty($getUser)){
                                        $userSave = User::find($getUser->id);
                                    } else {
                                        $userSave = new User;
                                        $userSave->unique_id = generateRandomNumber();
                                    }
                                    $userSave->unique_id = generateRandomNumber();
                                    $userSave->branch_id = getBranchId();
                                    $userSave->user_type_id = $user_type_id;
                                    $userSave->role_id =  $user_type_id;
                                    $userSave->parent_id = $user->id;
                                    $userSave->top_most_parent_id = $top_most_parent_id;
                                    $userSave->name = $value['name'] ;
                                    $userSave->email = $value['email'] ;
                                    $userSave->password = Hash::make('12345678');
                                    $userSave->contact_number = $value['contact_number'];
                                    $userSave->country_id = $value['country_id'];
                                    $userSave->city = $value['city'];
                                    $userSave->postal_area = $value['postal_area'];
                                    $userSave->zipcode = $value['zipcode'];
                                    $userSave->full_address = $value['full_address'] ;
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
                    }
                }

                $patientImpPlan = PatientImplementationPlan::whereIn('id',$impPlan_ids)->get();
                return prepareResult(true,getLangByLabelGroups('IP','create') ,$patientImpPlan, $this->success);
            } else {
                return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            }
              
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
        try {
            $user = getUser();
            $checkId= PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','id_not_found'), [],$this->not_found);
            }
            
            $patientPlan = PatientImplementationPlan::where('id',$id)->delete();
            return prepareResult(true,getLangByLabelGroups('IP','delete') ,[], $this->success);
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }
    public function show($id){
        try {
            $user = getUser();
            $checkId= PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','id_not_found'), [],$this->not_found);
            }

            $patientPlan = PatientImplementationPlan::where('id',$id)->with('Parent','Category:id,name','Subcategory:id,name','CreatedBy:id,name','EditedBy:id,name','ApprovedBy:id,name','patient','persons.Country','children','assignEmployee:id,ip_id,user_id')->first();
            return prepareResult(true,'View Patient plan' ,$patientPlan, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function approvedPatientPlan(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'id' => 'required',   
            ],
            [
            'id' =>  getLangByLabelGroups('IP','id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $id = $request->id;
            $checkId= PatientImplementationPlan::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','id_not_found'), [],$this->not_found);
            }
            $patientPlan = PatientImplementationPlan::find($id);
            $patientPlan->approved_by = $user->id;
            $patientPlan->approved_date = date('Y-m-d');
            $patientPlan->status = '1';
            $patientPlan->save();
            return prepareResult(true,getLangByLabelGroups('IP','approve') ,$patientPlan, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

     public function ipAssigneToEmployee(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'user_id' => 'required',   
                'ip_id' => 'required',     
            ],
            [
            'user_id.required' => getLangByLabelGroups('IP','user_id'),
            'ip_id.required' => getLangByLabelGroups('IP','ip_id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkAlready = IpAssigneToEmployee::where('user_id',$request->user_id)->where('ip_id',$request->ip_id)->first(); 
            if($checkAlready) {
                return prepareResult(false,getLangByLabelGroups('IP','patient_already_assigne'),[], $this->unprocessableEntity); 
            }
            
            $ipAssigne = new IpAssigneToEmployee;
            $ipAssigne->user_id = $request->user_id;
            $ipAssigne->ip_id = $request->ip_id;
            $ipAssigne->status = '1';
            $ipAssigne->save();
            $ipAssigneEmp = IpAssigneToEmployee::where('id',$ipAssigne->id)->with('User:id,name','PatientImplementationPlan')->first();
            return prepareResult(true,getLangByLabelGroups('IP','assigne') ,$ipAssigneEmp, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
     public function viewIpAssigne(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'id' => 'required',   
            ],
            [
            'id' =>  getLangByLabelGroups('IP','id'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $id = $request->id;
            $checkId= IpAssigneToEmployee::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','id_not_found'), [],$this->not_found);
            }
            $ipAssigne = IpAssigneToEmployee::where('id',$id)->with('User:id,name','PatientImplementationPlan')->first();
            return prepareResult(true,'View assigne ip' ,$ipAssigne, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function patientPersonList(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'patient_id' => 'required',   
            ],
            [
            'patient_id' =>  'Patient Id is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $id = $request->patient_id;
            $whereRaw = $this->getWhereRawFromRequest1($request);
            if($whereRaw != '') { 
                $query= PersonalInfoDuringIp::where('patient_id',$id)->whereRaw($whereRaw)->with('Country');
            } else {
                $query= PersonalInfoDuringIp::where('patient_id',$id)->with('Country');
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
                return prepareResult(true,"Person list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,'Person List' ,$query, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function ipEditHistory(Request $request){
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'parent_id' => 'required|exists:patient_implementation_plans,id',   
            ],
            [
            'parent_id' =>  'Parent id is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $id = $request->parent_id;
            $parent_id = 
            $query= PatientImplementationPlan::where('parent_id',$id);
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
                return prepareResult(true,"Edited Ip list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,'Edited Ip List' ,$query, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

     public function ipTemplateList(Request $request){
        try {

            $whereRaw = $this->getWhereRawFromRequest1($request);
            if($whereRaw != '') { 
                $query= IpTemplate::select('id','ip_id','template_title')->whereRaw($whereRaw)->orderBy('id','DESC');
            } else {
                $query= IpTemplate::select('id','ip_id','template_title')->orderBy('id','DESC');
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
                return prepareResult(true,"Edited Ip list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,' Ip Template List' ,$query, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "id = "."'" .$request->input('ip_id')."'".")";
        }
        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        if (is_null($request->input('user_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "user_id = "."'" .$request->input('user_id')."'".")";
        }
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        if (is_null($request->input('category_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "category_id = "."'" .$request->input('category_id')."'".")";
        }
         if (is_null($request->input('subcategory_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "subcategory_id = "."'" .$request->input('subcategory_id')."'".")";
        }
        if (is_null($request->input('goal')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "goal_id like '%" .trim(strtolower($request->input('goal_id'))) . "%')";

             
        }
        return($w);

    }
     private function getWhereRawFromRequest1(Request $request) {
        $w = '';
        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "ip_id = "."'" .$request->input('ip_id')."'".")";
        }
         if (is_null($request->input('created_by')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "created_by = "."'" .$request->input('created_by')."'".")";
        }
        
        return($w);

    }

    public function ipFollowupsPrint(Request $request, $ip_id)
    {
        try {
            $user = getUser();
            $checkId= PatientImplementationPlan::where('id',$ip_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('IP','id_not_found'), [],$this->not_found);
            }

            $patientPlan = PatientImplementationPlan::where('id',$ip_id)->with('Parent','Category','Subcategory','CreatedBy','patient','persons.Country','children')->first();
            $filename = $patientPlan->id."-".time().".pdf";
            $data['ipfollowupInfo'] = $patientPlan; 
            $data['bankid_verified'] = $request->bankid_verified;
            $pdf = PDF::loadView('print-followups', $data);
            $pdf->save('reports/followups/'.$filename);

            $returnData = [
                'url' => env('CDN_DOC_URL').'reports/followups/'.$filename
            ];

            return prepareResult(true,'Print FollowUp',$returnData, $this->success);
        }
        catch(Exception $exception) {
            \Log::info($exception);
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    
}
