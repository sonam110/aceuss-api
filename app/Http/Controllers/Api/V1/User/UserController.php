<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\package;
use App\Models\Subscription;
use App\Models\EmailTemplate;
use App\Models\PersonalInfoDuringIp;
use App\Models\UserType;
use App\Models\AgencyWeeklyHour;
use App\Models\PatientInformation;
use App\Models\PatientEmployee;
use Validator;
use Auth;
use DB;
use Exception;
use Mail;
use Str;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Hash;
use App\Models\LicenceKeyManagement;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\EmployeeAssignedWorkingHour;
use App\Models\Schedule;
use App\Models\UserScheduledDate;
use App\Models\Task;
use App\Models\Activity;
use App\Models\AssigneModule;
use App\Models\AssignTask;
use App\Models\ActivityAssigne;
use App\Models\Stampling;
use App\Models\PatientImplementationPlan;
use App\Models\IpFollowUp;
use App\Models\Journal;
use App\Models\Deviation;
use App\Models\EmployeeBranch;
use App\Models\PatientCashier;

class UserController extends Controller
{
    protected $top_most_parent_id;

    public function __construct()
    {

        /*$this->middleware('permission:users-browse patients-browse',['only' => ['show']]);
        $this->middleware('permission:users-add', ['only' => ['store']]);
        $this->middleware('permission:users-edit', ['only' => ['update']]);
        $this->middleware('permission:users-read', ['only' => ['show']]);
        $this->middleware('permission:users-delete', ['only' => ['destroy']]);*/
        
        $this->middleware(function ($request, $next) {
            $this->top_most_parent_id = auth()->user()->top_most_parent_id;
            return $next($request);
        });
    }

    public function users(Request $request)
    {
        try {
            $user = getUser();
            $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
            if(!empty($user->branch_id)) {
                if($user->user_type_id==11)
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                    $allChilds[] = $user->id;
                }
                else
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                }
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
            $query = User::select('users.id','users.unique_id','users.custom_unique_id','users.user_type_id', 'users.company_type_id','users.patient_type_id','users.avatar', 'users.category_id', 'users.top_most_parent_id', 'users.parent_id','users.branch_id','users.country_id','users.city', 'users.dept_id', 'users.govt_id','users.name','users.branch_name','users.branch_email', 'users.email', 'users.email_verified_at','users.contact_number','users.user_color', 'users.gender','users.organization_number', 'users.personal_number','users.joining_date','users.is_fake','users.is_secret','users.employee_type','users.is_password_change','users.status','users.step_one','users.step_two','users.step_three','users.step_four','users.step_five','users.report_verify','users.verification_method', 
                DB::raw("(SELECT count(*) from patient_implementation_plans WHERE patient_implementation_plans.user_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") ipCount"), 
                DB::raw("(SELECT count(activity_assignes.id) FROM activity_assignes JOIN activities ON activity_assignes.activity_id = activities.id WHERE activity_assignes.user_id = users.id AND activity_assignes.deleted_at IS NULL AND activities.deleted_at IS NULL AND activities.is_latest_entry = 1) assignActivityCount"), 
                DB::raw("(SELECT count(*) from activities WHERE activities.patient_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") patientActivityCount"), 
                DB::raw("(SELECT count(assign_tasks.id) from assign_tasks JOIN tasks ON assign_tasks.task_id = tasks.id  WHERE assign_tasks.user_id = users.id AND assign_tasks.deleted_at IS NULL AND tasks.deleted_at IS NULL AND tasks.is_latest_entry = 1) assignTaskCount"), 
                DB::raw("(SELECT count(*) from tasks WHERE tasks.patient_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") patientTaskCount"), 
                DB::raw("(SELECT count(*) from patient_implementation_plans WHERE patient_implementation_plans.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchIpCount"), 
                DB::raw("(SELECT count(*) from activities WHERE activities.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchActivityCount"), 
                DB::raw("(SELECT count(*) from tasks WHERE tasks.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchTaskCount"),
                DB::raw("(SELECT count(*) from ip_follow_ups WHERE ip_follow_ups.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchFollowupCount"), 
                DB::raw("(SELECT count(*) from personal_info_during_ips WHERE personal_info_during_ips.patient_id = users.id AND deleted_at IS NULL) personCount"), 
                DB::raw("(SELECT count(*) from journals WHERE (journals.patient_id = users.id OR journals.emp_id = users.id) AND deleted_at IS NULL) journals_count"), 
                DB::raw("(SELECT count(*) from deviations WHERE (deviations.patient_id = users.id OR deviations.emp_id = users.id) AND deleted_at IS NULL) deviations_count"))
            ->where('users.top_most_parent_id',$this->top_most_parent_id)
            ->withoutGlobalScope('top_most_parent_id')
            ->with('TopMostParent:id,user_type_id,name,email','TopMostParent.companySetting:id,user_id,company_name,company_logo,company_email','Parent:id,name','UserType:id,name','Country','agencyHours','PatientInformation','persons.Country','branch:id,name,branch_name','assignedWork','role','employeePatients.patient:id,name,avatar,email','patientEmployees.employee:id,name,avatar,email','companySetting:id,company_name,company_logo,company_email,company_address,company_website','employeeBranches:id,employee_id,branch_id','employeeBranches.branch:id,name,branch_name')
            ->withCount('employees','patients','leaves','vacations');
            if(in_array($user->user_type_id, [1,2,4,5,11,16]))
            {
                //$query =  $query->where('users.id', '!=',$user->id);
            }
            elseif(in_array($user->user_type_id, [3]))
            {
                if(isShowAllPatient($user->id))
                {
                    // shows all patients
                }
                else
                {
                    // shows only those patients whos assgined to this employee
                    $getListAssignedPatients = \DB::table('patient_employees')
                        ->select('patient_id')
                        ->where('employee_id', $user->id)
                        ->pluck('patient_id');
                    $query->whereIn('users.id', $getListAssignedPatients);
                }
            }
            else
            {
                $query =  $query->where(function ($q) use ($user) {
                    $q->where('users.id', $user->id)
                    ->orWhere('users.id', $user->parent_id);
                });
            }

            if($user->user_type_id =='2') {
                $query = $query->orderBy('users.id','DESC');
            } elseif($user->user_type_id =='3') {
                $user_records = getAllowUserList('visible-all-patients');
                $query->whereIn('users.id', $user_records);
            } elseif(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                
            } else{
                $query =  $query->whereIn('users.branch_id',$allChilds);
            }
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') {
                $query = $query->whereRaw($whereRaw)->orderBy('users.id', 'DESC');
            } else {
                $query = $query->orderBy('users.id', 'DESC');
            }

            if(!empty($request->name))
            {
                $name = $request->name;
                $query->where(function($q) use ($name) {
                    $q->where('name', 'LIKE', '%'.$name.'%')
                    ->orWhere('branch_name', 'LIKE', '%'.$name.'%');
                });
            }

            if(!empty($request->joining_date))
            {
                $query->where('users.joining_date','<', $request->joining_date);
            }
            if(!empty($request->employee_type))
            {
                $query->where('users.employee_type', $request->employee_type);
            }
            if(!empty($request->gender))
            {
                $query->where('users.gender', $request->gender);
            }
            if(!empty($request->patient_type_id))
            {
                $query->whereJsonContains('users.patient_type_id', strval($request->patient_type_id));
            }
            if(!empty($request->company_type_id))
            {
                $query->whereJsonContains('users.company_type_id', $request->company_type_id);
            }
            if(!empty($request->ip_id))
            {
                $query->join('patient_implementation_plans', function ($join) {
                    $join->on('users.id', '=', 'patient_implementation_plans.user_id');
                })
                ->where('patient_implementation_plans.id', $request->ip_id);
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
            return prepareResult(true,getLangByLabelGroups('User','message_list'),$query,'200');
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function store(Request $request) 
    {
        if(in_array($request->user_type_id, [3,6]))
        {
            $checkAccess = checkEmpPartientCount($this->top_most_parent_id, $request->user_type_id);
            if(!$checkAccess)
            {
                return prepareResult(false,getLangByLabelGroups('User','account_creation_limit_reached') ,[], config('httpcodes.unauthorized'));
            }
        }
        
        DB::beginTransaction();
        try {

            $userInfo = getUser();

            $validator = Validator::make($request->all(),[
                'user_type_id' => 'required|exists:user_types,id', 
                'role_id' => 'required|exists:roles,id',
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email',
            ],
            [
                'user_type_id.required' =>  getLangByLabelGroups('BcValidation','message_user_type_id_required'),
                'role_id.required' =>  getLangByLabelGroups('BcValidation','message_role_id'),
                'name.required' =>  getLangByLabelGroups('BcValidation','message_name_required'),
                'email.required' =>  getLangByLabelGroups('BcValidation','message_email_required'),
                'email.email' =>  getLangByLabelGroups('BcValidation','message_email_invalid'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            //check Permission for create employee


            ////////////////////////////////////////

            if($request->user_type_id == '6'){
                $validator = Validator::make($request->all(),[
                    //'personal_number' => 'required|digits:12|unique:users,personal_number', 
                    'custom_unique_id' => 'required|unique:users,custom_unique_id', 
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

            }
            if($request->user_type_id == '3'){
                /*$validator = Validator::make($request->all(),[
                    'personal_number' => 'required|digits:12|unique:users,personal_number', 
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }
                */

            }

            $genPassword = genPassword(12);
            $password = Hash::make($genPassword);
            $is_password_change = false;
            $is_fake = false;
            if($request->is_fake == true  && $request->user_type_id == '6'){
                $is_fake = true;
                $is_password_change = true;
                $password = Str::random(12);
            }

            //for role set
            if($request->user_type_id==6)
            {
                $roleInfo = getRoleInfo($this->top_most_parent_id, 'Patient');
            }
            else
            {
                $roleInfo = Role::where('id',$request->role_id)->first();
            }            

            $user = new User;
            $user->unique_id = generateRandomNumber();
            $user->branch_id = !empty($request->branch_id) ? $request->branch_id : getBranchId();
            $user->branch_name = $request->branch_name;
            $user->branch_email = $request->branch_email;
            $user->custom_unique_id = $request->custom_unique_id;
            $user->user_type_id = $request->user_type_id;
            $user->role_id = $roleInfo->id;
            $user->company_type_id = (!empty($request->company_type_id)) ? json_encode($request->company_type_id) : $userInfo->company_type_id;
            $user->category_id = (!empty($request->category_id)) ? $request->category_id : $userInfo->category_id;
            $user->top_most_parent_id = $this->top_most_parent_id;
            $user->parent_id = $userInfo->id;
            $user->govt_id = $request->govt_id;
            $user->dept_id = $request->dept_id;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $password;
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->patient_type_id = (!empty($request->patient_type_id)) ? json_encode($request->patient_type_id) : null;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->joining_date = $request->joining_date;
            $user->establishment_year = $request->establishment_year;
            $user->user_color = $request->user_color;
            $user->disease_description = $request->disease_description;
            $user->created_by = $userInfo->id;
            $user->employee_type = $request->employee_type;
            $user->contract_type = $request->contract_type;
            $user->report_verify = $request->report_verify;
            $user->verification_method = $request->verification_method;
            $user->contract_value = $request->contract_value;
            $user->is_file_required = ($request->is_file_required == true) ? 1:0;
            $user->is_secret = ($request->is_secret == true) ? 1:0;
            $user->is_fake =  $is_fake;
            $user->step_one = (!empty($request->step_one)) ? $request->step_one:0;
            $user->step_two = (!empty($request->step_two)) ? $request->step_two:0;
            $user->step_three = (!empty($request->step_three)) ? $request->step_three:0;
            $user->step_four = (!empty($request->step_four)) ? $request->step_four:0;
            $user->step_five = (!empty($request->step_five)) ? $request->step_five:0;
            $user->is_password_change =  $is_password_change;
            $user->documents = is_array($request->documents) ? json_encode($request->documents) : null;
            $user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->contact_person_name = $request->contact_person_name;
            $user->contact_person_number = $request->contact_person_number;
            $user->avatar = (!empty($request->avatar)) ? $request->avatar : env('NO_IMG_PATH');
            $user->save();
            if($roleInfo)
            {
                $role = $roleInfo;
                $user->assignRole($role->name);
            }

            if($request->user_type_id == '6'){
                $patientInfo = new PatientInformation;
                $patientInfo->patient_id = $user->id;
                $patientInfo->institute_contact_person = $request->institute_contact_person;
                $patientInfo->institute_name = $request->institute_name;
                $patientInfo->institute_contact_number = $request->institute_contact_number;
                $patientInfo->institute_full_address = $request->institute_full_address;
                $patientInfo->institute_week_days = is_array($request->institute_week_days) ? json_encode($request->institute_week_days) : null;
                $patientInfo->classes_from = $request->classes_from;
                $patientInfo->classes_to = $request->classes_to;
                $patientInfo->company_name = $request->company_name;
                $patientInfo->company_contact_person = $request->company_contact_person;
                $patientInfo->company_contact_number = $request->company_contact_number;
                $patientInfo->company_full_address = $request->company_full_address;
                $patientInfo->from_timing = (!empty($request->from_timing) ? date("Y-m-d H:i:s", strtotime($request->from_timing)) : null);
                $patientInfo->to_timing = (!empty($request->to_timing) ? date("Y-m-d H:i:s", strtotime($request->to_timing)) : null);
                $patientInfo->company_week_days = is_array($request->company_week_days) ? json_encode($request->company_week_days) : null;
                $patientInfo->special_information = $request->special_information;
                $patientInfo->aids = $request->aids;
                $patientInfo->another_activity = $request->another_activity;
                $patientInfo->another_activity_name = $request->another_activity_name;
                $patientInfo->another_activity_contact_person = $request->another_activity_contact_person;
                $patientInfo->activitys_contact_number = $request->activitys_contact_number;
                $patientInfo->another_activity_start_time = $request->another_activity_start_time;
                $patientInfo->another_activity_end_time = $request->another_activity_end_time;

                $patientInfo->activitys_full_address = $request->activitys_full_address;
                $patientInfo->week_days = json_encode($request->week_days);
                $patientInfo->issuer_name = $request->issuer_name;
                $patientInfo->number_of_hours = $request->number_of_hours;
                $patientInfo->period = $request->period;
                $patientInfo->save();

                //Assigned Employee
                if(is_array($request->assigned_employee))
                {
                    foreach($request->assigned_employee as $key => $employee)
                    {

                        $patientEmployee = new PatientEmployee;
                        $patientEmployee->patient_id = $user->id;
                        $patientEmployee->employee_id = $employee;
                        $patientEmployee->save();
                    }
                }
            }
            if(env('IS_MAIL_ENABLE',false) == true && $is_fake == false && $user){ 
                $content = [
                    'company_id' => $user->top_most_parent_id,
                    'name' => aceussDecrypt($user->name),
                    'email' => aceussDecrypt($user->email),
                    'password'=>$genPassword,
                    'id' => $user->id,
                ];   
                Mail::to(aceussDecrypt($user->email))->send(new WelcomeMail($content));
            }

            //----------notify-company-user-added--------//

            $notified_company = User::find($user->top_most_parent_id);
            $data_id =  $user->id;

            if($user->user_type_id==3)
            {
                $assignBranch = new EmployeeBranch;
                $assignBranch->employee_id = $data_id;
                $assignBranch->branch_id = $user->branch_id;
                $assignBranch->save();
            }

            if($user->user_type_id == 3 || $user->user_type_id == 16)
            {
                $notification_template = EmailTemplate::where('mail_sms_for', 'employee-created')->first();
            }
            elseif ($user->user_type_id == 6) {
                $notification_template = EmailTemplate::where('mail_sms_for', 'patient-created')->first();
            }
            elseif ($user->user_type_id == 11) {
                $notification_template = EmailTemplate::where('mail_sms_for', 'branch-created')->first();
            }
            $extra_param = ['name'=>$user->name];

            if($user)
            {
                $variable_data = [
                    '{{name}}'          => aceussDecrypt($notified_company->name),
                    '{{user_name}}'     => aceussDecrypt($user->name)
                ];
                actionNotification($notified_company,$data_id,$notification_template,$variable_data,$extra_param, null, true);
            }
            //----------------------------------------//

            /*-------------patient weekly Hours-----------------------*/
            if(is_array($request->agency_hours) && sizeof($request->agency_hours) >0){

                foreach ($request->agency_hours as $key => $agency_hours) {
                    $days = getDays($agency_hours['end_date'],$agency_hours['start_date']);
                    $assigned_hours = $agency_hours['assigned_hours'];
                    $ass_work_per_day = $assigned_hours / $days;
                    $ass_work_per_week = $ass_work_per_day * 7;
                    $ass_work_per_month = $ass_work_per_day * 30;
                    if(!empty(@$agency_hours['assigned_hours']))
                    {
                        $agencyWeeklyHour = new AgencyWeeklyHour;
                        $agencyWeeklyHour->user_id = $user->id;
                        $agencyWeeklyHour->name = @$agency_hours['name'];
                        $agencyWeeklyHour->assigned_hours = @$agency_hours['assigned_hours'];
                        $agencyWeeklyHour->assigned_hours_per_day =$ass_work_per_day;
                        $agencyWeeklyHour->assigned_hours_per_week = $ass_work_per_week;
                        $agencyWeeklyHour->assigned_hours_per_month = $ass_work_per_month;
                        $agencyWeeklyHour->start_date = @$agency_hours['start_date'];
                        $agencyWeeklyHour->end_date = @$agency_hours['end_date'];
                        $agencyWeeklyHour->save();
                    }
                }
            }
            /*-----------------Persons Informationn ----------------*/
            if($request->user_type_id == '6') {
                if(is_array($request->persons) && sizeof($request->persons) >0 ){
                    foreach ($request->persons as $key => $value) {
                        if(!empty($value['name']))
                        {
                            $is_user = true;
                            $user_type_id = null;
                            if(@$value['is_other'] == true){
                                $user_type_id ='15';
                                $is_user = true;
                            }
                            if(@$value['is_guardian'] == true){
                                $user_type_id ='12';
                                $is_user = true;
                            }
                            if(@$value['is_family_member'] == true){
                                $user_type_id ='8';
                                $is_user = true;
                            }
                            if(@$value['is_caretaker'] == true){
                                $user_type_id ='7';
                                $is_user = true;
                            }
                            
                            if(@$value['is_contact_person'] == true){
                                $user_type_id ='9';
                                $is_user = true;
                            }
                            if((@$value['is_caretaker'] == true) && (@$value['is_family_member'] == true )){
                                $user_type_id ='10';
                                $is_user = true;
                            }

                            /*-----Create Account /Entry in user table*/
                            if($is_user == true) {
                                $top_most_parent_id = auth()->user()->top_most_parent_id;
                                $checkAlreadyUser = User::where('email', @$value['email'])->first();
                                if(empty($checkAlreadyUser) && !empty($user_type_id)) {
                                    $getUserType = UserType::find($user_type_id);
                                    $roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);
                                    $pass = generateRandomNumber(15);
                                    $checkExistUser = User::where('email', @$value['email'])->withoutGlobalScope('top_most_parent_id')->first();
                                    if($checkExistUser)
                                    {
                                        $userSave = $checkExistUser;
                                    }
                                    else
                                    {
                                        $userSave = new User;
                                        $userSave->unique_id = generateRandomNumber();
                                        $userSave->branch_id =   getBranchId();
                                        $userSave->user_type_id = $user_type_id;
                                        $userSave->role_id =  $roleInfo->id;
                                        $userSave->parent_id = $user->id;
                                        $userSave->top_most_parent_id = $top_most_parent_id;
                                        $userSave->name = @$value['name'] ;
                                        $userSave->email = @$value['email'] ;
                                        $userSave->personal_number = @$value['personal_number'];
                                        $userSave->password = Hash::make($pass);
                                    }

                                    $userSave->contact_number = @$value['contact_number'];
                                    $userSave->country_id = @$value['country_id'];
                                    $userSave->city = @$value['city'];
                                    $userSave->postal_area = @$value['postal_area'];
                                    $userSave->zipcode = @$value['zipcode'];
                                    $userSave->full_address = @$value['full_address'];
                                    $userSave->is_family_member = returnBoolean(@$value['is_family_member']);
                                    $userSave->is_caretaker = returnBoolean(@$value['is_caretaker']);
                                    $userSave->is_contact_person = returnBoolean(@$value['is_contact_person']);
                                    $userSave->is_guardian = returnBoolean(@$value['is_guardian']);
                                    $userSave->is_other = returnBoolean(@$value['is_other']);
                                    $userSave->is_other_name = (@$value['is_other_name']) ? @$value['is_other_name'] : 0 ;
                                    $userSave->save(); 

                                    if(!empty($user_type_id))
                                    {
                                        $role = $roleInfo;
                                        $userSave->assignRole($role->name);
                                    }     
                                    if(env('IS_MAIL_ENABLE',false) == true){ 
                                        $content = [
                                            'company_id' => $userSave->top_most_parent_id,
                                            'name' => $userSave->name,
                                            'email' => $userSave->email,
                                            'password' => $pass,
                                            'id' => $userSave->id,
                                        ];    
                                        Mail::to(aceussDecrypt($userSave->email))->send(new WelcomeMail($content));
                                    }

                                }
                            }
                        }

                    }
                }
            }

            if($request->user_type_id == '3')
            {
                $assWorking = $request->assigned_working_hour_per_week;
                $workingPercent = $request->working_percent;

                $actWorking = $assWorking * $workingPercent / 100;

                $empAssWorkHour = new EmployeeAssignedWorkingHour;
                $empAssWorkHour->emp_id = $user->id;
                $empAssWorkHour->assigned_working_hour_per_week = $assWorking;
                $empAssWorkHour->working_percent = $workingPercent;
                $empAssWorkHour->actual_working_hour_per_week = $actWorking;
                $empAssWorkHour->created_by = Auth::id();
                $empAssWorkHour->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $empAssWorkHour->save();

                $joining_date = $request->joining_date ? strtotime($request->joining_date) : strtotime(date('Y-m-d'));
                $two_years_later = strtotime(date('Y-m-d', strtotime('+2 years',$joining_date)));
                $date_sets = [];
                for($curDate=$joining_date; $curDate<=$two_years_later; $curDate += (86400 * 28))
                {
                    $start_date = date('Y-m-d', $curDate);
                    $end_date = date('Y-m-d', strtotime('+27 days',strtotime($start_date)));
                    if(strtotime($end_date) < $two_years_later)
                    {
                        $date_sets[] = [$start_date,$end_date];
                    }
                }

                foreach ($date_sets as $key => $value) {
                    $datesData = new UserScheduledDate;
                    $datesData->working_percent = $workingPercent;
                    $datesData->emp_id = $user->id;
                    $datesData->start_date = $value[0];
                    $datesData->end_date = $value[1];
                    $datesData->save();
                }

                //Assigned Patient
                if(is_array($request->assigned_patiens))
                {
                    foreach($request->assigned_patiens as $key => $patient)
                    {
                        $patientEmployee = new PatientEmployee;
                        $patientEmployee->patient_id = $patient;
                        $patientEmployee->employee_id = $user->id;
                        $patientEmployee->save();
                    }
                }
            }
            DB::commit();
            $user['branch'] = $user->branch()->select('id', 'name','branch_name')->first();
            $user['assignedWork'] = $user->assignedWork;
            $user['assigned_patiens'] = $request->assigned_patiens;
            $user['assigned_employee'] = $request->assigned_employee;
            $user['company_setting'] = $user->companySetting()->select('id','company_name','company_logo','company_email','company_address','company_website')->first();
            return prepareResult(true,getLangByLabelGroups('User','message_create') ,$user, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function show(User $user)
    {
        try {
            $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
            $checkId= User::where('id', $user->id)->where('top_most_parent_id', $this->top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('User','message_record_not_found'), [], config('httpcodes.not_found'));
            }
            $userShow = User::select('users.*',DB::raw("(SELECT count(*) from patient_implementation_plans WHERE patient_implementation_plans.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchIpCount"), 
                DB::raw("(SELECT count(*) from activities WHERE activities.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchActivityCount"), 
                DB::raw("(SELECT count(*) from tasks WHERE tasks.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchTaskCount"),
                DB::raw("(SELECT count(*) from ip_follow_ups WHERE ip_follow_ups.branch_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") branchFollowupCount"))
            ->where('id',$user->id)
            ->with('TopMostParent:id,user_type_id,name,email','TopMostParent.companySetting:id,user_id,company_name,company_logo,company_email','UserType:id,name','CategoryMaster:id,created_by,name','Department:id,name','Country:id,name','agencyHours','branch','persons.Country','PatientInformation','branch:id,name,branch_name,email,contact_number','assignedWork','role','employeePatients.patient:id,name,avatar,email','patientEmployees.employee:id,name,avatar,email','branchEmployees:id,employee_id,branch_id','branchEmployees.employee:id,name','employeeBranches:id,employee_id,branch_id','employeeBranches.branch:id,name,branch_name,branch_email')->first();
            if($user->user_type_id == 6)
            {
                // $patientAssignedHours = AgencyWeeklyHour::where('user_id',$user->id)->sum('assigned_hours') * 60;
                $patientSchedules = Schedule::select([
                    \DB::raw('SUM(scheduled_work_duration) as regular_hours'),
                    \DB::raw('SUM(extra_work_duration) as extra_hours'),
                    \DB::raw('SUM(ob_work_duration) as obe_hours'),
                    \DB::raw('SUM(emergency_work_duration) as emergency_hours'),
                    \DB::raw('SUM(vacation_duration) as vacation_hours')
                ])
                ->whereDate('shift_date','<=', date('Y-m-d'))
                ->where('patient_id', $user->id) 
                ->where('leave_applied', 0) 
                ->first();
                $used_total_patient_hours = $patientSchedules->regular_hours + $patientSchedules->extra_hours + $patientSchedules->obe_hours + $patientSchedules->emergency_hours + $patientSchedules->vacation_hours;
            }
            else
            {
                $used_total_patient_hours = 0;
            }
            $userShow->used_total_patient_hours = $used_total_patient_hours;

            $assigned_patiens = [];
            foreach ($userShow->employeePatients as $key => $patient) {
                $assigned_patiens[] = $patient->patient_id;
            }

            $assigned_employee = [];
            foreach ($userShow->patientEmployees as $key => $employee) {
                $assigned_employee[] = $employee->employee_id;
            }

            $userShow['assigned_patiens'] = $assigned_patiens;
            $userShow['assigned_employee'] = $assigned_employee;
            $userShow['company_setting'] = $userShow->companySetting()->select('id','company_name','company_logo','company_email','company_address','company_website')->first();
            return prepareResult(true,getLangByLabelGroups('User','message_show'),$userShow, config('httpcodes.success'));

        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function update(Request $request,User $user)
    {
        DB::beginTransaction();
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[
                'user_type_id' => 'required|exists:user_types,id', 
                'role_id' => 'required|exists:roles,id', 
                'name' => 'required', 
            ],
            [
                'user_type_id.required' =>  getLangByLabelGroups('BcValidation','message_user_type_id'),
                'role_id.required' =>  getLangByLabelGroups('BcValidation','message_role_id'),
                'name.required' =>  getLangByLabelGroups('BcValidation','message_name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            if($request->user_type_id == '6' || $request->user_type_id =='3')
            {
                /*$validator = Validator::make($request->all(),[
                    'personal_number' => 'required|digits:12|unique:users,personal_number,'.$user->id, 
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }*/
            }

            $checkId = User::where('id',$user->id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('BcValidation','message_record_not_found'), [], config('httpcodes.not_found'));
            }

            //for role set
            if($request->user_type_id==6)
            {
                $roleInfo = getRoleInfo($this->top_most_parent_id, 'Patient');
            }
            else
            {
                $roleInfo = Role::where('id',$request->role_id)->first();
            }

            $user->user_type_id = $request->user_type_id;
            $user->role_id = $roleInfo->id;
            $user->category_id = (!empty($request->category_id)) ? $request->category_id : $userInfo->category_id;
            $user->branch_id = $request->branch_id ? $request->branch_id : $userInfo->top_most_parent_id;
            $user->branch_name = $request->branch_name;
            $user->branch_email = $request->branch_email;
            $user->govt_id = $request->govt_id;
            $user->dept_id = $request->dept_id;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->name = $request->name;
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->patient_type_id = (!empty($request->patient_type_id)) ? json_encode($request->patient_type_id) : null;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->joining_date = $request->joining_date;
            $user->establishment_year = $request->establishment_year;
            $user->user_color = $request->user_color;
            $user->disease_description = $request->disease_description;
            $user->employee_type = $request->employee_type;
            $user->contract_type = $request->contract_type;
            $user->report_verify = $request->report_verify;
            $user->verification_method = $request->verification_method;
            $user->contract_value = $request->contract_value;
            $user->is_file_required = ($request->is_file_required) ? 1:0;
            $user->is_secret = ($request->is_secret) ? 1:0;
            $user->step_one = (!empty($request->step_one)) ? $request->step_one:0;
            $user->step_two = (!empty($request->step_two)) ? $request->step_two:0;
            $user->step_three = (!empty($request->step_three)) ? $request->step_three:0;
            $user->step_four = (!empty($request->step_four)) ? $request->step_four:0;
            $user->step_five = (!empty($request->step_five)) ? $request->step_five:0;
            $user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->documents = is_array($request->documents) ? json_encode($request->documents) : $user->documents;
            $user->contact_person_name = $request->contact_person_name;
            $user->contact_person_number = $request->contact_person_number;
            if(!empty($request->avatar))
            {
                $user->avatar = $request->avatar;
            }

            $user->company_type_id = (!empty($request->company_type_id)) ? json_encode($request->company_type_id) : $userInfo->company_type_id;
            $user->save();

            if($roleInfo)
            {
                \DB::table('model_has_roles')->where('model_id',$user->id)->delete();
                $role = $roleInfo;
                $user->assignRole($role->name);
            }


            if($request->user_type_id == '6')
            {
                $checkPatientAlready = PatientInformation::where('patient_id',$user->id)->first();
                if(is_object($checkPatientAlready)){
                    $patientInfo =  PatientInformation::find($checkPatientAlready->id);
                } else{
                    $patientInfo = new PatientInformation;
                }

                $patientInfo->patient_id = $user->id;
                $patientInfo->institute_contact_person = $request->institute_contact_person;
                $patientInfo->institute_name = $request->institute_name;
                $patientInfo->institute_contact_number = $request->institute_contact_number;
                $patientInfo->institute_full_address = $request->institute_full_address;
                $patientInfo->institute_week_days = is_array($request->institute_week_days) ? json_encode($request->institute_week_days) : null;
                $patientInfo->classes_from = $request->classes_from;
                $patientInfo->classes_to = $request->classes_to;
                $patientInfo->company_name = $request->company_name;
                $patientInfo->company_contact_number = $request->company_contact_number;
                $patientInfo->company_contact_person = $request->company_contact_person;
                $patientInfo->company_full_address = $request->company_full_address;
                $patientInfo->from_timing = (!empty($request->from_timing) ? date("Y-m-d H:i:s", strtotime($request->from_timing)) : null);
                $patientInfo->to_timing = (!empty($request->to_timing) ? date("Y-m-d H:i:s", strtotime($request->to_timing)) : null);
                $patientInfo->company_week_days = is_array($request->company_week_days) ? json_encode($request->company_week_days) : null;
                $patientInfo->special_information = $request->special_information;
                $patientInfo->aids = $request->aids;
                $patientInfo->another_activity = $request->another_activity;
                $patientInfo->another_activity_name = $request->another_activity_name;
                $patientInfo->another_activity_contact_person = $request->another_activity_contact_person;
                $patientInfo->activitys_contact_number = $request->activitys_contact_number;
                $patientInfo->another_activity_start_time = $request->another_activity_start_time;
                $patientInfo->another_activity_end_time = $request->another_activity_end_time;
                $patientInfo->activitys_full_address = $request->activitys_full_address;
                $patientInfo->week_days = json_encode($request->week_days);
                $patientInfo->issuer_name = $request->issuer_name;
                $patientInfo->number_of_hours = $request->number_of_hours;
                $patientInfo->period = $request->period;
                $patientInfo->save();

                //Assigned Employee
                //removed preassigned employee
                PatientEmployee::where('patient_id', $user->id)->delete();
                if(is_array($request->assigned_employee))
                {
                    foreach($request->assigned_employee as $key => $employee)
                    {
                        $patientEmployee = new PatientEmployee;
                        $patientEmployee->patient_id = $user->id;
                        $patientEmployee->employee_id = $employee;
                        $patientEmployee->save();
                    }
                }

            }

            /*-------------patient weekly Hours-----------------------*/
            if(is_array($request->agency_hours) && sizeof($request->agency_hours) >0){
                $deleteOld = AgencyWeeklyHour::where('user_id',$user->id)->delete();
                foreach ($request->agency_hours as $key => $agency_hours) {
                    $days = getDays($agency_hours['end_date'],$agency_hours['start_date']);
                    $assigned_hours = $agency_hours['assigned_hours'];
                    $ass_work_per_day = $assigned_hours / $days;
                    $ass_work_per_week = $ass_work_per_day * 7;
                    $ass_work_per_month = $ass_work_per_day * 30;
                    if(!empty(@$agency_hours['assigned_hours']))
                    {
                        $agencyWeeklyHour = new AgencyWeeklyHour;
                        $agencyWeeklyHour->user_id = $user->id;
                        $agencyWeeklyHour->name = @$agency_hours['name'];
                        $agencyWeeklyHour->assigned_hours = @$agency_hours['assigned_hours'];
                        $agencyWeeklyHour->assigned_hours_per_day = $ass_work_per_day;
                        $agencyWeeklyHour->assigned_hours_per_week = $ass_work_per_week;
                        $agencyWeeklyHour->assigned_hours_per_month = $ass_work_per_month;
                        $agencyWeeklyHour->start_date = @$agency_hours['start_date'];
                        $agencyWeeklyHour->end_date = @$agency_hours['end_date'];
                        $agencyWeeklyHour->save();
                    }
                }
            }

            /*-----------------Persons Informationn ----------------*/
            if(is_array($request->persons) && sizeof($request->persons) >0){
                foreach ($request->persons as $key => $value) {
                    if(!empty($value['name']))
                    {
                        $is_user = false;
                        $user_type_id = null;
                        if(@$value['is_other'] == true){
                            $user_type_id ='15';
                            $is_user = true;
                        }
                        if(@$value['is_guardian'] == true){
                            $user_type_id ='12';
                            $is_user = true;
                        }
                        if(@$value['is_family_member'] == true){
                            $user_type_id ='8';
                            $is_user = true;
                        }
                        if(@$value['is_caretaker'] == true){
                            $user_type_id ='7';
                            $is_user = true;
                        }
                        
                        if(@$value['is_contact_person'] == true){
                            $user_type_id ='9';
                            $is_user = true;
                        }
                        if((@$value['is_caretaker'] == true) && (@$value['is_family_member'] == true )){
                            $user_type_id ='10';
                            $is_user = true;
                        }

                        /*-----Create Account /Entry in user table*/
                        if($is_user == true) {
                            $top_most_parent_id = auth()->user()->top_most_parent_id;

                            $checkAlreadyUser = User::where(function($q) use ($value) {
                                $q->where('email', @$value['email'])
                                ->orWhere('id', @$value['id']);
                            })->withTrashed()->first();
                            $pass = generateRandomNumber(15);
                            if(empty($checkAlreadyUser)) 
                            {
                                $getUserType = UserType::find($user_type_id);
                                $roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);

                                $userSave = new User;
                                $userSave->branch_id = getBranchId();
                                $userSave->user_type_id = $user_type_id;
                                $userSave->role_id =  $roleInfo->id;
                                $userSave->parent_id = $user->id;
                                $userSave->top_most_parent_id = $top_most_parent_id;
                                $userSave->password = Hash::make($pass);
                            } 
                            else
                            {
                                $userSave = $checkAlreadyUser;
                            }
                            $userSave->name = @$value['name'] ;
                            $userSave->email = @$value['email'] ;
                            $userSave->personal_number = @$value['personal_number'] ;
                            
                            $userSave->contact_number = @$value['contact_number'];
                            $userSave->country_id = @$value['country_id'];
                            $userSave->city = @$value['city'];
                            $userSave->postal_area = @$value['postal_area'];
                            $userSave->zipcode = @$value['zipcode'];
                            $userSave->full_address = @$value['full_address'];
                            $userSave->is_family_member = returnBoolean(@$value['is_family_member']);
                            $userSave->is_caretaker = returnBoolean(@$value['is_caretaker']);
                            $userSave->is_contact_person = returnBoolean(@$value['is_contact_person']);
                            $userSave->is_guardian = returnBoolean(@$value['is_guardian']);
                            $userSave->is_other = returnBoolean(@$value['is_other']);
                            $userSave->is_other_name = (@$value['is_other_name']) ? @$value['is_other_name'] : null ;
                            $userSave->save(); 
                            
                            if(empty($checkAlreadyUser)) 
                            {
                                if(!empty($user_type_id))
                                {
                                    $role = $roleInfo;
                                    $userSave->assignRole($role->name);
                                }     
                                if(env('IS_MAIL_ENABLE',false) == true){ 
                                    $content = [
                                        'company_id' => $userSave->top_most_parent_id,
                                        'name' => $userSave->name,
                                        'email' => $userSave->email,
                                        'password' => $pass,
                                        'id' => $userSave->id,
                                    ];    
                                    Mail::to(aceussDecrypt($userSave->email))->send(new WelcomeMail($content));
                                }
                            }
                            else
                            {
                                if(!empty($user_type_id))
                                {
                                    $getUserType = UserType::find($user_type_id);
                                    $roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);

                                    $role = $roleInfo;
                                    //Delete old role
                                    \DB::table('model_has_roles')->where('model_id', $userSave->id)->delete();
                                    $userSave->assignRole($role->name);
                                }  
                            }
                        }
                    }
                }
            }

            if($request->user_type_id == '3')
            {
                //Assigned Patient
                //removed preassigned employee
                PatientEmployee::where('employee_id', $user->id)->delete();
                if(is_array($request->assigned_patiens))
                {
                    foreach($request->assigned_patiens as $key => $patient)
                    {
                        $patientEmployee = new PatientEmployee;
                        $patientEmployee->patient_id = $patient;
                        $patientEmployee->employee_id = $user->id;
                        $patientEmployee->save();
                    }
                }
            }

            DB::commit();
            $user['branch'] = $user->branch()->select('id', 'name', 'branch_name')->first();
            $user['assignedWork'] = $user->assignedWork;
            $user['assigned_patiens'] = $request->assigned_patiens;
            $user['assigned_employee'] = $request->assigned_employee;
            $user['company_setting'] = $user->companySetting()->select('id','company_name','company_logo','company_email','company_address','company_website')->first();
            $user['patient_imformation'] = $user->PatientInformation;
            return prepareResult(true,getLangByLabelGroups('User','message_update'),$user, config('httpcodes.success'));

        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[],'500');

        }
    }

    public function destroy(User $user)
    {
        try {
            $id = $user->id;
            $checkId = User::where('id',$id)->where('top_most_parent_id', $this->top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('User','message_record_not_found'), [], config('httpcodes.not_found'));
            }

            if($user->id == auth()->id())
            {
                return prepareResult(false,getLangByLabelGroups('User','message_unauthorized'), [], config('httpcodes.unauthorized'));
            }

            $updateStatus = $user->update(['status'=>2]);
            if(in_array($user->user_type_id, [2,11]))
            {
                User::where('top_most_parent_id', $user->id)->update(['status'=> 3]);
            }
            elseif(in_array($user->user_type_id, [3]))
            {
                Task::where('type_id', 8)->where('resource_id', $user->id)->delete();
                AssignTask::where('user_id',$user->id)->delete();
                PatientEmployee::where('employee_id', $user->id)
                    ->delete();
                ActivityAssigne::where('user_id',$user->id)->delete();
                Schedule::where('user_id',$user->id)->delete();
                Stampling::where('user_id',$user->id)->delete();
            }
            elseif(in_array($user->user_type_id, [6]))
            {
                Task::where('type_id', 7)->where('resource_id', $user->id)->delete();
                AssignTask::where('user_id',$user->id)->delete();
                Activity::where('patient_id',$user->id)->delete();
                ActivityAssigne::where('user_id',$user->id)->delete();
                Schedule::where('patient_id',$user->id)->delete();
                Stampling::where('user_id',$user->id)->delete();
                PatientImplementationPlan::where('user_id',$user->id)->delete();
                Journal::where('patient_id',$user->id)->delete();
                Deviation::where('patient_id',$user->id)->delete();
                PersonalInfoDuringIp::where('patient_id',$user->id)->delete();
                IpFollowUp::where('patient_id',$user->id)->delete();
                User::where('parent_id', $user->id)->update(['status'=> 3]);
                PatientEmployee::where('patient_id',$user->id)
                    ->delete();
                PatientCashier::where('patient_id',$user->id)->delete();
            }
            $userDelete = $user->delete();
            return prepareResult(true, getLangByLabelGroups('User','message_delete'),[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function getLicenceStatus()
    {
        try 
        {
            $licenceKeyData = LicenceKeyManagement::where('top_most_parent_id', auth()->user()->top_most_parent_id)->where('is_used',1)->orderBy('id','desc')->first();

            if(empty($licenceKeyData))
            {
                return prepareResult(false,getLangByLabelGroups('LicenceKey','message_record_not_found') ,[], config('httpcodes.success'));
            }

            if($licenceKeyData->expire_at >= date('Y-m-d'))
            {
                return prepareResult(true,getLangByLabelGroups('LicenceKey','message_status_active') ,'active', config('httpcodes.success'));
            }
            else
            {
                $companyStatus = User::find(auth()->user()->top_most_parent_id);
                $companyStatus->licence_status = 0;
                $companyStatus->save();

                return prepareResult(true,getLangByLabelGroups('LicenceKey','message_status_inactive') ,'inactive', config('httpcodes.success'));
            }
        } catch (\Throwable $exception) {
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function emailUpdate(Request $request,User $user)
    {
        DB::beginTransaction();
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[
                'email' => 'required|unique:users', 
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }


            if($userInfo->is_fake == 1)
            {
                Auth::User()->update(['email'=>$request->email,'is_fake'=>0]);
            }
            else
            {
                return prepareResult(false,['cant update, is_fake = 0'],[], config('httpcodes.bad_request')); 
            }
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('User','message_update'),$userInfo, config('httpcodes.success'));

        }
        catch(Exception $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[],'500');
        }
    }

    public function getCompanyAssignedPackages(Request $request)
    {
        try 
        {
            $data = Subscription::where('user_id',Auth::id());
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $data->count();
                $result = $data->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $data = $pagination;
            }
            else
            {
                $data = $data->get();
            }

            // if(empty($data))
            // {
            //     return prepareResult(false,getLangByLabelGroups('Package','message_record_not_found') ,[], config('httpcodes.success'));
            // }

            return prepareResult(true,getLangByLabelGroups('Package','message_list') ,$data, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function getCompanyActivePackage()
    {
        try 
        {
            // $data = LicenceKeyManagement::where('top_most_parent_id', auth()->user()->top_most_parent_id)->where('is_used',1)->where('expire_at','>=',date('Y-m-d'))->orderBy('id','desc')->first();

            $data = Subscription::whereIn('user_id',[Auth::id(),Auth::user()->top_most_parent_id])->where('start_date','<=',date('Y-m-d'))->where('end_date','>=',date('Y-m-d'))->orderBy('id','desc')->first();

            // if(empty($data))
            // {
            //     return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found') ,[], config('httpcodes.not_found'));
            // }
            return prepareResult(true,getLangByLabelGroups('Package','message_show') ,$data, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('user_type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.user_type_id = "."'" .$request->input('user_type_id')."'".")";
        }

        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.branch_id = "."'" .$request->input('branch_id')."'".")";
        }

        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        if (is_null($request->input('category_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.category_id = "."'" .$request->input('category_id')."'".")";
        }
        if (is_null($request->input('dept_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.dept_id = "."'" .$request->input('dept_id')."'".")";
        }
        // if (is_null($request->input('patient_type_id')) == false) {
        //  if ($w != '') {$w = $w . " AND ";}
        //  $w = $w . "(" . "patient_type_id = "."'" .$request->input('patient_type_id')."'".")";
        // }
        
        if (is_null($request->input('email')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.email like '%" .trim(strtolower($request->input('email'))) . "%')";

        }
        if (is_null($request->input('contact_number')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.contact_number like '%" .trim(strtolower($request->input('contact_number'))) . "%')";

        }
        if (is_null($request->input('personal_number')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.personal_number like '%" .trim(strtolower($request->input('personal_number'))) . "%')";

        }
        if (is_null($request->input('organization_number')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "users.organization_number like '%" .trim(strtolower($request->input('organization_number'))) . "%')";

        }
        return($w);

    }

    public function trashedUsers(Request $request)
    {
        try 
        {

            $user = getUser();
            $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
            if(!empty($user->branch_id)) {
                if($user->user_type_id==11)
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                    $allChilds[] = $user->id;
                }
                else
                {
                    $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                }
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
            $query = User::onlyTrashed()->select('users.id','users.unique_id','users.custom_unique_id','users.user_type_id', 'users.company_type_id','users.patient_type_id','users.avatar', 'users.category_id', 'users.top_most_parent_id', 'users.parent_id','users.branch_id','users.country_id','users.city', 'users.dept_id', 'users.govt_id','users.name', 'users.email', 'users.email_verified_at','users.contact_number','users.user_color', 'users.gender','users.organization_number', 'users.personal_number','users.joining_date','users.is_fake','users.is_secret','users.employee_type','users.is_password_change','users.status','users.step_one','users.step_two','users.step_three','users.step_four','users.step_five','users.report_verify','users.verification_method', 
                DB::raw("(SELECT count(*) from patient_implementation_plans WHERE patient_implementation_plans.user_id = users.id AND is_latest_entry = 1 AND start_date >= ".$date.") ipCount"), 
                DB::raw("(SELECT count(activity_assignes.id) FROM activity_assignes JOIN activities ON activity_assignes.activity_id = activities.id WHERE activity_assignes.user_id = users.id AND activity_assignes.deleted_at IS NULL AND activities.deleted_at IS NULL AND activities.is_latest_entry = 1) assignActivityCount"), 
                DB::raw("(SELECT count(*) from activities WHERE activities.patient_id = users.id AND deleted_at IS NULL AND is_latest_entry = 1 AND start_date >= ".$date.") patientActivityCount"), 
                DB::raw("(SELECT count(assign_tasks.id) from assign_tasks JOIN tasks ON assign_tasks.task_id = tasks.id  WHERE assign_tasks.user_id = users.id AND assign_tasks.deleted_at IS NULL AND tasks.deleted_at IS NULL AND tasks.is_latest_entry = 1) assignTaskCount"), 
                DB::raw("(SELECT count(*) from tasks WHERE tasks.resource_id = users.id AND tasks.type_id = 7 AND is_latest_entry = 1 AND start_date >= ".$date.") patientTaskCount"), 
                DB::raw("(SELECT count(*) from personal_info_during_ips WHERE personal_info_during_ips.patient_id = users.id ) personCount"), 
                DB::raw("(SELECT count(*) from journals WHERE journals.patient_id = users.id ) journals_count"), 
                DB::raw("(SELECT count(*) from deviations WHERE deviations.patient_id = users.id ) deviations_count"))
            ->where('users.top_most_parent_id',$this->top_most_parent_id)
            ->withoutGlobalScope('top_most_parent_id')
            ->with(
                [
                    'TopMostParent' => function ($query) {
                        $query->withTrashed();
                    },
                    'Parent' => function ($query) {
                        $query->withTrashed();
                    },
                    // 'UserType' => function ($query) {
                    //     $query->withTrashed();
                    // },
                    // 'Country' => function ($query) {
                    //     $query->withTrashed();
                    // },
                    'agencyHours' => function ($query) {
                        $query->withTrashed();
                    },
                    // 'PatientInformation' => function ($query) {
                    //     $query->withTrashed();
                    // },
                    'persons' => function ($query) {
                        $query->withTrashed();
                    },
                    'branchs' => function ($query) {
                        $query->withTrashed();
                    },
                    'assignedWork' => function ($query) {
                        $query->withTrashed();
                    },
                    // 'role' => function ($query) {
                    //     $query->withTrashed();
                    // },
                    'tasks' => function ($query) use ($date) {
                        $query->withTrashed()->where('start_date','>=',$date);
                    },
                    'activities' => function ($query) use ($date) {
                        $query->withTrashed()->where('start_date','>=',$date);
                    },
                    'ips' => function ($query) use ($date) {
                        $query->withTrashed()->where('start_date','>=',$date);
                    },
                    'patients' => function ($query) use ($date) {
                        $query->withTrashed();
                    },
                    'employees' => function ($query) use ($date) {
                        $query->withTrashed();
                    },
                    'assignedModule' => function ($query) use ($date) {
                        $query->withTrashed();
                    },
                    'branchs' => function ($query) use ($date) {
                        $query->withTrashed();
                    }
                ]
            )
            ->withCount(
                [
                    'tasks' => function ($query) use ($date) {
                        $query->withTrashed()->where('start_date','>=',$date);
                    },
                    'activities' => function ($query) use ($date) {
                        $query->withTrashed()->where('start_date','>=',$date);
                    },
                    'ips' => function ($query) use ($date) {
                        $query->withTrashed()->where('start_date','>=',$date);
                    },
                    'patients' => function ($query) {
                        $query->withTrashed();
                    },
                    'employees' => function ($query) {
                        $query->withTrashed();
                    },
                    'assignedModule' => function ($query) {
                        $query->withTrashed();
                    },
                    'branchs' => function ($query) {
                        $query->withTrashed();
                    },
                    'leaves' => function ($query) {
                        $query->withTrashed();
                    },
                    'vacations' => function ($query) {
                        $query->withTrashed();
                    }
                ]
            );
            if(in_array($user->user_type_id, [1,2,3,4,5,11,16]))
            {
                $query =  $query->where('users.id', '!=',$user->id);
            }
            else
            {
                $query =  $query->where(function ($q) use ($user) {
                    $q->where('users.id', $user->id)
                    ->orWhere('users.id', $user->parent_id);
                });
            }



            if($user->user_type_id =='2') {
                $query = $query->orderBy('users.id','DESC');
            } else{
                $query =  $query->whereIn('users.branch_id',$allChilds);
            }
            
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') {
                $query = $query->whereRaw($whereRaw)->orderBy('users.id', 'DESC');
            } else {
                $query = $query->orderBy('users.id', 'DESC');
            }
            if(!empty($request->joining_date))
            {
                $query->where('users.joining_date','<', $request->joining_date);
            }

            if(!empty($request->name))
            {
                $name = $request->name;
                $query->where(function($q) use ($name) {
                    $q->where('name', 'LIKE', '%'.$name.'%')
                    ->orWhere('branch_name', 'LIKE', '%'.$name.'%');
                });
            }

            if(!empty($request->employee_type))
            {
                $query->where('users.employee_type', $request->employee_type);
            }
            if(!empty($request->gender))
            {
                $query->where('users.gender', $request->gender);
            }
            if(!empty($request->patient_type_id))
            {
                $query->whereJsonContains('users.patient_type_id', strval($request->patient_type_id));
            }
            if(!empty($request->company_type_id))
            {
                $query->whereJsonContains('users.company_type_id', $request->company_type_id);
            }
            if(!empty($request->ip_id))
            {
                $query->join('patient_implementation_plans', function ($join) {
                    $join->on('users.id', '=', 'patient_implementation_plans.user_id');
                })
                ->where('patient_implementation_plans.id', $request->ip_id);
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
            return prepareResult(true,getLangByLabelGroups('User','message_list'),$query,'200');
        }
        catch(Exception $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

        }
    }

    public function revokePatientEmployee(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'patient_id' => 'required|exists:users,id', 
            'employee_id' => 'required|exists:users,id'
        ]);
        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        try 
        {
            $data = PatientEmployee::where('patient_id', $request->patient_id)
                ->where('employee_id', $request->employee_id)
                ->delete();
            return prepareResult(true, getLangByLabelGroups('User','message_delete'),[], config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
