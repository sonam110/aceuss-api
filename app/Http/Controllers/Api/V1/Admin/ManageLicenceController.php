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

    public function index(Request $request)
    {
        try {

            $query = LicenceKeyManagement::where('is_expired', 0)
                ->orderBy('id', 'DESC');

            if(!empty($request->user_id))
            {
                $query->where('top_most_parent_id', $request->user_id);
            }

            if(!empty($request->licence_key))
            {
                $query->where('licence_key', $request->licence_key);
            }

            if($request->is_used == "1")
            {
                $query = $query->where('is_used', 1);
            }

            if($request->is_used == "no" || $request->is_used == "No")
            {
                $query = $query->where('is_used', 0);
            }

            if(!empty($request->active_from))
            {
                $query->where('active_from','>=', $request->active_from);
            }

            if(!empty($request->expire_at))
            {
                $query->where('expire_at','<=', $request->expire_at);
            }
            
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $data = [];
                foreach ($result as $key => $value) {
                    $modules = json_decode($value->module_attached);
                    $mod = [];
                    foreach ($modules as $key => $module) {
                        $mod[] = Module::find($module);
                    }
                    $value['company'] = User::with('companySetting:id,user_id,company_name,company_logo,company_email')->withTrashed()->find($value->top_most_parent_id);
                    $value['package'] = json_decode($value->package_details);
                    $value['module'] = $mod;
                    $data[] = $value;
                }

                $pagination =  [
                    'data' => $data,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->toSql();
            }
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            logException($exception);
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
                $createLicHistory->licence_key = $request->licence_key;
                $createLicHistory->active_from = date('Y-m-d');
                $createLicHistory->expire_at = ($request->expire_at) ? $request->expire_at : $package_expire_at;
                $createLicHistory->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $createLicHistory->package_details = $package;
                $createLicHistory->save();

                // Create Licence Key
                $keyMgmt = new LicenceKeyManagement;
                $keyMgmt->top_most_parent_id = $request->user_id;
                $keyMgmt->created_by = auth()->id();
                $keyMgmt->licence_key = $request->licence_key;
                $keyMgmt->active_from = date('Y-m-d');
                $keyMgmt->expire_at = ($request->expire_at) ? $request->expire_at : $package_expire_at;
                $keyMgmt->module_attached = ($request->modules) ? json_encode($request->modules) : null;
                $keyMgmt->package_details = $package;
                $keyMgmt->is_used = false;
                $keyMgmt->is_expired = false;
                $keyMgmt->save();

            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$keyMgmt, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function show($id)
    {
        try {
            $checkId= LicenceKeyManagement::where('id',$id)->first();
            $modules = json_decode($checkId->module_attached);
            foreach ($modules as $key => $module) {
                $mod[] = Module::find($module);
            }
            $checkId['company']=User::find($checkId->top_most_parent_id);
            $checkId['package']=json_decode($checkId->package_details);
            $checkId['module'] = $mod;
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,getLangByLabelGroups('BcCommon','message_show') ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
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
                $createLicHistory->licence_key = $licenceKeyData->licence_key;
                $createLicHistory->active_from = date('Y-m-d');
                $createLicHistory->expire_at = ($request->expire_at) ? $request->expire_at : $package_expire_at;
                $createLicHistory->module_attached = ($request->modules) ? json_encode($request->modules) : $licenceKeyData->module_attached;
                $createLicHistory->package_details = $package;
                $createLicHistory->save();

                // Create Licence Key
                $keyMgmt = LicenceKeyManagement::find($id);
                $keyMgmt->top_most_parent_id = $licenceKeyData->top_most_parent_id;
                $keyMgmt->created_by = auth()->id();
                $keyMgmt->licence_key = $licenceKeyData->licence_key;
                $keyMgmt->active_from = date('Y-m-d');
                $keyMgmt->expire_at = ($request->expire_at) ? $request->expire_at : $package_expire_at;
                $keyMgmt->module_attached = ($request->modules) ? json_encode($request->modules) : $licenceKeyData->module_attached;
                $keyMgmt->package_details = $package;
                $keyMgmt->save();

            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_update') ,$keyMgmt, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
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
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found') ,[], config('httpcodes.not_found'));
            }

            if($licenceKeyData->is_used == 1)
            {
                return prepareResult(false,getLangByLabelGroups('LicenceKey','message_already_assigned') ,[], config('httpcodes.bad_request'));
            }

            $package_details =  json_decode($licenceKeyData->package_details);
            $package_expire_at = date('Y-m-d', strtotime($package_details->validity_in_days.' days'));

            $assignLicenceKey = LicenceKeyManagement::where('id',$id)->update(['is_used' => 1]);

            $otherLicenceKey = LicenceKeyManagement::where('licence_key', '!=', $licenceKeyData->licence_key)
                ->where('top_most_parent_id', $licenceKeyData->top_most_parent_id)
                ->update(['is_expired' => 1, 'cancelled_by' => auth()->id()]);

            Subscription::where('user_id',$licenceKeyData->top_most_parent_id)->update(['status' => 0]);

            $packageSubscribe = new Subscription;
            $packageSubscribe->user_id = $licenceKeyData->top_most_parent_id;
            $packageSubscribe->package_id = $package_details->id;
            $packageSubscribe->package_details = $package_details;
            $packageSubscribe->licence_key = $licenceKeyData->licence_key;
            $packageSubscribe->start_date = date('Y-m-d');
            $packageSubscribe->end_date = $package_expire_at;
            $packageSubscribe->status = 1;
            $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $packageSubscribe->save();

            // if($request->modules)
            // {
            //     $modules_attached = $request->modules;
            // }
            // else
            // {
            //     $modules_attached = json_decode($licenceKeyData->module_attached);
            // }

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

            User::where('id',$licenceKeyData->top_most_parent_id)->update(['licence_status' => 1, 
                'licence_key' => $licenceKeyData->licence_key,
                'licence_end_date'=> $package_expire_at
            ]);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$licenceKeyData, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }


    public function cancelLicenceKey(Request $request,$id)
    {
        DB::beginTransaction();
        try {
                $user = User::find($id);
                if($user->licence_status == 0)
                {
                    return prepareResult(false,getLangByLabelGroups('BcCommon','message_subscription_already_expired') ,['already expired'], config('httpcodes.success'));
                }

                $licenceKeyData = LicenceKeyManagement::where('licence_key',$user->licence_key)->first();

                if(empty($licenceKeyData))
                {
                    return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found') ,[], config('httpcodes.not_found'));
                }
                $package_expire_at = date('Y-m-d', strtotime('-1 days'));

                $expireLicenceKey = LicenceKeyManagement::where('id',$licenceKeyData->id)
                    ->update([
                        'expire_at' => $package_expire_at,
                        'cancelled_by' => Auth::id(), 
                        'is_expired' => 1, 
                        'reason_for_cancellation' => $request->reason_for_cancellation
                    ]);

                Subscription::where('user_id',$id)
                ->where('status',1)
                ->update([
                    'status' => 0,
                    'end_date' => $package_expire_at
                ]);

                $user->update([
                    'licence_status' => 0,
                    'licence_end_date'=> $package_expire_at
                ]);

                $user = User::find($id);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_cancel') ,$user, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
