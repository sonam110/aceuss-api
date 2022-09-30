<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Exception;
use App\Models\CompanySetting;
class SettingController extends Controller
{
    protected $top_most_parent_id;
    public function __construct()
    {
        $this->middleware('permission:settings-edit', ['only' => ['settingUpdate']]);

        $this->middleware(function ($request, $next) {
            $this->top_most_parent_id = auth()->user()->top_most_parent_id;
            return $next($request);
        });
        
    }
    public function settingUpdate(Request $request)
    { 
        DB::beginTransaction();
        try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[
                'company_name' => 'required',  
                'company_email' => 'required',  
                'company_contact' => 'required', 
                'company_address' => 'required', 
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $user = CompanySetting::where('user_id', $this->top_most_parent_id)->first();
            if(!$user){
            	$user = new CompanySetting;
            }
            $user->user_id = $userInfo->id;
            $user->company_name = $request->company_name;
            if(!empty($request->company_logo))
            {
                $user->company_logo = $request->company_logo;
            }
            $user->company_email = $request->company_email;
            $user->company_contact = $request->company_contact;
            $user->company_address = $request->company_address;
            $user->company_website = $request->company_website;
            $user->before_minute = $request->before_minute;
            $user->extra_hour_rate = $request->extra_hour_rate;
            $user->ob_hour_rate = $request->ob_hour_rate;
            $user->relaxation_time = $request->relaxation_time;
            $user->follow_up_reminder = ($request->follow_up_reminder) ? 1:0 ;
            $user->save();

            $companyInfo = $user->companyInfo;
            $companyInfo->postal_area = $request->postal_area;
            $companyInfo->branch_name = $request->company_name;
            $companyInfo->branch_email = $request->company_email;
            $companyInfo->zipcode = $request->zipcode;
            $companyInfo->city = $request->city;
            $companyInfo->organization_number = $request->organization_number;
            $companyInfo->save();
             DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_update'),$user, config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
