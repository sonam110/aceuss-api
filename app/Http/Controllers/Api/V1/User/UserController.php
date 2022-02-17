<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\package;
use App\Models\Subscription;
use App\Models\EmailTemplate;
use Validator;
use Auth;
use DB;
use Exception;
use Mail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class UserController extends Controller
{

    protected $top_most_parent_id;
    public function __construct()
    {
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
            $query = User::select('id','user_type_id', 'company_type_id', 'category_id', 'top_most_parent_id', 'parent_id','branch_id','country_id','city', 'dept_id', 'govt_id','name', 'email', 'email_verified_at','contact_number', 'gender', 'personal_number','joining_date','status')->where('top_most_parent_id',$this->top_most_parent_id)->with('TopMostParent:id,user_type_id,name,email','Parent:id,name','UserType:id,name','CompanyType:id,created_by,name','Country') ;
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
                'user_type_id' => 'required', 
                'role_id' => 'required',
                'category_id' => 'required', 
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|same:confirm-password|min:8|max:30',
                'contact_number' => 'required', 

            ],
            [
            'user_type_id.required' =>  getLangByLabelGroups('UserValidation','user_type_id'),
            'role_id.required' =>  getLangByLabelGroups('UserValidation','role_id'),
            'category_id.required' =>  getLangByLabelGroups('UserValidation','category_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','email_invalid'),
            'password.required' =>  getLangByLabelGroups('UserValidation','password'),
            'password.min' =>  getLangByLabelGroups('UserValidation','password_min'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
           
            $user = new User;
            $user->user_type_id = $request->user_type_id;
            $user->role_id = $request->role_id;
            $user->company_type_id = $request->company_type_id;
            $user->category_id = $request->category_id;
            $user->top_most_parent_id = $this->top_most_parent_id;
            $user->parent_id = $userInfo->id;
            $user->branch_id = $request->branch_id;
            $user->govt_id = $request->govt_id;
            $user->dept_id = $request->dept_id;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->weekly_hours_alloted_by_govt = $request->weekly_hours_alloted_by_govt;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->license_key = $request->license_key;
            $user->license_end_date = $request->license_end_date;
            $user->joining_date = $request->joining_date;
            $user->establishment_date = $request->establishment_date;
            $user->user_color = $request->user_color;
            $user->disease_description = $request->disease_description;
            $user->created_by = $userInfo->id;
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->save();
            activity()
           ->causedBy($user)
           ->log($user);
            if(!empty($request->input('role_id')))
            {
                $role = Role::where('id',$request->role_id)->first();
                $user->assignRole($role->name);
            }
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
                return prepareResult(false,getLangByLabelGroups('UserValidation','id_not_found'), [],'404');
            }
            $userShow = User::where('id',$user->id)->with('TopMostParent:id,user_type_id,name,email','UserType:id,name','CompanyType:id,created_by,name','CategoryMaster:id,created_by,name','Department:id,name','Country:id,name')->first();
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
                'user_type_id' => 'required', 
                'role_id' => 'required',
                'category_id' => 'required', 
                'name' => 'required', 
                'email'     => 'required|email|unique:users,email,'.$user->id,
                'contact_number' => 'required', 

            ],
            [
            'user_type_id.required' =>  getLangByLabelGroups('UserValidation','user_type_id'),
            'role_id.required' =>  getLangByLabelGroups('UserValidation','role_id'),
            'category_id.required' =>  getLangByLabelGroups('UserValidation','category_id'),
            'name.required' =>  getLangByLabelGroups('UserValidation','name'),
            'email.required' =>  getLangByLabelGroups('UserValidation','email'),
            'email.email' =>  getLangByLabelGroups('UserValidation','email_invalid'),
            'contact_number' =>  getLangByLabelGroups('UserValidation','contact_number'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], '422'); 
            }
            $checkId = User::where('id',$user->id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('UserValidation','id_not_found'), [],'404');
            }
            
            $user->user_type_id = $request->user_type_id;
            $user->role_id = $request->role_id;
            $user->company_type_id = $request->company_type_id;
            $user->category_id = $request->category_id;
            $user->branch_id = $request->branch_id;
            $user->govt_id = $request->govt_id;
            $user->dept_id = $request->dept_id;
            $user->country_id = $request->country_id;
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->weekly_hours_alloted_by_govt = $request->weekly_hours_alloted_by_govt;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->license_key = $request->license_key;
            $user->license_end_date = $request->license_end_date;
            $user->joining_date = $request->joining_date;
            $user->establishment_date = $request->establishment_date;
            $user->user_color = $request->user_color;
            $user->disease_description = $request->disease_description;
            $user->is_substitute = ($request->is_substitute) ? 1:0 ;
            $user->is_regular = ($request->is_regular) ? 1:0 ;
            $user->is_seasonal = ($request->is_seasonal) ? 1:0 ;
            $user->is_file_required = ($request->is_file_required) ? 1:0 ;
            $user->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $user->save();
            \DB::table('model_has_roles')->where('model_id',$user->id)->delete();
            if(!empty($request->input('role_id')))
            {
                $role = Role::where('id',$request->role_id)->first();
                $user->assignRole($role->name);
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
        return($w);

    }
}
