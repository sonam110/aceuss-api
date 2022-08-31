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
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NoMiddlewareController extends Controller
{
    /*---------------Agency list------------------------------------*/
    public function agencyList(Request $request)
    {
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
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));     
        } 
    }

     /*---------------Activity options------------------------------------*/
    public function activityOptions()
    {
        try {
            $query = ActivityOption::get();;
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        } 
    }

    /*---------------conntry list------------------------------------*/
    public function countryList(Request $request)
    {
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
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));       
        }
    }

    public function companySettingDetail($user_id)
    { 
        try {
            $userInfo = getUser();
            
            $checkSettings = CompanySetting::select(array('company_settings.*', DB::raw("(SELECT organization_number from users WHERE users.id = ".$user_id.") organization_number")))->where('user_id',$user_id)->first();
            if(!is_object($checkSettings)){
                 return prepareResult(false,'User not found',[], config('httpcodes.bad_request')); 
            }
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_show'),$checkSettings, config('httpcodes.success'));     
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error')); 
        }
    }

    public function passwordChange(Request $request)
    {
        DB::beginTransaction();
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
                DB::commit();
                return prepareResult(true,getLangByLabelGroups('User','message_password_change'),$updatePass,'200');
            } else {
                return prepareResult(false,getLangByLabelGroups('User','message_email_dob_error'),[],'422');
            }
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], '500');
        }
    }

    public function userTypePermission(Request $request)
    {
        try {
            $query = UserTypeHasPermission::select('user_type_has_permissions.*')->with('permission');
            
            /*if(!empty($request->belongs_to))
            {
                $query->join('permissions', function($join) {
                    $join->on('user_type_has_permissions.permission_id', '=', 'permissions.id');
                })
                ->where(function ($q) use ($request) {
                    $q->where('permissions.belongs_to', $request->belongs_to)
                        ->orWhere('permissions.name', 'dashboard-browse');
                })
                ->groupBy('permissions.name');
            }*/
            if(!empty($request->user_type_id))
            {
                $query->where('user_type_has_permissions.user_type_id',$request->user_type_id);
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
            return prepareResult(true,getLangByLabelGroups('permission','message_list'),$query,'200');
        } catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
        }
    }

    public function allPermissions(Request $request)
    {
        $permissions = Permission::withoutGlobalScope('top_most_parent_id');
        if(!empty($request->belongs_to))
        {
            $permissions->where(function ($q) use ($request) 
            {
                $q->where('permissions.belongs_to', $request->belongs_to)
                    ->orWhere('permissions.name', 'dashboard-browse');
            });
        }
        $permissions = $permissions->get();

        $user_type_has_permissions = UserTypeHasPermission::where('user_type_id', $request->user_type_id)
            ->withoutGlobalScope('top_most_parent_id')
            ->get();

        $returnData = [
            'permissions' => $permissions,
            'user_type_has_permissions' => $user_type_has_permissions

        ];  
        return prepareResult(true,getLangByLabelGroups('permission','message_list'), $returnData,'200');
    }

    public function addUserTypeHasPermissions(Request $request)
    {
        $deleteAll = UserTypeHasPermission::where('user_type_id', $request->user_type_id)
            ->withoutGlobalScope('top_most_parent_id')
            ->delete();
        foreach ($request->permissions as $key => $permission) {
            $add =  new UserTypeHasPermission;
            $add->user_type_id = $request->user_type_id;
            $add->permission_id = $permission;
            $add->save();
        }
        return prepareResult(true,getLangByLabelGroups('permission','message_assigne'),[],'200');
    }
}
