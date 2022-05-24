<?php

namespace App\Http\Controllers\Api\v1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyContact;
use App\Models\User;
use Validator;
use Auth;
use Exception;
use DB;

class EmergencyContactController extends Controller
{
    public function emergencyContact(Request $request)
    {
        try {
            $query = EmergencyContact::orderBy('id', 'DESC');
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
                return prepareResult(true,"EmergencyContact list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"EmergencyContact list",$query,config('httpcodes.success'));
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
                'user_id' => 'required|exists:users,id',   
            ],
            [
            'user_id.required' => 'User Id  Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $getUser = User::where('id',$request->user_id)->first();
            $EmergencyContact = new EmergencyContact;
            $EmergencyContact->user_id = $request->user_id;
            $EmergencyContact->contact_number = ($getUser) ? $getUser->contact_number: null;
            $EmergencyContact->is_default = ($request->is_default) ? 1:0;
            $EmergencyContact->created_by = $user->id;
            $EmergencyContact->save();
            DB::commit();
            if($request->is_default) {
            	$updateDefault = EmergencyContact::where('id','!=',$EmergencyContact->id)->update(['is_default'=>'0']);
            }
            return prepareResult(true,getLangByLabelGroups('message_CompanyType','create') ,$EmergencyContact, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error')); 
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
           $user = getUser();
            $validator = Validator::make($request->all(),[
                'user_id' => 'required|exists:users,id',    
            ],
            [
            'user_id.required' => 'User Id  Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkId = EmergencyContact::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('message_CompanyType','id_not_found'), [],config('httpcodes.not_found'));
            }
            $getUser = User::where('id',$request->user_id)->first();
            $EmergencyContact = EmergencyContact::find($id);
            $EmergencyContact->user_id = $request->user_id;
            $EmergencyContact->contact_number = ($getUser) ? $getUser->contact_number: null;
            $EmergencyContact->is_default = ($request->is_default) ? 1:0;
            $EmergencyContact->created_by = $user->id;
            $EmergencyContact->save();
            DB::commit();
            if($request->is_default) {
            	$updateDefault = EmergencyContact::where('id','!=',$EmergencyContact->id)->update(['is_default'=>'0']);
            }
            return prepareResult(true,getLangByLabelGroups('message_CompanyType','update'),$EmergencyContact, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        try {
            $checkId= EmergencyContact::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('message_CompanyType','id_not_found'), [],config('httpcodes.not_found'));
            }
            $EmergencyContact = EmergencyContact::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('message_CompanyType','delete') ,[], config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
