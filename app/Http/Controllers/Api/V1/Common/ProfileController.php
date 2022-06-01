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
            $user->city = $request->city;
            $user->postal_area = $request->postal_area;
            $user->contact_number = $request->contact_number;
            $user->gender = $request->gender;
            $user->personal_number = $request->personal_number;
            $user->organization_number = $request->organization_number;
            $user->zipcode = $request->zipcode;
            $user->full_address = $request->full_address;
            $user->avatar = (!empty($request->avatar)) ? $request->avatar :'https://aceuss.3mad.in/uploads/no-image.png';
            $user->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('UserValidation','message_update'),$user, config('httpcodes.success'));
	    }
        catch(Exception $exception) {
        	\Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
