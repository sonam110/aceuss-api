<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\package;
use App\Models\Subscription;
use App\Models\EmailTemplate;
use App\Models\PersonalInfoDuringIp;
use App\Models\AgencyWeeklyHour;
use App\Models\PatientInformation;
use Validator;
use Auth;
use DB;
use Exception;
use Mail;
use Str;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class UserController extends Controller
{

    protected $top_most_parent_id;
    public function __construct()
    {

        /*$this->middleware('permission:users-browse',['except' => ['show']]);
        $this->middleware('permission:users-add', ['only' => ['store']]);
        $this->middleware('permission:users-edit', ['only' => ['update']]);
        $this->middleware('permission:users-read', ['only' => ['show']]);
        $this->middleware('permission:users-delete', ['only' => ['destroy']]);*/
        
        $this->middleware(function ($request, $next) {
            if(auth()->user()->user_type_id=='1') {
                $this->top_most_parent_id = auth()->user()->id;
            }
            else if(auth()->user()->user_type_id=='2')
            {
                $this->top_most_parent_id = auth()->user()->id;
            } else {
                $this->top_most_parent_id = auth()->user()->top_most_parent_id;
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function users(Request $request)
    {
        try {
            $user = getUser();
            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchChilds = branchChilds($branch_id);
            $allChilds = array_merge($branchChilds,[$user->id]);
            $query = User::select('id','unique_id','custom_unique_id','user_type_id', 'company_type_id','patient_type_id', 'category_id', 'top_most_parent_id', 'parent_id','branch_id','country_id','city', 'dept_id', 'govt_id','name', 'email', 'email_verified_at','contact_number','user_color', 'gender','organization_number', 'personal_number','joining_date','is_fake','is_password_change','status', DB::raw("(SELECT count(*) from activity_assignes WHERE activity_assignes.user_id = users.id ) assignActivityCount") , DB::raw("(SELECT count(*) from assign_tasks WHERE assign_tasks.user_id = users.id ) assignTaskCount"), DB::raw("(SELECT count(*) from ip_assigne_to_employees WHERE ip_assigne_to_employees.user_id = users.id ) assignIpCount"))->where('top_most_parent_id',$this->top_most_parent_id)->with('TopMostParent:id,user_type_id,name,email','Parent:id,name','UserType:id,name','Country','weeklyHours','PatientInformation');
            if($user->user_type_id =='2'){
                
                $query = $query->orderBy('id','DESC');
            } else{
                $query =  $query->whereIn('branch_id',$allChilds);
            }
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') {
                $query = $query->whereRaw($whereRaw)->orderBy('id', 'DESC');
            } else {
                $query = $query->orderBy('id', 'DESC');
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
                return prepareResult(true,"User list",$pagination,'200');
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"User list",$query,'200');
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request) {
        try {

            $userInfo = getUser();

            $validator = Validator::make($request->all(),[
                'user_type_id' => 'required|exists:user_types,id', 
                'role_id' => 'required|exists:roles,id',
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email',
            ],
            [
            'user_type_id.required' =>  getLangByLabelGroups('UserValidation','user_type_id'),
            'role_id.required' =>  getLangByLabelGroups('UserValidation','role_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','email_invalid'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
            if($request->user_type_id  != '6'){
                 $validator = Validator::make($request->all(),[
                'password'  => 'required|same:confirm-password|min:8|max:30', 
                'contact_number' => 'required', 

                ],
                [
                'password.required' =>  getLangByLabelGroups('UserValidation','password'),
                'password.min' =>  getLangByLabelGroups('UserValidation','password_min'),
                'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], '422'); 
                }

            }
            if($request->user_type_id == '6'){
                $validator = Validator::make($request->all(),[
                    'personal_number' => 'required|digits:12|unique:users,personal_number', 
                    'custom_unique_id' => 'required|unique:users,custom_unique_id', 
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], '422'); 
                }

            }
            if($request->user_type_id == '3'){
                $validator = Validator::make($request->all(),[
                    'personal_number' => 'required|digits:12|unique:users,personal_number', 
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], '422'); 
                }

            }
            /*if(!empty($request->patient_type_id)){
                if($request->patient_type_id == '2' || $request->patient_type_id == '3' ){
                    $validator = Validator::make($request->all(),[
                        'working_from' => 'required', 
                        'working_to' => 'required',
                        'place_name' => 'required', 
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], '422'); 
                    }

                }
            }*/

            $password = Hash::make($request->password);
            $is_password_change = false;
            $is_fake = false;
            if($request->is_fake == true){
                $is_fake = true;
                $is_password_change = true;
                $password = Str::random(12);
            }

            $user = new User;
            $user->unique_id = generateRandomNumber();
            $user->branch_id = getBranchId();
            $user->custom_unique_id = $request->custom_unique_id;
            $user->user_type_id = $request->user_type_id;
            $user->role_id = $request->role_id;
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
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->is_secret = ($request->is_secret) ? 1:0 ;
            $user->is_fake =  $is_fake;
            $user->is_password_change =  $is_password_change;
            $user->documents = json_encode($request->documents);
            $user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->save();
            if(!empty($request->input('role_id')))
            {
                $role = Role::where('id',$request->role_id)->first();
                $user->assignRole($role->name);
            }
            if($request->user_type_id == '6'){
                $patientInfo = new PatientInformation;
                $patientInfo->patient_id = $user->id;
                $patientInfo->institute_name = $request->institute_name;
                $patientInfo->institute_contact_number = $request->institute_contact_number;
                $patientInfo->institute_full_address = $request->institute_full_address;
                $patientInfo->classes_from = $request->classes_from;
                $patientInfo->classes_to = $request->classes_to;
                $patientInfo->company_name = $request->company_name;
                $patientInfo->company_contact_number = $request->company_contact_number;
                $patientInfo->company_full_address = $request->company_full_address;
                $patientInfo->from_timing = $request->from_timing;
                $patientInfo->to_timing = $request->to_timing;
                $patientInfo->special_information = $request->special_information;
                $patientInfo->aids = $request->aids;
                $patientInfo->another_activity = $request->another_activity;
                $patientInfo->another_activity_name = $request->another_activity_name;
                $patientInfo->activitys_contact_number = $request->activitys_contact_number;
                $patientInfo->activitys_full_address = $request->activitys_full_address;
                $patientInfo->week_days = json_encode($request->week_days);
                $patientInfo->issuer_name = $request->issuer_name;
                $patientInfo->number_of_hours = $request->number_of_hours;
                $patientInfo->period = $request->period;
                $patientInfo->save();
              
            }
            if(env('IS_MAIL_ENABLE',false) == true && $is_fake == false){ 
                    $content = ([
                    'company_id' => $user->top_most_parent_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'id' => $user->id,
                    ]);   
                Mail::to($user->email)->send(new WelcomeMail($content));
            }
             /*-------------patient weekly Hours-----------------------*/
            if(is_array($request->weekly_hours) && sizeof($request->weekly_hours) >0){

                foreach ($request->weekly_hours as $key => $weekly_hours) {
                    if(!empty($weekly_hours['weekly_hours_allocated']))
                    {
                        $agencyWeeklyHour = new AgencyWeeklyHour;
                        $agencyWeeklyHour->user_id = $user->id;
                        $agencyWeeklyHour->name = $weekly_hours['name'];
                        $agencyWeeklyHour->weekly_hours_allocated = $weekly_hours['weekly_hours_allocated'];
                        $agencyWeeklyHour->start_date = $weekly_hours['start_date'];
                        $agencyWeeklyHour->end_date = $weekly_hours['end_date'];
                        $agencyWeeklyHour->save();
                    }
                }
            }
            /*-----------------Persons Informationn ----------------*/
            if($request->user_type_id == '6') {
                if(is_array($request->persons) && sizeof($request->persons) >0 ){
                    foreach ($request->persons as $key => $value) {
                        $is_user = false;
                        if(@$value['is_family_member'] == true){
                            $user_type_id ='8';
                            $is_user = true;
                        }
                        if(@$value['is_caretaker'] == true){
                            $user_type_id ='7';
                            $is_user = true;
                        }
                        if((@$value['is_caretaker'] == true) && (@$value['is_family_member'] == true )){
                            $user_type_id ='10';
                            $is_user = true;
                        }
                        if(@$value['is_contact_person'] == true){
                            $user_type_id ='9';
                            $is_user = true;
                        }
                        if(is_null(@$value['id']) == false){
                            $personalInfo = PersonalInfoDuringIp::find(@$value['id']);
                            $getperson = PersonalInfoDuringIp::where('id',@$value['id'])->first();
                            $getUser = User::where('email',$getperson->email)->first();
                        } else{
                            $personalInfo = new PersonalInfoDuringIp;
                        }
                        $personalInfo->patient_id = $user->id;
                        $personalInfo->name = @$value['name'] ;
                        $personalInfo->email = @$value['email'] ;
                        $personalInfo->contact_number = @$value['contact_number'];
                        $personalInfo->country_id = @$value['country_id'];
                        $personalInfo->city = @$value['city'];
                        $personalInfo->postal_area = @$value['postal_area'];
                        $personalInfo->zipcode = @$value['zipcode'];
                        $personalInfo->full_address = @$value['full_address'] ;
                        $personalInfo->personal_number = @$value['personal_number'] ;
                        $personalInfo->is_family_member = (@$value['is_family_member'] == true) ? @$value['is_family_member'] : 0 ;
                        $personalInfo->is_caretaker = (@$value['is_caretaker'] == true) ? @$value['is_caretaker'] : 0 ;
                        $personalInfo->is_contact_person = (@$value['is_contact_person'] == true) ? @$value['is_contact_person'] : 0 ;
                        $personalInfo->is_guardian = (@$value['is_guardian'] == true) ? @$value['is_guardian'] : 0 ;
                        $personalInfo->is_other = (@$value['is_other'] == true) ? @$value['is_other'] : 0 ;
                        $personalInfo->is_presented = (@$value['is_presented'] == true) ? @$value['is_presented'] : 0 ;
                        $personalInfo->is_participated = (@$value['is_participated'] == true) ? @$value['is_participated'] : 0 ;
                        $personalInfo->how_helped = @$value['how_helped'];
                        $personalInfo->is_other_name = @$value['is_other_name'];
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
                            $checkAlreadyUser = User::where('email',@$value['email'])->first();
                            if(empty($checkAlreadyUser)) {
                                if(!empty($getUser)){
                                    $userSave = User::find($getUser->id);
                                } else {
                                    $userSave = new User;
                                    $userSave->unique_id = generateRandomNumber();
                                }
                                $userSave->unique_id = generateRandomNumber();
                                $userSave->branch_id =   getBranchId();
                                $userSave->user_type_id = $user_type_id;
                                $userSave->role_id =  $user_type_id;
                                $userSave->parent_id = $user->id;
                                $userSave->top_most_parent_id = $top_most_parent_id;
                                $userSave->name = @$value['name'] ;
                                $userSave->email = @$value['email'] ;
                                $userSave->password = Hash::make('12345678');
                                $userSave->contact_number = @$value['contact_number'];
                                $userSave->country_id = @$value['country_id'];
                                $userSave->city = @$value['city'];
                                $userSave->postal_area = @$value['postal_area'];
                                $userSave->zipcode = @$value['zipcode'];
                                $userSave->full_address = @$value['full_address'] ;
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
            return prepareResult(true,getLangByLabelGroups('UserValidation','create') ,$user, '200');
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
         try {
            
            $checkId= User::where('id',$user->id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(falsuser,getLangByLabelGroups('UserValidation','id_not_found'), [],'404');
            }
            $userShow = User::where('id',$user->id)->with('TopMostParent:id,user_type_id,name,email','UserType:id,name','CategoryMaster:id,created_by,name','Department:id,name','Country:id,name','weeklyHours','branch','persons.Country','PatientInformation','branch:id,name,email,contact_number')->first();
            return prepareResult(true,'User View' ,$userShow, '200');
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,User $user){
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[
                'user_type_id' => 'required|exists:user_types,id', 
                'role_id' => 'required|exists:roles,id', 
                'name' => 'required', 
                //'contact_number' => 'required', 

            ],
            [
            'user_type_id.required' =>  getLangByLabelGroups('UserValidation','user_type_id'),
            'role_id.required' =>  getLangByLabelGroups('UserValidation','role_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            //'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
            
            if($request->user_type_id == '6' || $request->user_type_id =='3'){
                $validator = Validator::make($request->all(),[
                    'personal_number' => 'required|digits:12|unique:users,personal_number,'.$user->id, 
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], '422'); 
                }

            }
            
            $checkId = User::where('id',$user->id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('UserValidation','id_not_found'), [],'404');
            }
            
            $user->user_type_id = $request->user_type_id;
            $user->role_id = $request->role_id;
            $user->category_id = (!empty($request->category_id)) ? $request->category_id : $userInfo->category_id;
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
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->is_secret = ($request->is_secret) ? 1:0 ;
            $user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->documents = json_encode($request->documents);
            $user->save();
            \DB::table('model_has_roles')->where('model_id',$user->id)->delete();
            if(!empty($request->input('role_id')))
            {
                $role = Role::where('id',$request->role_id)->first();
                $user->assignRole($role->name);
            }


            if($request->user_type_id == '6'){
                $checkPatientAlready = PatientInformation::where('patient_id',$user->id)->first();
                if(is_object($checkPatientAlready)){
                    $patientInfo =  PatientInformation::find($checkPatientAlready->id);
                } else{
                    $patientInfo = new PatientInformation;
                }
                
                $patientInfo->patient_id = $user->id;
                $patientInfo->institute_name = $request->institute_name;
                $patientInfo->institute_contact_number = $request->institute_contact_number;
                $patientInfo->institute_full_address = $request->institute_full_address;
                $patientInfo->classes_from = $request->classes_from;
                $patientInfo->classes_to = $request->classes_to;
                $patientInfo->company_name = $request->company_name;
                $patientInfo->company_contact_number = $request->company_contact_number;
                $patientInfo->company_full_address = $request->company_full_address;
                $patientInfo->from_timing = $request->from_timing;
                $patientInfo->to_timing = $request->to_timing;
                $patientInfo->special_information = $request->special_information;
                $patientInfo->aids = $request->aids;
                $patientInfo->another_activity = $request->another_activity;
                $patientInfo->another_activity_name = $request->another_activity_name;
                $patientInfo->activitys_contact_number = $request->activitys_contact_number;
                $patientInfo->activitys_full_address = $request->activitys_full_address;
                $patientInfo->week_days = json_encode($request->week_days);
                $patientInfo->issuer_name = $request->issuer_name;
                $patientInfo->number_of_hours = $request->number_of_hours;
                $patientInfo->period = $request->period;
                $patientInfo->save();
              
            }

            /*-------------patient weekly Hours-----------------------*/
            if(is_array($request->weekly_hours) && sizeof($request->weekly_hours) >0){
                $deleteOld = AgencyWeeklyHour::where('user_id',$user->id)->delete();
                 foreach ($request->weekly_hours as $key => $weekly_hours) {
                    $agencyWeeklyHour = new AgencyWeeklyHour;
                    $agencyWeeklyHour->user_id = $user->id;
                    $agencyWeeklyHour->name = $weekly_hours['name'];
                    $agencyWeeklyHour->weekly_hours_allocated = $weekly_hours['weekly_hours_allocated'];
                    $agencyWeeklyHour->start_date = $weekly_hours['start_date'];
                    $agencyWeeklyHour->end_date = $weekly_hours['end_date'];
                    $agencyWeeklyHour->save();
                }
            }
             
            /*-----------------Persons Informationn ----------------*/
            if(is_array($request->persons) && sizeof($request->persons) >0){
                foreach ($request->persons as $key => $value) {
                    $is_user = false;
                    if(@$value['is_family_member'] == true){
                        $user_type_id ='8';
                        $is_user = true;
                    }
                    if(@$value['is_caretaker'] == true){
                        $user_type_id ='7';
                        $is_user = true;
                    }
                    if((@$value['is_caretaker'] == true) && (@$value['is_family_member'] == true )){
                        $user_type_id ='10';
                        $is_user = true;
                    }
                    if(@$value['is_contact_person'] == true){
                        $user_type_id ='9';
                        $is_user = true;
                    }
                    if(is_null(@$value['id']) == false){
                        $personalInfo = PersonalInfoDuringIp::find(@$value['id']);
                        $getperson = PersonalInfoDuringIp::where('id',@$value['id'])->first();
                        $getUser = User::where('email',$getperson->email)->first();
                    } else{
                        $personalInfo = new PersonalInfoDuringIp;
                    }
                    $personalInfo->patient_id = $user->id;
                    $personalInfo->name = $value['name'] ;
                    $personalInfo->email = $value['email'] ;
                    $personalInfo->contact_number = @$value['contact_number'];
                    $personalInfo->country_id = @$value['country_id'];
                    $personalInfo->city = @$value['city'];
                    $personalInfo->postal_area = @$value['postal_area'];
                    $personalInfo->zipcode = @$value['zipcode'];
                    $personalInfo->full_address = @$value['full_address'] ;
                    $personalInfo->personal_number = @$value['personal_number'] ;
                    $personalInfo->is_family_member = (@$value['is_family_member'] == true) ? @$value['is_family_member'] : 0 ;
                    $personalInfo->is_caretaker = (@$value['is_caretaker'] == true) ? @$value['is_caretaker'] : 0 ;
                    $personalInfo->is_contact_person = (@$value['is_contact_person'] == true) ? @$value['is_contact_person'] : 0 ;
                    $personalInfo->is_guardian = (@$value['is_guardian'] == true) ? @$value['is_guardian'] : 0 ;
                    $personalInfo->is_other = (@$value['is_other'] == true) ? @$value['is_other'] : 0 ;
                    $personalInfo->is_presented = (@$value['is_presented'] == true) ? @$value['is_presented'] : 0 ;
                    $personalInfo->is_participated = (@$value['is_participated'] == true) ? @$value['is_participated'] : 0 ;
                    $personalInfo->how_helped = @$value['how_helped'];
                    $personalInfo->is_other_name = @$value['is_other_name'];
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
                        $checkAlreadyUser = User::where('email',@$value['email'])->first();
                        if(empty($checkAlreadyUser)) {
                            if(!empty($getUser)){
                                $userSave = User::find($getUser->id);
                            } else {
                                $userSave = new User;
                                $userSave->unique_id = generateRandomNumber();
                            }
                            $userSave->branch_id = getBranchId();
                            $userSave->user_type_id = $user_type_id;
                            $userSave->role_id =  $user_type_id;
                            $userSave->parent_id = $user->id;
                            $userSave->top_most_parent_id = $top_most_parent_id;
                            $userSave->name = @$value['name'] ;
                            $userSave->email = @$value['email'] ;
                            $userSave->password = Hash::make('12345678');
                            $userSave->contact_number = @$value['contact_number'];
                            $userSave->country_id = @$value['country_id'];
                            $userSave->city = @$value['city'];
                            $userSave->postal_area = @$value['postal_area'];
                            $userSave->zipcode = @$value['zipcode'];
                            $userSave->full_address = @$value['full_address'] ;
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
            return prepareResult(true,getLangByLabelGroups('UserValidation','update'),$user, '200');
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[],'500');
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            
            $id = $user->id;
            $checkId= User::where('id',$id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('UserValidation','id_not_found'), [],'404');
            }
            $updateStatus = User::where('id',$id)->update(['status'=>'2']);
            $userDelete = User::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('UserValidation','delete'),[], '200');
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }

    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('user_type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "user_type_id = "."'" .$request->input('user_type_id')."'".")";
        }

        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }

        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        if (is_null($request->input('category_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "category_id = "."'" .$request->input('category_id')."'".")";
        }
        if (is_null($request->input('dept_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "dept_id = "."'" .$request->input('dept_id')."'".")";
        }
        if (is_null($request->input('patient_type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_type_id = "."'" .$request->input('patient_type_id')."'".")";
        }
        if (is_null($request->input('name')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "name like '%" .trim(strtolower($request->input('name'))) . "%')";
             
        }
        if (is_null($request->input('email')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "email like '%" .trim(strtolower($request->input('email'))) . "%')";
             
        }
        if (is_null($request->input('contact_number')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "contact_number like '%" .trim(strtolower($request->input('contact_number'))) . "%')";
             
        }
        if (is_null($request->input('personal_number')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "personal_number like '%" .trim(strtolower($request->input('personal_number'))) . "%')";
             
        }
        if (is_null($request->input('organization_number')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "organization_number like '%" .trim(strtolower($request->input('organization_number'))) . "%')";
             
        }
        return($w);

    }
}
