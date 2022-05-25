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
        $this->middleware('permission:licences-browse',['except' => ['show']]);
        $this->middleware('permission:licences-add', ['only' => ['store']]);
        $this->middleware('permission:licences-edit', ['only' => ['update']]);
        $this->middleware('permission:licences-read', ['only' => ['show']]);
        $this->middleware('permission:licences-delete', ['only' => ['destroy']]);
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

            DB::commit();
            return prepareResult(true,getLangByLabelGroups('LicenceKey','message_create') ,$keyMgmt, config('httpcodes.success'));
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
                return prepareResult(false,getLangByLabelGroups('LicenceKey','message_id_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,'View Licence Key Management' ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request, $id)
    {
        $validation = \Validator::make($request->all(), [
            'user_id'      => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
                $licenceKeyData = LicenceKeyManagement::find($id);

                if($request->package_id)
                {
                    $package = Package::where('id',$request->package_id)->first();
                }
                else
                {
                    $package = json_decode($licenceKeyData->package_details);
                }
                
                $package_expire_at = date('Y-m-d', strtotime($package->validity_in_days.' days'));

                // Create Licence History
                $createLicHistory = new LicenceHistory;
                $createLicHistory->top_most_parent_id = $licenceKeyData->top_most_parent_id;
                $createLicHistory->created_by = auth()->id();
                $createLicHistory->license_key = $licenceKeyData->license_key;
                $createLicHistory->active_from = date('Y-m-d');
                $createLicHistory->expire_at = $package_expire_at;
                $createLicHistory->module_attached = ($request->modules) ? json_encode($request->modules) : $licenceKeyData->module_attached;
                $createLicHistory->package_details = $package;
                $createLicHistory->save();

                // Create Licence Key
                $keyMgmt = LicenceKeyManagement::find($id);
                $keyMgmt->top_most_parent_id = $licenceKeyData->top_most_parent_id;
                $keyMgmt->created_by = auth()->id();
                $keyMgmt->license_key = $licenceKeyData->license_key;
                $keyMgmt->active_from = date('Y-m-d');
                $keyMgmt->expire_at = $package_expire_at;
                $keyMgmt->module_attached = ($request->modules) ? json_encode($request->modules) : $licenceKeyData->module_attached;
                $keyMgmt->package_details = $package;
                $keyMgmt->is_used = false;
                $keyMgmt->save();

            DB::commit();
            return prepareResult(true,getLangByLabelGroups('LicenceKey','message_create') ,$keyMgmt, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function destroy($id)
    {
        //
    }

    public function assignLicenceKey(Request $request,$id)
    {
        DB::beginTransaction();
        try {
                $licenceKeyData = LicenceKeyManagement::find($id);

                if(empty($licenceKeyData))
                {
                    return prepareResult(false,getLangByLabelGroups('LicenceKey','message_invalid_id') ,[], config('httpcodes.success'));
                }

                if($licenceKeyData->is_used == 1)
                {
                    return prepareResult(false,getLangByLabelGroups('LicenceKey','message_already_assigned') ,[], config('httpcodes.success'));
                }

                $package_details =  json_decode($licenceKeyData->package_details);
                $package_expire_at = date('Y-m-d', strtotime($package_details->validity_in_days.' days'));

                $assignLicenceKey = LicenceKeyManagement::where('id',$id)->update(['is_used' => 1]);

                Subscription::where('user_id',$licenceKeyData->top_most_parent_id)->update(['status' => 0]);

                $packageSubscribe = new Subscription;
                $packageSubscribe->user_id = $licenceKeyData->top_most_parent_id;
                $packageSubscribe->package_id = $package_details->id;
                $packageSubscribe->package_details = $package_details;
                $packageSubscribe->license_key = $licenceKeyData->license_key;
                $packageSubscribe->start_date = date('Y-m-d');
                $packageSubscribe->end_date = $package_expire_at;
                $packageSubscribe->status = 1;
                $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $packageSubscribe->save();

                $modules_attached = json_decode($licenceKeyData->module_attached);

                if($modules_attached  && sizeof($modules_attached) >0)
                {
                    AssigneModule::where('user_id',$licenceKeyData->top_most_parent_id)->delete(); 
                    foreach ($modules_attached as $key => $module) 
                    {
                        $assigneModule = new AssigneModule;
                        $assigneModule->user_id = $licenceKeyData->top_most_parent_id;
                        $assigneModule->module_id = $module;
                        $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                        $assigneModule->save();
                    }
                }

                User::where('id',$licenceKeyData->top_most_parent_id)->update(['license_status' => 1]);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('LicenceKey','message_create') ,$licenceKeyData, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
