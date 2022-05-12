<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use DB;
use Str;
use App\Models\User;
use App\Models\UserTypeHasPermission;

class RoleController extends Controller
{
    protected $top_most_parent_id;
    public function __construct()
    {


        $this->middleware('permission:role-browse',['except' => ['show']]);
        $this->middleware('permission:role-add', ['only' => ['store']]);
        $this->middleware('permission:role-edit', ['only' => ['update']]);
        $this->middleware('permission:role-read', ['only' => ['show']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);

        $this->middleware(function ($request, $next) {
            $this->top_most_parent_id = auth()->user()->top_most_parent_id;
            return $next($request);
        });
    }
    
    public function roles(Request $request)
    {
        try {
            $query = Role::select('*')->with('permissions');
            if(auth()->user()->user_type_id!='1')
            {
                $query->where('top_most_parent_id', $this->top_most_parent_id);
            }

            if(!empty($request->top_most_parent_id))
            {
                if($request->top_most_parent_id==1)
                {
                    $query->where(function ($q) {
                        $q->whereNull('top_most_parent_id')
                            ->orWhere('top_most_parent_id', 1);
                    });
                }
                else
                {
                    $query->where('top_most_parent_id', $request->top_most_parent_id);
                }
            }

            if(!empty($request->user_type_id))
            {
                $query->where('user_type_id', $request->user_type_id);
            }

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
            return prepareResult(true,"Roles",$query,'200');
        } catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
        }
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_type_id'   => 'required|exists:user_types,id',  
            'se_name'   => 'required',
            'permissions' => 'required'
        ],
        [
        'se_name.required' => getLangByLabelGroups('role','se_name'),
        'permissions.required' => getLangByLabelGroups('role','permissions'),
        ]);
        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], '422'); 
        }
        
        DB::beginTransaction();
        try {
            $role = new Role;
            if(auth()->user()->user_type_id=='1')
            {
                $role->top_most_parent_id = auth()->id();
            }
            elseif(auth()->user()->user_type_id=='2')
            {
                $role->top_most_parent_id = auth()->id();
            }
            else
            {
                $role->top_most_parent_id = $this->top_most_parent_id;
            }
            $role->name = $this->top_most_parent_id.'-'.Str::slug(substr($request->se_name, 0, 20));
            $role->user_type_id  = $request->user_type_id;
            $role->se_name  = $request->se_name;
            $role->guard_name  = 'api';
            $role->entry_mode  = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $role->save();
            DB::commit();
            if($role) {
                $role->syncPermissions($request->permissions);
            }
            
            return prepareResult(true,getLangByLabelGroups('role','create') ,$role, '200');
        } catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], '500');
        }
    }

    public function show(Role $role)
    {
        try {
            $roleInfo = Role::with('permissions');
            if(auth()->user()->user_type_id!='1')
            {
                $roleInfo = $roleInfo->where('top_most_parent_id', $this->top_most_parent_id);
            }
            $roleInfo = $roleInfo->find($role->id);
            if($roleInfo)
            {
                return prepareResult(true,'View Role',$roleInfo, '200');
            }
            return prepareResult(false, getLangByLabelGroups('role','id_not_found'), [],config('httpcodes.not_found'));
        } catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
        }
    }

    public function update(Request $request, Role $role)
    {
        $validator = \Validator::make($request->all(), [
            'user_type_id'   => 'required|exists:user_types,id', 
            'se_name'   => 'required',
            'permissions' => 'required'
        ],
        [
        'se_name.required' => getLangByLabelGroups('role','se_name'),
        'permissions.required' => getLangByLabelGroups('role','permissions'),
        ]);
        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[],'422'); 
        }

        DB::beginTransaction();
        try {
            $roleInfo = Role::select('*');
            if(auth()->user()->user_type_id!='1')
            {
                $roleInfo = $roleInfo->where('top_most_parent_id', $this->top_most_parent_id);
            }
            $roleInfo = $roleInfo->find($role->id);
            if($roleInfo)
            {
                $roleInfo->user_type_id  = $request->user_type_id;
                $roleInfo->se_name  = $request->se_name;
                $roleInfo->entry_mode  = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $roleInfo->save();
                DB::commit();
                if($roleInfo) {
                    $roleInfo->syncPermissions($request->permissions);
                }
                return prepareResult(true,getLangByLabelGroups('role','update') ,$roleInfo, '200');
            }
            return prepareResult(false, getLangByLabelGroups('role','role_not_found'), [],config('httpcodes.not_found'));
        } catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], '500');
        }
    }

    public function destroy(Role $role)
    {
        try {
            $roleInfo = Role::select('*');
            if(auth()->user()->user_type_id!='1')
            {
                $roleInfo = $roleInfo->where('top_most_parent_id', $this->top_most_parent_id);
            }
            $roleInfo = $roleInfo->find($role->id);
            if($roleInfo)
            {
                $roleInfo->delete();
                 return prepareResult(true,getLangByLabelGroups('role','delete') ,[], '200');
            }
            return prepareResult(false, getLangByLabelGroups('role','role_not_found'), [],config('httpcodes.not_found'));
            
        } catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], '500');
            
        }
    }
}
