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
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkSettings = CompanySetting::where('user_id',$userInfo->id)->first();
            if(empty($checkSettings)){
            	$user = new CompanySetting;
            } else {
            	$user = CompanySetting::find($userInfo->id);
            }
            $user->user_id = $userInfo->id;
            $user->company_name = $request->company_name;
            $user->company_logo = $request->company_logo;
            $user->company_email = $request->company_email;
            $user->company_contact = $request->company_contact;
            $user->company_address = $request->company_address;
            $user->contact_person_name = $request->contact_person_name;
            $user->contact_person_email = $request->contact_person_email;
            $user->contact_person_phone = $request->contact_person_phone;
            $user->company_website = $request->company_website;
            $user->before_minute = $request->before_minute;
            $user->follow_up_reminder = ($request->follow_up_reminder) ? 1:0 ;
            $user->save();
             DB::commit();
            return prepareResult(true,getLangByLabelGroups('UserValidation','update'),$user, $this->success);
                
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
}
