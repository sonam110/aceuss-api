<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use Exception;
use App\Models\PersonalInfoDuringIp;
class PersonController extends Controller
{
    
     public function patientPersonList(Request $request){
        try {
            $user = getUser();
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query= PersonalInfoDuringIp::whereRaw($whereRaw)->with('Country');
            } else {
                $query= PersonalInfoDuringIp::with('Country');
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
                return prepareResult(true,"Person list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,'Person List' ,$query, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    
     public function store(Request $request){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'patient_id' => 'required|exists:users,id',      
        		'name' => 'required',   
        		'email' => 'required|email',   
        		'contact_number' => 'required',   
	        ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
	        $personalInfo = new PersonalInfoDuringIp;
		 	$personalInfo->patient_id = $request->patient_id;
            $personalInfo->ip_id = $request->ip_id;
            $personalInfo->follow_up_id = $request->follow_up_id;
            $personalInfo->name = $request->name ;
            $personalInfo->email = $request->email ;
            $personalInfo->contact_number = $request->contact_number;
            $personalInfo->country_id = $request->country_id;
            $personalInfo->city = $request->city;
            $personalInfo->postal_area = $request->postal_area;
            $personalInfo->zipcode = $request->zipcode;
            $personalInfo->full_address = $request->full_address ;
            $personalInfo->personal_number = $request->personal_number ;
            $personalInfo->is_family_member = ($request->is_family_member == true) ? 1 : 0 ;
            $personalInfo->is_caretaker = ($request->is_caretaker == true) ? 1 : 0 ;
            $personalInfo->is_contact_person = ($request->is_contact_person == true) ? 1 : 0 ;
            $personalInfo->is_guardian = ($request->is_guardian == true) ? 1 : 0 ;
            $personalInfo->is_other = ($request->is_other == true) ? 1 : 0 ;
            $personalInfo->is_presented = ($request->is_presented == true) ? 1  : 0 ;
            $personalInfo->is_participated = ($request->is_participated == true) ? 1 : 0 ;
            $personalInfo->how_helped = $request->how_helped;
            $personalInfo->is_other_name = $request->is_other_name;
            $personalInfo->save();
             DB::commit();
	        return prepareResult(true,getLangByLabelGroups('CompanyType','create') ,$personalInfo, $this->success);
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function show($id){
        
        try {
            $user = getUser();
            $personInfo= PersonalInfoDuringIp::where('id',$id)->with('patient:id,name,email','PatientImplementationPlan')->first();
            if (!is_object($personInfo)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','id_not_found'), [],$this->not_found);
            }
            
            return prepareResult(true,'View Compan Type' ,$personInfo, $this->success);
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'patient_id' => 'required|exists:users,id',      
        		'name' => 'required',   
        		'email' => 'required|email',   
        		'contact_number' => 'required',   
	        ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkId = PersonalInfoDuringIp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CompanyType','id_not_found'), [],$this->not_found);
            }
            
	        $personalInfo = PersonalInfoDuringIp::find($id);
		 	$personalInfo->patient_id = $request->patient_id;
            $personalInfo->ip_id = $request->ip_id;
            $personalInfo->follow_up_id = $request->follow_up_id;
            $personalInfo->name = $request->name ;
            $personalInfo->email = $request->email ;
            $personalInfo->contact_number = $request->contact_number;
            $personalInfo->country_id = $request->country_id;
            $personalInfo->city = $request->city;
            $personalInfo->postal_area = $request->postal_area;
            $personalInfo->zipcode = $request->zipcode;
            $personalInfo->full_address = $request->full_address ;
            $personalInfo->personal_number = $request->personal_number ;
            $personalInfo->is_family_member = ($request->is_family_member == true) ? 1 : 0 ;
            $personalInfo->is_caretaker = ($request->is_caretaker == true) ? 1 : 0 ;
            $personalInfo->is_contact_person = ($request->is_contact_person == true) ? 1 : 0 ;
            $personalInfo->is_guardian = ($request->is_guardian == true) ? 1 : 0 ;
            $personalInfo->is_other = ($request->is_other == true) ? 1 : 0 ;
            $personalInfo->is_presented = ($request->is_presented == true) ? 1  : 0 ;
            $personalInfo->is_participated = ($request->is_participated == true) ? 1 : 0 ;
            $personalInfo->how_helped = $request->how_helped;
            $personalInfo->is_other_name = $request->is_other_name;
            $personalInfo->save();
             DB::commit();
	        return prepareResult(true,getLangByLabelGroups('CompanyType','update'),$personalInfo, $this->success);
			    
		       
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
    	
        try {
            $user = getUser();
        	$checkId= PersonalInfoDuringIp::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','id_not_found'), [],$this->not_found);
            }
            
        	$personDelete = PersonalInfoDuringIp::where('id',$id)->delete();
         	return prepareResult(true, getLangByLabelGroups('CompanyType','delete') ,[], $this->success);
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('ip_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "ip_id = "."'" .$request->input('ip_id')."'".")";
        }
        if (is_null($request->input('patient_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "patient_id = "."'" .$request->input('patient_id')."'".")";
        }
        if (is_null($request->input('follow_up_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "follow_up_id = "."'" .$request->input('follow_up_id')."'".")";
        }
        
       
        return($w);

    }
}
