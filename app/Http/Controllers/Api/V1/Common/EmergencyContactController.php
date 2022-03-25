<?php

namespace App\Http\Controllers\Api\v1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyContact;
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
                return prepareResult(true,"EmergencyContact list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"EmergencyContact list",$query,$this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
        
    }

    

   public function store(Request $request){
        try {
        	$user = getUser();
            $validator = Validator::make($request->all(),[
                'contact_number' => 'required|numeric',   
            ],
            [
            'contact_number.required' => 'Contact Number Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $EmergencyContact = new EmergencyContact;
            $EmergencyContact->contact_number = $request->contact_number;
            $EmergencyContact->is_default = ($request->is_default) ? 1:0;
            $EmergencyContact->created_by = $user->id;
            $EmergencyContact->save();
            if($request->is_default) {
            	$updateDefault = EmergencyContact::where('id','!=',$EmergencyContact->id)->update(['is_default'=>'0']);
            }
            return prepareResult(true,getLangByLabelGroups('CompanyType','create') ,$EmergencyContact, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
           $user = getUser();
            $validator = Validator::make($request->all(),[
                'contact_number' => 'required|numeric',   
            ],
            [
            'contact_number.required' => 'Contact Number Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkId = EmergencyContact::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CompanyType','id_not_found'), [],$this->not_found);
            }
          
            $EmergencyContact = EmergencyContact::find($id);
            $EmergencyContact->contact_number = $request->contact_number;
            $EmergencyContact->is_default = ($request->is_default) ? 1:0;
            $EmergencyContact->created_by = $user->id;
            $EmergencyContact->save();
            if($request->is_default) {
            	$updateDefault = EmergencyContact::where('id','!=',$EmergencyContact->id)->update(['is_default'=>'0']);
            }
            return prepareResult(true,getLangByLabelGroups('CompanyType','update'),$EmergencyContact, $this->success);
                
               
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
        
        try {
            $checkId= EmergencyContact::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','id_not_found'), [],$this->not_found);
            }
            $EmergencyContact = EmergencyContact::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('CompanyType','delete') ,[], $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
}
