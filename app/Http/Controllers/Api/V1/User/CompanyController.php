<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyWorkShift;
use App\Models\ShiftAssigne;
use App\Models\User;
use Validator;
use Auth;
use DB;
use Exception;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\AssigneModule;
use App\Models\LicenceHistory;
use App\Models\LicenceKeyManagement;
class CompanyController extends Controller
{
	public function workshifts(Request $request)
    {
        DB::beginTransaction();
        try {
	        $user = getUser();
            $whereRaw = $this->getWhereRawFromRequest($request);
	        if($whereRaw != '') { 
                $query =  CompanyWorkShift::whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = CompanyWorkShift::orderBy('id', 'DESC');
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
                return prepareResult(true,"WorkShift list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"WorkShift list",$query,config('httpcodes.success'));
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    	
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'shift_name' => 'required',   
        		'shift_start_time' => 'required|date_format:H:i',   
                'shift_end_time' => 'required|date_format:H:i',   
	        ],
		    [
            'shift_name.required' =>  getLangByLabelGroups('Company','message_shift_name'),
            'shift_start_time.required' =>  getLangByLabelGroups('Company','message_shift_start_time'),
            'shift_end_time.required' =>  getLangByLabelGroups('Company','message_shift_end_time'),
            'shift_end_time.after' =>  getLangByLabelGroups('Company','message_shift_end_time_after'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkAlready = CompanyWorkShift::where('shift_name',$request->shift_name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,getLangByLabelGroups('Company','message_name_already_exists'),[], config('httpcodes.bad_request')); 
        	}
	        $companyWorkShift = new CompanyWorkShift;
		 	$companyWorkShift->shift_name = $request->shift_name;
		 	$companyWorkShift->shift_start_time = $request->shift_start_time;
		 	$companyWorkShift->shift_end_time = $request->shift_end_time;
		 	$companyWorkShift->shift_color = $request->shift_color;
		 	$companyWorkShift->status = '1';
            $companyWorkShift->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$companyWorkShift->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Company','message_create') ,$companyWorkShift, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function show($id)
    {
        try {
            $user = getUser();
            $checkId= CompanyWorkShift::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Company','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            $companyWorkShift = CompanyWorkShift::where('id',$id)->first();
            return prepareResult(true,'View Workshift' ,$companyWorkShift, config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try 
        {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[ 
        		'shift_name' => 'required',   
        		'shift_start_time' => 'required|date_format:H:i',   
        		'shift_end_time' => 'required|date_format:H:i',   
	        ],
            [
            'shift_name.required' =>  getLangByLabelGroups('Company','message_shift_name'),
            'shift_start_time.required' =>  getLangByLabelGroups('Company','message_shift_start_time'),
            'shift_end_time.required' =>  getLangByLabelGroups('Company','message_shift_end_time'),
            'shift_end_time.after' =>  getLangByLabelGroups('Company','message_shift_end_time_after'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkId = CompanyWorkShift::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Company','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            $checkAlready = CompanyWorkShift::where('id','!=',$id)->where('shift_name',$request->shift_name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false, getLangByLabelGroups('Company','message_name_already_exists'),[], config('httpcodes.bad_request')); 

        	}
	        $companyWorkShift = CompanyWorkShift::find($id);
		 	$companyWorkShift->shift_name = $request->shift_name;
		 	$companyWorkShift->shift_start_time = $request->shift_start_time;
		 	$companyWorkShift->shift_end_time = $request->shift_end_time;
		 	$companyWorkShift->shift_color = $request->shift_color;
		 	$companyWorkShift->status = ($request->status) ? $request->status : '1';
            $companyWorkShift->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$companyWorkShift->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Company','message_update') ,$companyWorkShift, config('httpcodes.success'));
			  
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
        	$checkId= CompanyWorkShift::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Company','message_id_not_found'), [],config('httpcodes.not_found'));
            }
        	$checkId->delete();
            DB::commit();  
         	return prepareResult(true,getLangByLabelGroups('Company','message_delete') ,[], config('httpcodes.success'));
         }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }

    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('shift_name')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "shift_name like '%" .trim(strtolower($request->input('shift_name'))) . "%')";
             
        }
        if (is_null($request->shift_start_time) == false || is_null($request->shift_end_time) == false) {
           
            if ($w != '') {$w = $w . " AND ";}

            if ($request->shift_start_time != '')
            {
              $w = $w . "("."shift_start_time >= '".date('y-m-d',strtotime($request->shift_start_time))."')";
            }
            if (is_null($request->shift_start_time) == false && is_null($request->shift_end_time) == false) 
                {

              $w = $w . " AND ";
            }
            if ($request->shift_end_time != '')
            {
                $w = $w . "("."shift_start_time <= '".date('y-m-d',strtotime($request->shift_end_time))."')";
            }
            
          
           
        }
        return($w);

    }

    public function companySubscriptionExtend(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'licence_key'  => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
                $user_id = Auth::user()->top_most_parent_id;
                $licenceKeyData = LicenceKeyManagement::where('top_most_parent_id',$user_id)->where('license_key',$request->licence_key)->where('is_used',0)->first();
                if(empty($licenceKeyData))
                {
                    return prepareResult(false,getLangByLabelGroups('LicenceKey','message_invalid_data') ,[], config('httpcodes.success'));
                }
                $package_details =  json_decode($licenceKeyData->package_details);
                $package_expire_at = date('Y-m-d', strtotime($package_details->validity_in_days.' days'));

                LicenceKeyManagement::where('top_most_parent_id',$user_id)->where('license_key',$request->licence_key)->where('is_used',0)->update(['is_used' => 1]);


                $packageSubscribe = new Subscription;
                $packageSubscribe->user_id = $user_id;
                $packageSubscribe->package_id = $package_details->id;
                $packageSubscribe->package_details = $package_details;
                $packageSubscribe->license_key = $request->licencse_key;
                $packageSubscribe->start_date = date('Y-m-d');
                $packageSubscribe->end_date = $package_expire_at;
                $packageSubscribe->status = 1;
                $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $packageSubscribe->save();

                $modules_attached = json_decode($licenceKeyData->module_attached);

                if($modules_attached  && sizeof($modules_attached) >0)
                { 
                    foreach ($modules_attached as $key => $module) 
                    {
                        $count = AssigneModule::where('user_id',$user_id)->where('module_id',$module)->count();
                        if($count<1)
                        { 
                            $assigneModule = new AssigneModule;
                            $assigneModule->user_id = $user_id;
                            $assigneModule->module_id = $module;
                            $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                            $assigneModule->save();
                        }
                    }
                }

                User::where('id',$user_id)->update(['license_status' => 1]);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('LicenceKey','message_updated') ,$licenceKeyData, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
}
