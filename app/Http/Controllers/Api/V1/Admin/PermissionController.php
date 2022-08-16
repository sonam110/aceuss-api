<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Models\PermissionExtend;
use DB;

class PermissionController extends Controller
{
    public function permissions(Request $request)
    {
        try {
            $query = Permission::select('*');
            
            if(!empty($request->name))
            {
                $query->where('name', 'LIKE', '%'.$request->name.'%');
            }

            if(!empty($request->se_name))
            {
                $query->where('se_name', 'LIKE', '%'.$request->se_name.'%');
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

            return prepareResult(true,getLangByLabelGroups('BcCommon','bc_message_list'),$query,config('httpcodes.success'));
        } catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name'      => 'required|unique:permissions,name',
            'se_name'   => 'required|unique:permissions,se_name',
            'group_name'=> 'required'
        ],
        [
        'name.required' => getLangByLabelGroups('permission','message_name'),
        'se_name.required' => getLangByLabelGroups('permission','message_se_name'),
        'group_name.required' => getLangByLabelGroups('permission','message_group_name'),
        ]);
        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
            $permission = new Permission;
            $permission->group_name  = $request->group_name;
            $permission->guard_name    = 'api';
            $permission->name = $request->name;
            $permission->se_name  = $request->se_name;
            $permission->belongs_to  = empty($request->belongs_to) ? 1 : $request->belongs_to;
            $permission->entry_mode  = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $permission->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('permission','message_create') ,$permission, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function show(Permission $permission)
    {
        try {
            if($permission)
            {
                 return prepareResult(true,getLangByLabelGroups('BcCommon','bc_message_show') ,$permission, config('httpcodes.success'));
            }
            return prepareResult(false, getLangByLabelGroups('permission','message_per_not_found'), [],config('httpcodes.not_found'));
        } catch (\Throwable $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request, Permission $permission)
    {
        $validator = \Validator::make($request->all(), [
            'name'      => 'required|unique:permissions,name,'.$permission->id,
            'se_name'   => 'required|unique:permissions,se_name,'.$permission->id,
            'group_name'=> 'required'
        ],
        [
        'name.required' => getLangByLabelGroups('permission','message_name'),
        'se_name.required' => getLangByLabelGroups('permission','message_se_name'),
        'group_name.required' => getLangByLabelGroups('permission','message_group_name'),
        ]);

        DB::beginTransaction();
        try {
            $permission->group_name  = $request->group_name;
            $permission->name = $request->name;
            $permission->se_name  = $request->se_name;
            $permission->belongs_to  = empty($request->belongs_to) ? 1 : $request->belongs_to;
            $permission->entry_mode  = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $permission->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('permission','message_update') ,$permission, config('httpcodes.success'));
        } catch (\Throwable $e) {
            \Log::error($e);
            DB::rollback();
           return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function destroy(Permission $permission)
    {
        //Temporary enabled, after deployment removed this function
        try {
            $permission->delete();
           return prepareResult(true,getLangByLabelGroups('permission','message_delete') ,[], config('httpcodes.success'));
        } catch (\Throwable $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function permissionsGroupWise()
    {
        try {
            $permissions = PermissionExtend::select('group_name')->with('groupWisePermissions')->groupBy('group_name')->get();
            
             return prepareResult(true,'Groupwsir Permission' ,$permission, config('httpcodes.success'));
        } catch (\Throwable $exception) {
           return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
