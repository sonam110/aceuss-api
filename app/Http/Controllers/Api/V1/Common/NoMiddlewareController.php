<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use DB;
use Validator;
use Auth;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\CompanySetting;
use App\Models\UserTypeHasPermission;
use App\Models\ActivityOption;
class NoMiddlewareController extends Controller
{
    
     /*---------------Agency list------------------------------------*/
    public function agencyList(Request $request){
        try {
        $query = Agency::select('id','name')->orderby('id','ASC');
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
                return prepareResult(true,"Agency list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Agency list",$query,$this->success);
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
                
        }
        
    }

     /*---------------Activity options------------------------------------*/
    public function activityOptions(){
        try {
            $query = ActivityOption::get();;
            return prepareResult(true,"Agency list",$query,$this->success);
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
                
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
                return prepareResult(true,"Country list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Country list",$query,$this->success);
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
                
        }
        
    }
    public function companySetting($user_id)
    { 
        try {
            $userInfo = getUser();
            
            $checkSettings = CompanySetting::select(array('company_settings.*', DB::raw("(SELECT organization_number from users WHERE users.id = ".$user_id.") organization_number")))->where('user_id',$user_id)->first();
            if(!is_object($checkSettings)){
                 return prepareResult(false,'User not found',[], $this->unprocessableEntity); 
            }
            return prepareResult(true,'CompanySettings',$checkSettings, $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function passwordChange(Request $request)
    {
        try {
                $validator = Validator::make($request->all(),[   
                    'email'     => 'required|email|exists:users,email',  
                    'date_of_birth' => 'required|date_format:Y-m-d',      
                ]);
                if ($validator->fails()) {
                    return prepareResult(false,$validator->errors()->first(),[], '422'); 
                }
                $checkUser =User::where('email',$request->email)->first();
                $date_of_birth    = date('Y-m-d', strtotime(substr($checkUser->personal_number,0,8)));
                if(strtotime($date_of_birth) == strtotime($request->date_of_birth)){
                    $validator = Validator::make($request->all(),[   
                    'password'     => 'required|min:8|max:30',    
                    ]);
                    if ($validator->fails()) {
                        return prepareResult(false,$validator->errors()->first(),[], '422'); 
                    }
                    $updatePass = User::find($checkUser->id);
                    $updatePass->password = Hash::make($request->password);
                    $updatePass->is_password_change = '0';
                    $updatePass->save();
                    return prepareResult(true,"Password Change Successfully",$updatePass,'200');
                } else {
                    return prepareResult(false,"Email and Date of birth does not match",[],'422');
                }
            
            }
            catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),[], '500');
                
            }
       
    }
     public function userTypePermission(Request $request)
    {
        try {
            $query = UserTypeHasPermission::select('user_type_has_permissions.*')->with('permission');
            
            if(!empty($request->belongs_to))
            {
                $query->join('permissions', function($join) {
                    $join->on('user_type_has_permissions.permission_id', '=', 'permissions.id');
                })
                ->where('permissions.belongs_to',$request->belongs_to);
            }
            if(!empty($request->user_type_id))
            {
                $query->where('user_type_has_permissions.user_type_id',$request->user_type_id);
            }
            if(!empty($request->per_page_record))
            {
                $perPage = $request->per_page_record;
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

            return prepareResult(true,"Permissions",$query,'200');
        } catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }
}
