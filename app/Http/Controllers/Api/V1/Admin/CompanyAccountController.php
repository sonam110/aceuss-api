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
            $user = getUser();
            $query = User::select('users.*')->where('status','1')->with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details','assignedModule:id,user_id,module_id','assignedModule.module:id,name')->withCount('tasks','activities','ips','followUps','patients','employees','assignedModule','branchs')->where('role_id','2') ;
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

            ],
            [
            'company_type_id.required' =>  getLangByLabelGroups('UserValidation','company_type_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','email_invalid'),
            'password.required' =>  getLangByLabelGroups('UserValidation','password'),
            'password.min' =>  getLangByLabelGroups('UserValidation','password_min'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

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
            $user->license_key = $request->license_key;
            $user->license_end_date = $request->license_end_date;
            $user->joining_date = $request->joining_date;
            $user->establishment_year = $request->establishment_year;
            $user->user_color = $request->user_color;
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
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
                $createLicHistory->license_key = $request->license_key;
                $createLicHistory->active_from = date('Y-m-d');
                $createLicHistory->expire_at = $request->license_end_date;
                $createLicHistory->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $createLicHistory->save();

                // Create Licence Key
                $keyMgmt = new LicenceKeyManagement;
                $keyMgmt->top_most_parent_id = $user->id;
                $keyMgmt->created_by = auth()->id();
                $keyMgmt->license_key = $request->license_key;
                $keyMgmt->active_from = date('Y-m-d');
                $keyMgmt->expire_at = $request->license_end_date;
                $keyMgmt->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $keyMgmt->is_used = true;
                $keyMgmt->save();
            }
            
            if(env('IS_MAIL_ENABLE', false) == true){ 
                $content = ([
                    'company_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'id' => $user->id,
                ]);  
                Mail::to($user->email)->send(new WelcomeMail($content));
            }
            if(!empty($request->package_id)) {
                $validator = Validator::make($request->all(),[ 
                    "package_id"    => "required|exists:packages,id",
                 ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
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
            if(is_array($request->modules)  && sizeof($request->modules) >0){
                for ($i = 0;$i < sizeof($request->modules);$i++) {
                    if (!empty($request->modules[$i])) {
                        $assigneModule = new AssigneModule;
                        $assigneModule->user_id = $user->id;
                        $assigneModule->module_id = $request->modules[$i] ;
                        $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                        $assigneModule->save();
                    }
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
            $userdetail = User::select('users.*')->with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details','assignedModule:id,user_id,module_id','assignedModule.module:id,name')->withCount('tasks','activities','ips','followUps','patients','employees','assignedModule','branchs')
                ->where('id',$user->id)
                ->first();
            return prepareResult(true,getLangByLabelGroups('UserValidation','create') ,$userdetail, config('httpcodes.success'));
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
                return prepareResult(false,getLangByLabelGroups('UserValidation','id_not_found'), [],config('httpcodes.not_found'));
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
            'user_type_id.required' =>  getLangByLabelGroups('UserValidation','user_type_id'),
            'role_id.required' =>  getLangByLabelGroups('UserValidation','role_id'),
            'company_type_id.required' =>  getLangByLabelGroups('UserValidation','company_type_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $checkId = User::where('id',$user->id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('UserValidation','id_not_found'), [],config('httpcodes.not_found'));
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
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->status = ($request->status) ? $request->status: 1 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
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
                for ($i = 0;$i < sizeof($request->modules);$i++) {
                    if (!empty($request->modules[$i])) {
                        $assigneModule = new AssigneModule;
                        $assigneModule->user_id = $user->id;
                        $assigneModule->module_id = $request->modules[$i] ;
                        $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                        $assigneModule->save();
                    }
                }
            }
            DB::commit();
            $userdetail = User::select('users.*')->where('status','1')->with('Parent:id,name','UserType:id,name','Country:id,name','Subscription:user_id,package_details','assignedModule:id,user_id,module_id','assignedModule.module:id,name')->withCount('tasks','activities','ips','followUps','patients','employees','assignedModule','branchs')
                ->where('id',$user->id)
                ->first();;
            return prepareResult(true,getLangByLabelGroups('UserValidation','update'),$userdetail, config('httpcodes.success'));
                
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
                return prepareResult(false,getLangByLabelGroups('UserValidation','id_not_found'), [],config('httpcodes.not_found'));
            }
            $updateStatus = User::where('id',$id)->update(['status'=>'2']);
            $userDelete = User::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('UserValidation','delete'),[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        return($w);
    }
}
