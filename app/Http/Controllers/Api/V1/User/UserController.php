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
            if(!empty($user->branch_id)) {
                $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
    		$query = User::select('id','unique_id','custom_unique_id','user_type_id', 'company_type_id','patient_type_id', 'category_id', 'top_most_parent_id', 'parent_id','branch_id','country_id','city', 'dept_id', 'govt_id','name', 'email', 'email_verified_at','contact_number','user_color', 'gender','organization_number', 'personal_number','joining_date','is_fake','is_secret','is_password_change','status','step_one','step_two','step_three','step_four','step_five', 
    			DB::raw("(SELECT count(*) from patient_implementation_plans WHERE patient_implementation_plans.user_id = users.id AND is_latest_entry = 1) ipCount"), 
    			DB::raw("(SELECT count(*) from activity_assignes WHERE activity_assignes.user_id = users.id ) assignActivityCount"), 
    			DB::raw("(SELECT count(*) from activities WHERE activities.patient_id = users.id  AND is_latest_entry = 1) patientActivityCount"), 
    			DB::raw("(SELECT count(*) from assign_tasks WHERE assign_tasks.user_id = users.id ) assignTaskCount"), 
    			DB::raw("(SELECT count(*) from tasks WHERE tasks.resource_id = users.id AND tasks.type_id = 7 AND is_latest_entry = 1) patientTaskCount"), 
    			DB::raw("(SELECT count(*) from personal_info_during_ips WHERE personal_info_during_ips.patient_id = users.id ) personCount"), 
    			DB::raw("(SELECT count(*) from journals WHERE journals.patient_id = users.id ) journals_count"), 
    			DB::raw("(SELECT count(*) from deviations WHERE deviations.patient_id = users.id ) deviations_count"))->where('top_most_parent_id',$this->top_most_parent_id)
    		->with('TopMostParent:id,user_type_id,name,email','Parent:id,name','UserType:id,name','Country','weeklyHours','PatientInformation','persons.Country','branch:id,name');
    		if(in_array($user->user_type_id, [1,2,3,4,5,11,16]))
    		{
                $query =  $query->where('id', '!=',$user->id);
    		}
    		else
    		{
    			$query =  $query->where(function ($q) use ($user) {
    				$q->where('id', $user->id)
    				->orWhere('id', $user->parent_id);
    			});
    		}

    		if($user->user_type_id =='2') {
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

    public function store(Request $request) 
    {
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
    			'user_type_id.required' =>  getLangByLabelGroups('UserValidation','message_user_type_id'),
    			'role_id.required' =>  getLangByLabelGroups('UserValidation','message_role_id'),
    			'name.required' =>  getLangByLabelGroups('UserValidation','message_name'),
    			'email.required' =>  getLangByLabelGroups('UserValidation','message_email'),
    			'email.email' =>  getLangByLabelGroups('UserValidation','message_email_invalid'),
    		]);
    		if ($validator->fails()) {
    			return prepareResult(false,$validator->errors()->first(),[], '422'); 
    		}

            //check Permission for create employee


            ////////////////////////////////////////

    		if($request->user_type_id  != '6'){
    			$validator = Validator::make($request->all(),[
    				'password'  => 'required|same:confirm-password|min:8|max:30', 
    				'contact_number' => 'required', 

    			],
    			[
    				'password.required' =>  getLangByLabelGroups('UserValidation','message_password'),
    				'password.min' =>  getLangByLabelGroups('UserValidation','message_password_min'),
    				'contact_number' =>  getLangByLabelGroups('UserValidation','message_contact_number'),
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


    		$password = Hash::make($request->password);
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
            $user->avatar = (!empty($request->avatar)) ? $request->avatar :'https://aceuss.3mad.in/uploads/no-image.png';
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
    			$patientInfo->from_timing = $request->from_timing;
    			$patientInfo->to_timing = $request->to_timing;
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
    				if(!empty(@$weekly_hours['weekly_hours_allocated']))
    				{
    					$agencyWeeklyHour = new AgencyWeeklyHour;
    					$agencyWeeklyHour->user_id = $user->id;
    					$agencyWeeklyHour->name = @$weekly_hours['name'];
    					$agencyWeeklyHour->weekly_hours_allocated = @$weekly_hours['weekly_hours_allocated'];
    					$agencyWeeklyHour->start_date = @$weekly_hours['start_date'];
    					$agencyWeeklyHour->end_date = @$weekly_hours['end_date'];
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
    						$personalInfo->is_family_member = (@$value['is_family_member'] == true) ? @$value['is_family_member'] : 0;
    						$personalInfo->is_caretaker = (@$value['is_caretaker'] == true) ? @$value['is_caretaker'] : 0;
    						$personalInfo->is_contact_person = (@$value['is_contact_person'] == true) ? @$value['is_contact_person'] : 0;
    						$personalInfo->is_guardian = (@$value['is_guardian'] == true) ? @$value['is_guardian'] : 0;
    						$personalInfo->is_other = (@$value['is_other'] == true) ? @$value['is_other'] : 0;
    						$personalInfo->is_presented = (@$value['is_presented'] == true) ? @$value['is_presented'] : 0;
    						$personalInfo->is_participated = (@$value['is_participated'] == true) ? @$value['is_participated'] : 0;
    						$personalInfo->how_helped = @$value['how_helped'];
    						$personalInfo->is_other_name = @$value['is_other_name'];
    						$personalInfo->save() ;
    						/*-----Create Account /Entry in user table*/
    						if($is_user == true) {
    							$top_most_parent_id = auth()->user()->top_most_parent_id;
    							$checkAlreadyUser = User::where('email',@$value['email'])->first();
    							if(empty($checkAlreadyUser)) {
    								$getUserType = UserType::find($user_type_id);
    								$roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);
    								if(!empty($getUser)){
    									$userSave = User::find($getUser->id);
    								} else {
    									$userSave = new User;
    									$userSave->unique_id = generateRandomNumber();
    								}
    								$userSave->unique_id = generateRandomNumber();
    								$userSave->branch_id =   getBranchId();
    								$userSave->user_type_id = $user_type_id;
    								$userSave->role_id =  $roleInfo->id;
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

                                    //update personal_info_during_ips
    								$personalInfo->user_id =$userSave->id;
    								$personalInfo->save();

    								if(!empty($user_type_id))
    								{
    									$role = $roleInfo;
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
    		}
    		DB::commit();
    		$user['branch'] = $user->branch()->select('id', 'name')->first();
    		return prepareResult(true,getLangByLabelGroups('UserValidation','message_create') ,$user, '200');
    	}
    	catch(Exception $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], '500');

    	}
    }

    public function show(User $user)
    {
    	try {

    		$checkId= User::where('id', $user->id)->where('top_most_parent_id', $this->top_most_parent_id)->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('UserValidation','message_id_not_found'), [],'404');
    		}
    		$userShow = User::where('id',$user->id)->with('TopMostParent:id,user_type_id,name,email','UserType:id,name','CategoryMaster:id,created_by,name','Department:id,name','Country:id,name','weeklyHours','branch','persons.Country','PatientInformation','branch:id,name,email,contact_number')->first();
    		return prepareResult(true,'User View' ,$userShow, '200');

    	}
    	catch(Exception $exception) {
    		return prepareResult(false, $exception->getMessage(),[], '500');

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
    			'user_type_id.required' =>  getLangByLabelGroups('UserValidation','message_user_type_id'),
    			'role_id.required' =>  getLangByLabelGroups('UserValidation','message_role_id'),
    			'name.required' =>  getLangByLabelGroups('UserValidation','message_name'),
    		]);
    		if ($validator->fails()) {
    			return prepareResult(false,$validator->errors()->first(),[], '422'); 
    		}

    		if($request->user_type_id == '6' || $request->user_type_id =='3')
    		{
    			$validator = Validator::make($request->all(),[
    				'personal_number' => 'required|digits:12|unique:users,personal_number,'.$user->id, 
    			]);
    			if ($validator->fails()) {
    				return prepareResult(false,$validator->errors()->first(),[], '422'); 
    			}
    		}

    		$checkId = User::where('id',$user->id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false, getLangByLabelGroups('UserValidation','message_id_not_found'), [],'404');
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
    		$user->branch_id = $request->branch_id;
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
            $user->contract_value = $request->contract_value;
    		$user->is_file_required = ($request->is_file_required) ? 1:0;
    		$user->is_secret = ($request->is_secret) ? 1:0;
    		$user->step_one = (!empty($request->step_one)) ? $request->step_one:0;
    		$user->step_two = (!empty($request->step_two)) ? $request->step_two:0;
    		$user->step_three = (!empty($request->step_three)) ? $request->step_three:0;
    		$user->step_four = (!empty($request->step_four)) ? $request->step_four:0;
    		$user->step_five = (!empty($request->step_five)) ? $request->step_five:0;
    		$user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
    		$user->documents = is_array($request->documents) ? json_encode($request->documents) : null;
    		$user->contact_person_name = $request->contact_person_name;
            $user->avatar = (!empty($request->avatar)) ? $request->avatar :'https://aceuss.3mad.in/uploads/no-image.png';
    		$user->save();

    		if($roleInfo)
    		{
    			\DB::table('model_has_roles')->where('model_id',$user->id)->delete();
    			$role = $roleInfo;
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
    			$patientInfo->from_timing = $request->from_timing;
    			$patientInfo->to_timing = $request->to_timing;
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

    		}

    		/*-------------patient weekly Hours-----------------------*/
    		if(is_array($request->weekly_hours) && sizeof($request->weekly_hours) >0){
    			$deleteOld = AgencyWeeklyHour::where('user_id',$user->id)->delete();
    			foreach ($request->weekly_hours as $key => $weekly_hours) {
    				if(!empty(@$weekly_hours['weekly_hours_allocated']))
    				{
    					$agencyWeeklyHour = new AgencyWeeklyHour;
    					$agencyWeeklyHour->user_id = $user->id;
    					$agencyWeeklyHour->name = @$weekly_hours['name'];
    					$agencyWeeklyHour->weekly_hours_allocated = @$weekly_hours['weekly_hours_allocated'];
    					$agencyWeeklyHour->start_date = @$weekly_hours['start_date'];
    					$agencyWeeklyHour->end_date = @$weekly_hours['end_date'];
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
    					$personalInfo->is_family_member = (@$value['is_family_member'] == true) ? @$value['is_family_member'] : 0;
    					$personalInfo->is_caretaker = (@$value['is_caretaker'] == true) ? @$value['is_caretaker'] : 0;
    					$personalInfo->is_contact_person = (@$value['is_contact_person'] == true) ? @$value['is_contact_person'] : 0;
    					$personalInfo->is_guardian = (@$value['is_guardian'] == true) ? @$value['is_guardian'] : 0;
    					$personalInfo->is_other = (@$value['is_other'] == true) ? @$value['is_other'] : 0;
    					$personalInfo->is_presented = (@$value['is_presented'] == true) ? @$value['is_presented'] : 0;
    					$personalInfo->is_participated = (@$value['is_participated'] == true) ? @$value['is_participated'] : 0;
    					$personalInfo->how_helped = @$value['how_helped'];
    					$personalInfo->is_other_name = @$value['is_other_name'];
    					$personalInfo->save() ;
    					/*-----Create Account /Entry in user table*/
    					if($is_user == true) {
    						$top_most_parent_id = auth()->user()->top_most_parent_id;

    						$checkAlreadyUser = User::where('email',@$value['email'])->first();
    						if(empty($checkAlreadyUser)) {
    							$getUserType = UserType::find($user_type_id);
    							$roleInfo = getRoleInfo($top_most_parent_id, $getUserType->name);

    							if(!empty($getUser)){
    								$userSave = User::find($getUser->id);
    							} else {
    								$userSave = new User;
    								$userSave->unique_id = generateRandomNumber();
    							}
    							$userSave->branch_id = getBranchId();
    							$userSave->user_type_id = $user_type_id;
    							$userSave->role_id =  $roleInfo->id;
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

                                //update personal_info_during_ips
    							$personalInfo->user_id =$userSave->id;
    							$personalInfo->save();

    							if(!empty($user_type_id))
    							{
    								$role = $roleInfo;
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

    		DB::commit();
    		$user['branch'] = $user->branch()->select('id', 'name')->first();
    		return prepareResult(true,getLangByLabelGroups('UserValidation','message_update'),$user, '200');

    	}
    	catch(Exception $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[],'500');

    	}
    }

    public function destroy(User $user)
    {
    	try {

    		$id = $user->id;
    		$checkId= User::where('id',$id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
    		if (!is_object($checkId)) {
    			return prepareResult(false,getLangByLabelGroups('UserValidation','message_id_not_found'), [],'404');
    		}

            if($user->id == auth()->id())
            {
                return prepareResult(false,getLangByLabelGroups('common','cant_delete'), [],'503');
            }

    		$updateStatus = User::where('id',$id)->update(['status'=>'2']);
    		$userDelete = User::where('id',$id)->delete();
    		return prepareResult(true, getLangByLabelGroups('UserValidation','message_delete'),[], '200');

    	}
    	catch(Exception $exception) {
    		return prepareResult(false, $exception->getMessage(),[], '500');

    	}
    }

    private function getWhereRawFromRequest(Request $request) 
    {
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


    public function getLicenceStatus()
    {
    	try 
    	{
    		$licenceKeyData = LicenceKeyManagement::where('top_most_parent_id',Auth::id())->where('is_used',1)->latest()->first();

    		if(empty($licenceKeyData))
    		{
    			return prepareResult(false,getLangByLabelGroups('LicenceKey','message_data_doesnt_exist') ,[], config('httpcodes.success'));
    		}

    		if($licenceKeyData->expire_at >= date('Y-m-d'))
    		{
    			return prepareResult(true,getLangByLabelGroups('LicenceKey','message_status_active') ,'active', config('httpcodes.success'));
    		}
    		else
    		{
    			Auth::user()->update(['license_status' => 0]);
    			return prepareResult(true,getLangByLabelGroups('LicenceKey','message_status_active') ,'inactive', config('httpcodes.success'));
    		}
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }


}
