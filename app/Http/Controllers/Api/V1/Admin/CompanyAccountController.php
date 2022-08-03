<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\AssigneModule;
use App\Models\CategoryType;
use App\Models\Module;
use App\Models\EmailTemplate;
use App\Models\CompanySetting;
use Mail;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Str;
use Exception;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\LicenceHistory;
use App\Models\LicenceKeyManagement;
use App\Models\Deviation;

class CompanyAccountController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:companies-browse',['except' => ['show']]);
        $this->middleware('permission:companies-add', ['only' => ['store']]);
        $this->middleware('permission:companies-edit', ['only' => ['update']]);
        $this->middleware('permission:companies-read', ['only' => ['show']]);
        $this->middleware('permission:companies-delete', ['only' => ['destroy']]);
        
       
    }

    public function companies(Request $request)
    {
        try {
            $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
            $user = getUser();
            $query = User::select('users.id','users.unique_id','users.custom_unique_id','users.user_type_id', 'users.company_type_id','users.patient_type_id', 'users.category_id', 'users.top_most_parent_id', 'users.parent_id','users.branch_id','users.country_id','users.city', 'users.dept_id', 'users.govt_id','users.name', 'users.email', 'users.email_verified_at','users.contact_number','users.user_color', 'users.gender','users.organization_number', 'users.personal_number','users.joining_date','users.is_fake','users.is_secret','users.employee_type','users.is_password_change','users.status','users.step_one','users.step_two','users.step_three','users.step_four','users.step_five')
            ->where('users.status','1')
            ->with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details','assignedModule:id,user_id,module_id','assignedModule.module:id,name')
            ->withCount(
                [
                    'tasks' => function ($query) use ($date) {
                        $query->where('start_date','>=',$date);
                    },
                    'activities' => function ($query) use ($date) {
                        $query->where('start_date','>=',$date);
                    },
                    'ips' => function ($query) use ($date) {
                        $query->where('start_date','>=',$date);
                    },
                    'followUps' => function ($query) use ($date) {
                        $query->where('start_date','>=',$date);
                    },
                    'patients','employees','assignedModule','branchs'
                ]
            )
            ->where('users.role_id','2')
            ->orderBy('users.id', 'DESC');

            if(!empty($request->company_type_id))
            {
                $query->whereJsonContains('users.company_type_id', $request->company_type_id);
            }

            if(!empty($request->contact_number))
            {
                $query->where('users.contact_number', $request->contact_number);
            }

            if(!empty($request->email))
            {
                $query->where('users.email', $request->email);
            }

            if(!empty($request->name))
            {
                $query->where('users.name',"like", "%".$request->name."%");
            }

            if(!empty($request->organization_number))
            {
                $query->where('users.organization_number',"like", "%".$request->organization_number."%");
            }
            if(!empty($request->status) && $request->status==1)
            {
                $query->where('users.status', 1);
            }
            elseif($request->status=='no')
            {
                $query->where('users.status', 0);
            }

            if(!empty($request->licence_end_date))
            {
                $query->where('users.licence_end_date', $request->licence_end_date);
            }

            if(!empty($request->package_id))
            {
                $query->join('subscriptions', function($join) use ($request) {
                    $join->on('users.id', '=', 'subscriptions.user_id');
                })
                ->where('subscriptions.package_id', $request->package_id)
                ->where('subscriptions.status', 1)
                ->groupBy('subscriptions.user_id');
            }

            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->withCount('tasks','activities','ips','followUps','patients','employees')->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"User list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->withCount('tasks','activities','ips','followUps','patients','employees')->get();
            }
            return prepareResult(true,"User list",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[ 
                "company_type_id"    => "required|array",
                "company_type_id.*"  => "required|distinct",
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|same:confirm-password|min:8|max:30',
                'contact_number' => 'required', 
                "package_id"    => "required|exists:packages,id",

            ],
            [
            'company_type_id.required' =>  getLangByLabelGroups('UserValidation','message_company_type_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','message_name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','message_email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','message_email_invalid'),
            'password.required' =>  getLangByLabelGroups('UserValidation','message_password'),
            'password.min' =>  getLangByLabelGroups('UserValidation','message_password_min'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','message_contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $package = Package::where('id',$request->package_id)->first();
            $package_expire_at = date('Y-m-d', strtotime($package->validity_in_days.' days'));

            $user = new User;
            $user->unique_id = generateRandomNumber();
            $user->user_type_id = '2';
            $user->role_id = '2';
            $user->company_type_id = ($request->company_type_id) ? json_encode($request->company_type_id) : null;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->contact_number = $request->contact_number;
            $user->organization_number = $request->organization_number;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->licence_key = $request->licence_key;
            $user->licence_end_date = $package_expire_at;
            $user->joining_date = $request->joining_date;
            $user->establishment_year = $request->establishment_year;
            $user->user_color = $request->user_color;
            $user->employee_type = $request->employee_type;
            $user->contract_type = $request->contract_type;
            $user->report_verify = $request->report_verify;
            $user->contract_value = $request->contract_value;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->documents = json_encode($request->documents);
            $user->avatar = (!empty($request->avatar)) ? $request->avatar :'https://aceuss.3mad.in/uploads/no-image.png';
            $user->save();


            $update_top_most_parent = User::where('id',$user->id)->update(['top_most_parent_id'=>$user->id]);
            
            $role = Role::where('id','2')->first();
            $user->assignRole($role->name);

            /*------Company Settings---------------*/
            $addSettings = new CompanySetting;
            $addSettings->user_id = $user->id;
            $addSettings->company_name = $request->name;
            $addSettings->company_email = $request->email;
            $addSettings->company_contact = $request->contact_number;
            $addSettings->company_address = $request->full_address;
            $addSettings->contact_person_name = $request->contact_person_name;
            $addSettings->contact_person_email = $request->contact_person_email;
            $addSettings->contact_person_phone = $request->contact_person_phone;
            $addSettings->company_website = $request->company_website;
            $addSettings->save();
            if($addSettings)
            {
                // Create Licence History
                $createLicHistory = new LicenceHistory;
                $createLicHistory->top_most_parent_id = $user->id;
                $createLicHistory->created_by = auth()->id();
                $createLicHistory->licence_key = $request->licence_key;
                $createLicHistory->active_from = date('Y-m-d');
                $createLicHistory->expire_at = $package_expire_at;
                $createLicHistory->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $createLicHistory->package_details = $package;
                $createLicHistory->save();

                // Create Licence Key
                $keyMgmt = new LicenceKeyManagement;
                $keyMgmt->top_most_parent_id = $user->id;
                $keyMgmt->created_by = auth()->id();
                $keyMgmt->licence_key = $request->licence_key;
                $keyMgmt->active_from = date('Y-m-d');
                $keyMgmt->expire_at = $package_expire_at;
                $keyMgmt->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $keyMgmt->package_details = $package;
                $keyMgmt->is_used = true;
                $keyMgmt->save();

                
                $packageSubscribe = new Subscription;
                $packageSubscribe->user_id = $user->id;
                $packageSubscribe->package_id = $request->package_id;
                $packageSubscribe->package_details = $package;
                $packageSubscribe->licence_key = $request->licence_key;
                $packageSubscribe->start_date = date('Y-m-d');
                $packageSubscribe->end_date = $package_expire_at;
                $packageSubscribe->status = 1;
                $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $packageSubscribe->save();
            }


            if(is_array($request->modules)  && sizeof($request->modules) >0)
            { 
                foreach ($request->modules as $key => $module) 
                {
                    $assigneModule = new AssigneModule;
                    $assigneModule->user_id = $user->id;
                    $assigneModule->module_id = $module;
                    $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                    $assigneModule->save();
                }
            }



            $roles = Role::where('is_default','1')->whereNull('top_most_parent_id')->get();
            if(!empty($roles)) {
                foreach ($roles as $key => $role) {
                    $addRole = new Role;
                    $addRole->top_most_parent_id = $user->id;
                    $addRole->name = $user->id.'-'.Str::slug(substr($role->se_name, 0, 20));
                    $addRole->se_name  = $role->se_name;
                    $addRole->guard_name  = 'api';
                    $addRole->user_type_id  = $role->user_type_id;
                    $addRole->entry_mode  = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                    $addRole->save();

                    //permissions assigned
                    foreach ($role->permissions as $key => $permission) {
                        $addRole->givePermissionTo($permission->name);
                    }
                }
            }
            DB::commit();

            if(env('IS_MAIL_ENABLE', false) == true){ 
                $content = ([
                    'company_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'id' => $user->id,
                ]);  
                Mail::to($user->email)->send(new WelcomeMail($content));
            }
            $userdetail = User::select('users.*')->with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details','assignedModule:id,user_id,module_id','assignedModule.module:id,name')->withCount('tasks','activities','ips','followUps','patients','employees','assignedModule','branchs')
                ->where('id',$user->id)
                ->first();
            return prepareResult(true,getLangByLabelGroups('UserValidation','message_create') ,$userdetail, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function show(User $user)
    {
        try {
            
            $checkId= User::where('id',$user->id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('UserValidation','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            $userShow = User::select('users.*')->where('status','1')->with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details','assignedModule:id,user_id,module_id','assignedModule.module:id,name')->withCount('tasks','activities','ips','followUps','patients','employees','assignedModule','branchs')
                ->where('id',$user->id)
                ->first();;

            return prepareResult(true,'User View' ,$userShow, config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request, User $user)
    { 
        DB::beginTransaction();
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[
                'user_type_id' => 'required', 
                'role_id' => 'required', 
                'company_type_id' => 'required', 
                'name' => 'required', 
                'contact_number' => 'required', 

            ],
            [
            'user_type_id.required' =>  getLangByLabelGroups('UserValidation','message_user_type_id'),
            'role_id.required' =>  getLangByLabelGroups('UserValidation','message_role_id'),
            'company_type_id.required' =>  getLangByLabelGroups('UserValidation','message_company_type_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','message_name'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','message_contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $checkId = User::where('id',$user->id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('UserValidation','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            
            $user->company_type_id = ($request->company_type_id) ? json_encode($request->company_type_id) : null;
            $user->name = $request->name;
            $user->contact_number = $request->contact_number;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->joining_date = $request->joining_date;
            $user->establishment_year = $request->establishment_year;
            $user->user_color = $request->user_color;
            $user->employee_type = $request->employee_type;
            $user->contract_type = $request->contract_type;
            $user->report_verify = $request->report_verify;
            $user->contract_value = $request->contract_value;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->status = ($request->status) ? $request->status: 1 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->contact_person_name = $request->contact_person_name;
            $user->documents = json_encode($request->documents);
            $user->avatar = (!empty($request->avatar)) ? $request->avatar :'https://aceuss.3mad.in/uploads/no-image.png';
            $user->save();
            if(!empty($request->package_id)){
                $validator = Validator::make($request->all(),[ 
                    "package_id"    => "required|exists:packages,id",
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }
                $checkAlreadySubsc = Subscription::where('user_id',$user->id)->first();
                if(!is_object($checkAlreadySubsc)){
                     return prepareResult(false,'User  has already one subsciption',[], config('httpcodes.bad_request')); 
                }
                
                $package = Package::where('id',$request->package_id)->first();
                $packageSubscribe = new Subscription;
                $packageSubscribe->user_id = $user->id;
                $packageSubscribe->package_id = $request->package_id;
                $packageSubscribe->package_details = $package;
                $packageSubscribe->start_date = date('Y-m-d');
                $packageSubscribe->end_date = date('Y-m-d', strtotime("+".$package->validity_in_days." days"));
                $packageSubscribe->status = '1';
                $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $packageSubscribe->save();
            }

            if(is_array($request->modules)  && sizeof($request->modules) >0 ){
                $deletOld = AssigneModule::where('user_id',$user->id)->delete();
                foreach ($request->modules as $key => $module) 
                {
                    $assigneModule = new AssigneModule;
                    $assigneModule->user_id = $user->id;
                    $assigneModule->module_id = $module;
                    $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                    $assigneModule->save();
                }
            }

            DB::commit();
            $userdetail = User::select('users.*')->where('status','1')->with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details','assignedModule:id,user_id,module_id','assignedModule.module:id,name')->withCount('tasks','activities','ips','followUps','patients','employees','assignedModule','branchs')
                ->where('id',$user->id)
                ->first();;
            return prepareResult(true,getLangByLabelGroups('UserValidation','message_update'),$userdetail, config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy(User $user)
    {
        try {
            $id = $user->id;
            $checkId= User::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('UserValidation','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            $updateStatus = User::where('id',$id)->update(['status'=>'2']);
            $userDelete = User::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('UserValidation','message_delete'),[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    public function companyStats(Request $request,$id)
    {
        try 
        { 
            $data_of = !empty($request->data_of) ? $request->data_of : 7;
            $date = date('Y-m-d',strtotime('-'. $data_of));
            // for($i=$request->data_of; $i>=1; $i--)
            // {
            //     $dates['dates'][] = date('Y-m-d',strtotime('-'.$i.' day'));
            // }
            // return $dates;

            
            $user = User::find($id);
            if (!is_object($user)) {
                return prepareResult(false,getLangByLabelGroups('UserValidation','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            $data = [];
            for($i=$data_of; $i>=0; $i--)
            {
                $date = date('Y-m-d',strtotime('-'.$i.' day'));
                $previous_date = date('Y-m-d',strtotime('-'.($i + 1).' day'));
                $data['date_labels'][] = $date;
                $data['company_employees_count'][] = $user->employees->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
                $data['company_patients_count'][] = $user->patients->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
                $data['company_tasks_count'][] = $user->tasks->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
                $data['company_activities_count'][] = $user->activities->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
                $data['company_ips_count'][] = $user->ips->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
                $data['company_followUps_count'][] = $user->followUps->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
                $data['company_assignedModule_count'][] = $user->assignedModule->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
                $data['company_branchs_count'][] = $user->branchs->where('created_at','<',$date)->where('created_at','>=',$previous_date)->count();
            }

            return prepareResult(true,'User Stats' ,$data, config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    
}
