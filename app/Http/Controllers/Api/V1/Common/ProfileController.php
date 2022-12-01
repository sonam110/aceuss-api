<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Exception;
use App\Models\User;

class ProfileController extends Controller
{
	public function updateProfile(Request $request)
    {
    	DB::beginTransaction();
	    try {
            $userInfo = getUser();
            $validator = Validator::make($request->all(),[
                'name' => 'required',  
                'email'     => 'required|email|unique:users,email,'.$userInfo->id,
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $user = User::find($userInfo->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->country_id = $request->country_id;
            $user->contact_number = $request->contact_number;
            $user->personal_number = $request->personal_number;
            $user->full_address = !empty($request->full_address) ? $request->full_address : $user->full_address;
            $user->city = !empty($request->city) ? $request->city : $user->city;
            $user->gender = $request->gender;
            $user->postal_area = !empty($request->postal_area) ? $request->postal_area : $user->postal_area;
            $user->zipcode = !empty($request->zipcode) ? $request->zipcode : $user->zipcode;
            if(!empty($request->contact_person_number))
            {
                $user->contact_person_number = $request->contact_person_number;
            }
            if(!empty($request->contact_person_name))
            {
                $user->contact_person_name = $request->contact_person_name;
            }
            $user->avatar = (!empty($request->avatar)) ? $request->avatar : env('NO_IMG_PATH');
            $user->save();

            if(!empty($request->company_logo))
            {
                $companySetting = $user->companySetting;
                $companySetting->company_logo = $request->company_logo;
                $companySetting->save();
            }

            DB::commit();
            return prepareResult(true,getLangByLabelGroups('UserValidation','message_update'),$user, config('httpcodes.success'));
	    }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
