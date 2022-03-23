<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deviation;
use Validator;
use Auth;
use DB;
use Exception
class DeviationController extends Controller
{
	public function deviations(Request $request)
    {
        try {
	        $user = getUser();
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =   Deviation:::whereRaw($whereRaw)
                ->orderBy('id', 'DESC')
                ->with('Parent:id,title','Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name')
               
            } else {
                $query =  Deviation::orderBy('id', 'DESC')
                ->with('Parent:id,title','Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name')
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
                return prepareResult(true,"Deviation list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Deviation list",$query,$this->success);
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }

    public function store(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[  
        		'category_id' => 'required|exists:category_masters,id',   
        		'title' => 'required',   
        		'description' => 'required',       
	        ],
            [  
                'category_id' =>  getLangByLabelGroups('Deviation','category_id'),   
                'title' =>  getLangByLabelGroups('Deviation','title'),   
                'description' =>  getLangByLabelGroups('Deviation','description'),     
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	
        	
	        $Deviation = new Deviation;
		 	$Deviation->activity_id = $request->activity_id;
            $Deviation->branch_id = $request->branch_id;
		 	$Deviation->journal_id = $request->journal_id;
		 	$Deviation->ip_id = $request->ip_id;
		 	$Deviation->patient_id = $request->patient_id;
		 	$Deviation->emp_id = $request->emp_id;
		 	$Deviation->category_id = $request->category_id;
		 	$Deviation->subcategory_id = $request->subcategory_id;
		 	$Deviation->title = $request->title;
		 	$Deviation->description = $request->description;
		 	$Deviation->is_deviation = ($request->is_deviation)? $request->is_deviation :0;
		 	$Deviation->not_a_deviation = ($request->not_a_deviation)? $request->not_a_deviation :0;
		 	$Deviation->reason_of_not_being_deviation = ($request->reason_of_not_being_deviation)? $request->reason_of_not_being_deviation :null;
            $Deviation->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$Deviation->save();
	        return prepareResult(true,getLangByLabelGroups('Deviation','create') ,$Deviation, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[    
                'category_id' => 'required|exists:category_masters,id',   
        		'title' => 'required',   
        		'description' => 'required',      
	        ],
            [   
                'category_id' =>  getLangByLabelGroups('Deviation','category_id'),   
                'title' =>  getLangByLabelGroups('Deviation','title'),   
                'description' =>  getLangByLabelGroups('Deviation','description'),     
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkId = Deviation::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],$this->not_found);
            }
            
        	$parent_id  = (is_null($checkId->parent_id)) ? $id : $checkId->parent_id;
        	$Deviation = new Deviation;
	       	$Deviation->parent_id = $parent_id;
		 	$Deviation->activity_id = $request->activity_id;
            $Deviation->branch_id = $request->branch_id;
		 	$Deviation->journal_id = $request->journal_id;
		 	$Deviation->ip_id = $request->ip_id;
		 	$Deviation->patient_id = $request->patient_id;
		 	$Deviation->emp_id = $request->emp_id;
		 	$Deviation->category_id = $request->category_id;
		 	$Deviation->subcategory_id = $request->subcategory_id;
		 	$Deviation->title = $request->title;
		 	$Deviation->description = $request->description;
		 	$Deviation->is_deviation = ($request->is_deviation)? $request->is_deviation :0;
		 	$Deviation->not_a_deviation = ($request->not_a_deviation)? $request->not_a_deviation :0;
		 	$Deviation->reason_of_not_being_deviation = ($request->reason_of_not_being_deviation)? $request->reason_of_not_being_deviation :null;
		 	$Deviation->edited_by = $user->id;
		 	$Deviation->reason_for_editing = $request->reason_for_editing;
            $Deviation->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$Deviation->save();
		 
	        return prepareResult(true,getLangByLabelGroups('Deviation','update') ,$Deviation, $this->success);
			  
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= Deviation::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],$this->not_found);
            }
        	$Deviation = Deviation::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Deviation','delete') ,[], $this->success);
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }
    public function approvedDeviation(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'id' => 'required',   
	        ],
            [
                'id' =>  getLangByLabelGroups('Journal','id'),   
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$id = $request->id;
        	$checkId= Deviation::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],$this->not_found);
            }
            $Deviation = Deviation::find($id);
		 	$Deviation->approved_by = $user->id;
		 	$Deviation->approved_date = date('Y-m-d');
		 	$Deviation->status = '1';
		 	$Deviation->save();
	        return prepareResult(true,getLangByLabelGroups('Deviation','approve') ,$Deviation, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= Deviation::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],$this->not_found);
            }

        	$Deviation = Deviation::where('id',$id)->with('Parent:id,title','Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name','children')->first();
	        return prepareResult(true,'View Patient plan' ,$Deviation, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('branch_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "branch_id = "."'" .$request->input('branch_id')."'".")";
        }
        return($w);

    }
    
}
