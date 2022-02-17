<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankDetail;
use Validator;
use Auth;
use Exception;
use DB;
class BankDetailController extends Controller
{
	public function bankList(Request $request)
    {
        try {
	        $user = getUser();
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  BankDetail::where('user_id',$user->id)
                ->whereRaw($whereRaw)
                ->orderBy('id', 'DESC')
                ->with('user:id,name');
            } else {
                $query = BankDetail::where('user_id',$user->id)
                ->orderBy('id', 'DESC')
                ->with('user:id,name');
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
                return prepareResult(true,"Bank list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Bank list",$query,$this->success);
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }

    public function store(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'bank_name' => 'required',     
        		'account_number' => 'required',   
        		'clearance_number' => 'required',   
	        ],
		    [
            'bank_name.required' => getLangByLabelGroups('Bank','bank_name'),
            'account_number.required' => getLangByLabelGroups('Bank','account_number'),
            'clearance_number.required' => getLangByLabelGroups('Bank','clearance_number'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkAlready = BankDetail::where('user_id',$user->id)->where('bank_name',$request->bank_name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false, getLangByLabelGroups('Bank','name_already_exists'),[], $this->unprocessableEntity); 
        	}
	        $bankDetail = new BankDetail;
		 	$bankDetail->user_id = $user->id;
		 	$bankDetail->bank_name = $request->bank_name;
		 	$bankDetail->account_number = $request->account_number;
		 	$bankDetail->clearance_number = $request->clearance_number;
		 	$bankDetail->is_default = ($request->is_default) ? '1' :'0';
            $bankDetail->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$bankDetail->save();
		 	if($request->is_default){
			 	$update_is_default = BankDetail::where('id','!=',$bankDetail->id)->where('user_id',$user->id)->update(['is_default'=>'0']);
			}
	        return prepareResult(true,getLangByLabelGroups('Bank','create') ,$bankDetail, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function editBank(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'id' => 'required',     
        		'bank_name' => 'required',     
        		'account_number' => 'required',   
        		'clearance_number' => 'required',   
	        ],
            [
            'id.required' => getLangByLabelGroups('Bank','id'),
            'bank_name.required' => getLangByLabelGroups('Bank','bank_name'),
            'account_number.required' => getLangByLabelGroups('Bank','account_number'),
            'clearance_number.required' => getLangByLabelGroups('Bank','clearance_number'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$id = $request->id;
        	$checkId = BankDetail::where('user_id',$user->id)->where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Bank','id_not_found'), [],$this->not_found);
            }
            $checkAlready = BankDetail::where('id','!=',$id)->where('user_id',$user->id)->where('bank_name',$request->bank_name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false, getLangByLabelGroups('Bank',' name_already_exists'),[], $this->unprocessableEntity); 
        	}
	        $bankDetail = BankDetail::find($id);
		 	$bankDetail->user_id = $user->id;
		 	$bankDetail->bank_name = $request->bank_name;
		 	$bankDetail->account_number = $request->account_number;
		 	$bankDetail->clearance_number = $request->clearance_number;
		 	$bankDetail->is_default = ($request->is_default) ? '1' :'0';
            $bankDetail->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$bankDetail->save();
		 	if($request->is_default){
			 	$update_is_default = BankDetail::where('id','!=',$bankDetail->id)->where('user_id',$user->id)->update(['is_default'=>'0']);
			}
	        return prepareResult(true, getLangByLabelGroups('Bank','update') ,$bankDetail, $this->success);
			  
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function deleteBank(Request $request){
    	
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
	            'id' =>  'required',
	        ],
            [
            'id.required' => getLangByLabelGroups('Bank','id'),
            ]);
	        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$id = $request->id;

        	$checkId= BankDetail::where('user_id',$user->id)->where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Bank','id_not_found'), [],$this->not_found);
            }
            
        	$bankDetail = BankDetail::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Bank','delete'),[], $this->success);
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }
     public function viewBank(Request $request){
        
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'id' =>  'required',
            ],
            [
            'id.required' => getLangByLabelGroups('Bank','id'),
            ]);
            if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $id = $request->id;

            $checkId= BankDetail::where('user_id',$user->id)->where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Bank','id_not_found'), [],$this->not_found);
            }
            
            $bankDetail = BankDetail::where('id',$id)->with('User:id,name')->first();
            return prepareResult(true,'view bank detail',$bankDetail, $this->success);
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }
    
}
