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
use Mail;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class CompanyAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
    }
    public function companies(Request $request)
    {
        try {
            $user = getUser();
            $query = User::select(array('users.*', DB::raw("(SELECT count(*) from users WHERE users.top_most_parent_id = users.id and users.user_type_id ='2') employeeCount"), DB::raw("(SELECT count(*) from users WHERE users.top_most_parent_id = users.id and users.user_type_id ='6') patrientCount")))->where('status','1')->with('Parent:id,name','UserType:id,name','CompanyType:id,created_by,name','Country:id,name','Subscription:user_id,package_details')->where('role_id','2') ;
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
                return prepareResult(true,"User list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"User list",$query,$this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[ 
                'company_type_id' => 'required', 
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
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }

            $user = new User;
            $user->user_type_id = '2';
            $user->role_id = '2';
            $user->company_type_id = $request->company_type_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->license_key = $request->license_key;
            $user->license_end_date = $request->license_end_date;
            $user->joining_date = $request->joining_date;
            $user->establishment_date = $request->establishment_date;
            $user->user_color = $request->user_color;
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->save();
            activity()
           ->causedBy($user)
           ->log($user);
            $update_top_most_parent = User::where('id',$user->id)->update(['top_most_parent_id'=>$user->id]);
            
            $role = Role::where('id','2')->first();
            $user->assignRole($role->name);
            
            if(env('IS_MAIL_ENABLE',false) == true){ 
                    $variables = ([
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                    'city' => $user->city,
                    'zipcode' => $user->zipcode,
                    ]);   
                $emailTem = EmailTemplate::where('id','2')->first();           
                $content = mailTemplateContent($emailTem->content,$variables);
                Mail::to($user->email)->send(new WelcomeMail($content));
            }
            if($request->package_id) {
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
            if(is_array($request->modules) ){
                for ($i = 0;$i < sizeof($request->modules);$i++) {
                    if (!empty($request->modules[$i])) {
                        $assigneModule = new AssigneModule;
                        $assigneModule->user_id = $user->id;
                        $assigneModule->module_id = $request->modules[$i] ;
                        $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                        $assigneModule->save() ;
          

                    }
                }
            }

            /*$categoryTypes = CategoryType::where('created_by','1')->get();
            if(!empty($categoryTypes)) {
                foreach ($categoryTypes as $key => $type) {
                    $addType = new CategoryType;
                    $addType->top_most_parent_id = $user->id;
                    $addType->created_by = $user->id;
                    $addType->name = $type->name;
                    $addType->status = '1';
                    $addType->save();
                }
            }*/
            $userdetail = User::with('Parent:id,name','UserType:id,name','CompanyType:id,created_by,name','Country:id,name','Subscription:user_id,package_details')->where('id',$user->id)->first() ;
            return prepareResult(true,getLangByLabelGroups('UserValidation','create') ,$userdetail, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
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
            
            $checkId= User::where('id',$user->id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('UserValidation','id_not_found'), [],$this->not_found);
            }
            $userShow = User::where('id',$user->id)->with('Parent:id,name','UserType:id,name','CompanyType:id,created_by,name','Country:id,name','Subscription:user_id,package_details')->first();
            $getAssigneModule = AssigneModule::where('user_id',$user->id)->pluck('module_id')->implode(',');
            $userShow['module_list'] = Module::select('id','name')->whereIn('id',explode(',',$getAssigneModule))->get();

            return prepareResult(true,'User View' ,$userShow, $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    { 
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[
                'user_type_id' => 'required', 
                'role_id' => 'required', 
                'company_type_id' => 'required', 
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email,'.$user->id,
                'contact_number' => 'required', 

            ],
            [
            'user_type_id.required' =>  getLangByLabelGroups('UserValidation','user_type_id'),
            'role_id.required' =>  getLangByLabelGroups('UserValidation','role_id'),
            'company_type_id.required' =>  getLangByLabelGroups('UserValidation','company_type_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','email_invalid'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }

            $checkId = User::where('id',$user->id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('UserValidation','id_not_found'), [],$this->not_found);
            }
            
            $user->company_type_id = $request->company_type_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->license_key = $request->license_key;
            $user->license_end_date = $request->license_end_date;
            $user->joining_date = $request->joining_date;
            $user->establishment_date = $request->establishment_date;
            $user->user_color = $request->user_color;
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->status = ($request->status) ? $request->status: 1 ;
            $user->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->save();
            activity()
           ->causedBy($user)
           ->log($user);
            if($request->package_id) {
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

            if(is_array($request->modules) ){
                $deletOld = AssigneModule::where('user_id',$user->id)->delete();
                for ($i = 0;$i < sizeof($request->modules);$i++) {
                    if (!empty($request->modules[$i])) {
                        $assigneModule = new AssigneModule;
                        $assigneModule->user_id = $user->id;
                        $assigneModule->module_id = $request->modules[$i] ;
                        $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                        $assigneModule->save() ;
          

                    }
                }
            }
           
            $userdetail = User::with('Parent:id,name','UserType:id,name','CompanyType:id,created_by,name','Country:id,name','Subscription:user_id,package_details')->where('id',$user->id)->first() ;
            return prepareResult(true,getLangByLabelGroups('UserValidation','update'),$userdetail, $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
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
            $checkId= User::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('UserValidation','id_not_found'), [],$this->not_found);
            }
            $userDelete = User::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('UserValidation','delete'),[], $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
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
