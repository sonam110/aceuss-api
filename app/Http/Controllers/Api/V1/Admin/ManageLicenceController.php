<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\AssigneModule;
use App\Models\Module;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Str;
use Exception;
use App\Models\LicenceHistory;
use App\Models\LicenceKeyManagement;


class ManageLicenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:licences',['except' => ['show']]);
        $this->middleware('permission:licence-add', ['only' => ['store']]);
        $this->middleware('permission:licence-edit', ['only' => ['update']]);
        $this->middleware('permission:licence-read', ['only' => ['show']]);
        $this->middleware('permission:licence-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        try {

            $query = LicenceKeyManagement::orderBy('id', 'DESC');
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
                return prepareResult(true,"Licence Key list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Licence Key list",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function store(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'user_id'      => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
                $package = Package::where('id',$request->package_id)->first();
                $package_expire_at = date('Y-m-d', strtotime($package->validity_in_days.' days'));

                // Create Licence History
                $createLicHistory = new LicenceHistory;
                $createLicHistory->top_most_parent_id = $request->user_id;
                $createLicHistory->created_by = auth()->id();
                $createLicHistory->license_key = $request->license_key;
                $createLicHistory->active_from = date('Y-m-d');
                $createLicHistory->expire_at = $package_expire_at;
                $createLicHistory->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $createLicHistory->package_details = $package;
                $createLicHistory->save();

                // Create Licence Key
                $keyMgmt = new LicenceKeyManagement;
                $keyMgmt->top_most_parent_id = $request->user_id;
                $keyMgmt->created_by = auth()->id();
                $keyMgmt->license_key = $request->license_key;
                $keyMgmt->active_from = date('Y-m-d');
                $keyMgmt->expire_at = $package_expire_at;
                $keyMgmt->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $keyMgmt->package_details = $package;
                $keyMgmt->is_used = false;
                $keyMgmt->save();

                $packageSubscribe = new Subscription;
                $packageSubscribe->user_id = $request->user_id;
                $packageSubscribe->package_id = $request->package_id;
                $packageSubscribe->package_details = $package;
                $packageSubscribe->license_key = $request->license_key;
                $packageSubscribe->start_date = date('Y-m-d');
                $packageSubscribe->end_date = $package_expire_at;
                $packageSubscribe->status = 1;
                $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $packageSubscribe->save();

            if(is_array($request->modules)  && sizeof($request->modules) >0)
            { 
                foreach ($request->modules as $key => $module) 
                {
                    $count = AssigneModule::where('user_id',$request->user_id)->where('module_id',$module)->count();
                    if($count<1)
                    { 
                        $assigneModule = new AssigneModule;
                        $assigneModule->user_id = $request->user_id;
                        $assigneModule->module_id = $module;
                        $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                        $assigneModule->save();
                    }
                }
            }
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('message_LicenceKey','create') ,$keyMgmt, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function show($id)
    {
        try {
            $checkId= LicenceKeyManagement::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('message_Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,'View Licence Key Management' ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
