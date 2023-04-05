<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\CompanySetting;
use App\Models\Subscription;
use App\Models\AssigneModule;
use App\Models\Package;
use App\Models\EmailTemplate;
use App\Models\DeviceLoginHistory;
use App\Models\AdminFile;
use App\Models\FileAccessLog;
use Validator;
use Auth;
use DB;
use Exception;
use Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\SendResetPassworkLink;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserLoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            $this->username() => 'required|email',
            'password' => 'required|string',
            ],
            [
            'email.required' => getLangByLabelGroups('LoginValidation','message_email'),
            'email.email' => getLangByLabelGroups('LoginValidation','message_email_invalid'),
            'password.required' =>  getLangByLabelGroups('LoginValidation','message_password'),
            ]);

        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        }
        try {
            $user = User::where('email',$request->email)->with('TopMostParent:id,user_type_id,name,email')
            ->withoutGlobalScope('top_most_parent_id')
            ->first();
            if (!empty($user)) {
                
                if (Hash::check($request->password, $user->password)) {
                    if($user->status == '0' ) { 
                        return prepareResult(false,getLangByLabelGroups('LoginValidation','message_account_inactive'),[],config('httpcodes.unauthorized'));
                    }
                    if($user->status == '2') { 
                        return prepareResult(false,getLangByLabelGroups('LoginValidation','message_account_deactive'),[],config('httpcodes.unauthorized'));
                    }
                    
                    if ($this->attemptLogin($request)) {
                            $token = auth()->user()->createToken('authToken')->accessToken;
                            if (empty($token)) {
                                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_unable_generate_token'),[], config('httpcodes.bad_request'));
                            }else{
                                 //======= login history==================//
                                $history =  DeviceLoginHistory::where('user_id',$user->id)
                                ->where('login_via', $request->login_via)
                                ->where('device_token', $request->device_token)
                                ->where('device_id', $request->device_id)
                                ->first();
                                if ($history) {
                                    $history->user_token = $token;
                                    $history->ip_address = request()->ip();
                                    $history->save();
                                }
                                else
                                {
                                    $createHistory = DeviceLoginHistory::create([
                                        'user_id'=> Auth::id(),
                                        'login_via'=> ($request->login_via) ? $request->login_via:'0',
                                        'device_token'=> $request->device_token,
                                        'device_id'=> $request->device_id,
                                        'user_token'=> $token,
                                        'ip_address'=> request()->ip(),
                                    ]);
                                }

                                if(auth()->user()->top_most_parent_id!=1)
                                {
                                    $checkLicence = User::find(auth()->user()->top_most_parent_id);
                                    if($checkLicence->licence_end_date<date('Y-m-d'))
                                    {
                                        $checkLicence->licence_status = 0;
                                        $checkLicence->save();
                                    }
                                }
                        
                                $user = User::where('id',$user->id)->with('TopMostParent:id,user_type_id,name,email','language:id,title,value', 'branch:id,branch_id,branch_name','employeeBranches.branch:id,name,branch_name')->first();    
                                $user['access_token'] = $token;
                                $user['user_type']    = @Auth::user()->UserType->name;
                                $user['roles']    = @Auth::user()->roles[0]->name;
                                $user['role_name']    = @Auth::user()->roles[0]->se_name;
                                $role   = Role::where('name', $user['roles'])->first();
                                $user['permissions']  = $role->permissions()->select('id','name as action','group_name as subject','se_name')->get();
                                $user['licence_status'] = 1;

                                $companySetting = CompanySetting::where('user_id',Auth::user()->top_most_parent_id)->first();
                                if(!empty($companySetting))
                                { 
                                    $user['relaxation_time'] = $companySetting->relaxation_time;
                                }
                                else
                                {
                                    $user['relaxation_time'] = 0;
                                }

                                if(auth()->user()->top_most_parent_id!=1)
                                {
                                    $user['licence_status'] = User::find(auth()->user()->top_most_parent_id)->licence_status;
                                    $assigned_module = User::find(auth()->user()->top_most_parent_id);
                                    $user['assigned_module'] = $assigned_module->assignedModule()->select('id','user_id','module_id')->with('Module:id,name')->get();

                                    $checkFileAccess = AdminFile::where('user_type_id', auth()->user()->user_type_id)
                                        ->first();
                                    if($checkFileAccess)
                                    {
                                        $checkLog = FileAccessLog::where('admin_file_id', $checkFileAccess->id)
                                        ->where('user_id', auth()->id())
                                        ->first();
                                        if($checkLog)
                                        {
                                            $user['file_access'] = null;
                                        }
                                        else
                                        {
                                            $user['file_access'] = $checkFileAccess;
                                        }
                                    }
                                    else
                                    {
                                        $user['file_access'] = null;
                                    }
                                }
                                else
                                {
                                    $user['file_access'] = null;
                                }
                                
                             return prepareResult(true,getLangByLabelGroups('LoginValidation','message_login'),$user,config('httpcodes.success'));
                            }
                        
                    }
                   
                } else {
                    return prepareResult(false,getLangByLabelGroups('LoginValidation','message_wrong_password'),[],config('httpcodes.bad_request'));
                }   
            } else {
                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_user_not_found'),[],config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
       }
   }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function username()
    {
        return 'email';
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
        ? new Response('', 204)
        : redirect()->intended($this->redirectPath());
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    protected function guard()
    {
        return Auth::guard();
    }   

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }



    /* ------------------------------------
        @Url:  /forgot-password
        @Description:  Forget Password Api
        @method : Post
        @Parameters : email
        @Output: returns Success Or Fail.
    --------------------------------------- */

    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                "email" => 'required|email',
                "device" => 'required|in:web,mobile'
            ],
            [
            'email.required' => getLangByLabelGroups('LoginValidation','message_email'),
            'email.email' => getLangByLabelGroups('LoginValidation','message_email_invalid'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $user = User::where('email',$request->email)->first();
            if (!empty($user)) {
                    if ($user->status == '0') { 
                        return prepareResult(false,getLangByLabelGroups('LoginValidation','message_account_inactive'),[],config('httpcodes.bad_request'));
                    }
                    if ($user->status == '2') { 
                        return prepareResult(false,getLangByLabelGroups('LoginValidation','message_account_deactive'),[],config('httpcodes.bad_request'));
                    }
                   
                    if($request->device == "mobile") {
                        $token = (env('APP_ENV','local') == 'local') ?'123456' : rand(0,999999);
                        $passowrd_link = 'Your Reset Password Otp is'.'  '.$token.'';
                        $passMessage = 'This email is to confirm a recent password reset request for your account. To confirm this request and reset your password Your forgot password token given in below .';
                    } else {
                        $token = (env('APP_ENV','local') == 'local') ?'123456' : \Str::random(60);
                        $passowrd_link = '<a href="'.route('password.reset',$token).'" style="color: #000;font-size: 18px;text-decoration: underline;font-family: "Roboto Condensed", sans-serif;" target="_blank">Reset your password </a>';
                        $passMessage = 'This email is to confirm a recent password reset request for your account. To confirm this request and reset your password Please click below link ';
                    }
                    
                    User::updateOrCreate(['email'=>$user->email],['password_token'=>$token]);   
                    
                    $content = ([
                    'company' => companySetting($user->top_most_parent_id),
                    'name' => aceussDecrypt($user->name),
                    'email' => aceussDecrypt($user->email),
                    'token' => $token,
                    'passowrd_link' => $passowrd_link,
                    'passMessage' => $passMessage,
                    ]);         
                    if(env('IS_MAIL_ENABLE',false) == true){   
                        Mail::to(aceussDecrypt($user->email))->send(new SendResetPassworkLink($content));
                    }
                return prepareResult(true,getLangByLabelGroups('LoginValidation','message_password_reset_link'),$content,config('httpcodes.success'));

            }else{
                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_user_not_found'),[],config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function resetPassword($token)
    {
        if($token){
            $usertoken =[
                'token'=> $token,
            ];
            return prepareResult(true,getLangByLabelGroups('LoginValidation','message_token'),$usertoken,config('httpcodes.success'));
        } else {
            return prepareResult(false,getLangByLabelGroups('LoginValidation','message_no_token'),[],config('httpcodes.bad_request'));
        }

    }

    public function verifyOtp(Request $request)
    {
        try {
            $input = $request->only('email','token');
            $validator = Validator::make($input, [
                'token' => 'required',
                "email" => 'required|email',
            ],
            [
            'token.required' => getLangByLabelGroups('PasswordReset','token'),
            'email.required' => getLangByLabelGroups('LoginValidation','message_email'),
            'email.email' => getLangByLabelGroups('LoginValidation','message_email_invalid'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $user = User::select('id','email','password_token')->where('email',$request->email)->where('password_token',$request->token)->first();
            if (!empty($user)) {

                return prepareResult(true,getLangByLabelGroups('User','message_show'),$user,config('httpcodes.success'));
            }else {
                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_otp_invalid'),[],config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
       
    }

    public function passwordReset(Request $request)
    {
        try {
            $input = $request->only('email','token', 'password', 'password_confirmation');
            $validator = Validator::make($input, [
                'token' => 'required',
                "email" => 'required|email',
                'password' => 'required|confirmed|min:8',

            ],
            [
            'token.required' => getLangByLabelGroups('PasswordReset','token'),
            'email.required' => getLangByLabelGroups('LoginValidation','message_email'),
            'email.email' => getLangByLabelGroups('LoginValidation','message_email_invalid'),
            'password' =>  getLangByLabelGroups('PasswordReset','password'),
            'password.confirmed' =>  getLangByLabelGroups('PasswordReset','confirm_password'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $user = User::where('email',$request->email)->where('password_token',$request->token)->first();
            $password = $request->password;
            if (!empty($user)) {
                $response = Password::reset($input, function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->password_token = '';
                    $user->save();
                });

                return prepareResult(true,getLangByLabelGroups('PasswordReset','success'),[],config('httpcodes.success'));
            }else {
                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_user_not_found'),[],config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
       
    }

    public function passwordResetInMobile(Request $request)
    {
        try {
            $input = $request->only('email', 'password', 'password_confirmation');
            $validator = Validator::make($input, [
                "email" => 'required|email',
                'password' => 'required|confirmed|min:8',

            ],
            [
            'email.required' => getLangByLabelGroups('LoginValidation','message_email'),
            'email.email' => getLangByLabelGroups('LoginValidation','message_email_invalid'),
            'password' =>  getLangByLabelGroups('PasswordReset','password'),
            'password.confirmed' =>  getLangByLabelGroups('PasswordReset','confirm_password'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $user = User::where('email',$request->email)->first();

            $password = $request->password;
            if (!empty($user)) {
                    $user = User::find($user->id);
                    $user->password = Hash::make($password);
                    $user->password_token = '';
                    $user->save();
                
                return prepareResult(true,getLangByLabelGroups('PasswordReset','success'),[],config('httpcodes.success'));
            }else {
                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_user_not_found'),[],config('httpcodes.bad_request'));
            }
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
       
    }

    public function logout(Request $request)
    {
        $user = getUser();
        if (!is_object($user)) {
            return prepareResult(false,getLangByLabelGroups('User','message_record_not_found'), [],config('httpcodes.not_found'));
        }
        if(Auth::check()) {
            $token = $request->bearerToken();
            Auth::user()->token()->revoke();
            return prepareResult(true,getLangByLabelGroups('LoginValidation','message_logout'),[],config('httpcodes.success'));
        }else{
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }

    }

    public function bearerToken()
    {
       $header = $this->header('Authorization', '');
       if (Str::startsWith($header, 'Bearer ')) {
           return Str::substr($header, 7);
       }
    }

    /* ------------------------------------
        @Url:  /change-password
        @Description:  change Password Api
        @method : Post
        @Parameters : old_password,new_password,new_password_confirmation
        @Output: returns Success Or Fail.
    --------------------------------------- */
    public function changePassword(Request $request)
    {
        try {

            $user = auth()->user();
            $validator = Validator::make($request->all(),[
                'old_password'              => ['required'],
                'new_password'              => ['required', 'confirmed', 'min:6', 'max:25'],
                'new_password_confirmation' => ['required']
            ],
            [
            'old_password.required' => getLangByLabelGroups('ChangePassword','old_password'),
            'new_password.required' => getLangByLabelGroups('ChangePassword','new_password'),
            'new_password.confirmed' =>  getLangByLabelGroups('ChangePassword','new_password_confirm'),
            'new_password_confirmation' =>  getLangByLabelGroups('ChangePassword','new_password_confirmation'),
            ]);

            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
           

            if(Hash::check($request->old_password, $user->password)) {
                $data['password'] =  \Hash::make($request->new_password);
                $updatePass = User::updateOrCreate(['id' => $user->id],$data);

                return prepareResult(true,getLangByLabelGroups('User','message_password_change') ,[], config('httpcodes.success'));
                
               
            }else{

                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_incorrect_old_password'),[],config('httpcodes.bad_request'));
               

            }
        }
        catch(Exception $exception) {
	       logException($exception);
           return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
       }
    }

    /*---------------User------------------------------------*/

    public function userTypeList()
    {
        $userTypeList = UserType::whereNotIn('id',['1','2'])->select('id','name')->get();
        if($userTypeList) {
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list') ,$userTypeList, config('httpcodes.success'));
        } else{
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_record_not_found') ,[], config('httpcodes.not_found'));
        }
    }

    /* Verify User Email*/
    public function verifyUserEmail(Request $request)
    {
        if($request->email){
            $user = User::where('email',$request->email)->where('status','1')->first();
            if ($user) {
                return prepareResult(true,getLangByLabelGroups('User','message_record_already_exists') ,[], config('httpcodes.success'));
            }
            else {
                return prepareResult(false,getLangByLabelGroups('LoginValidation','message_email_not_exists'),[], config('httpcodes.not_found'));
            }
        }
    }

    public function userDetail(Request $request)
    {
        try {
            
            $user = getUser();
            $userDetail = User::where('id',$user->id)->where('top_most_parent_id',$user->top_most_parent_id)->with('UserType:id,name','TopMostParent:id,user_type_id,name,email','Parent:id,name','CategoryMaster:id,created_by,name','Department:id,name','agencyHours')->first();
            return prepareResult(true,getLangByLabelGroups('User','message_show') ,$userDetail, config('httpcodes.success'));
                
        } catch(Exception $exception) {
	            logException($exception);
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
                
        }
            
    }
     
    /*---------------conntry list------------------------------------*/
    public function countryList(Request $request){
        try {
        $query = DB::table('countries')->select('id','name')->orderby('id','ASC');
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
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
        } catch(Exception $exception) {
	            logException($exception);
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
                
        }
        
    }

    public function changeLanguage(Request $request)
    {
        $user = Auth::user();
        if($user)
        {
            $user->language_id   = $request->language_id;
            $user->save();
        }
        return prepareResult(true, getLangByLabelGroups('User','message_language_change'),$user,config('httpcodes.success'));
    }
    
}
