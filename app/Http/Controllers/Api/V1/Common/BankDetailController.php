<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankDetail;
use Validator;
use Auth;
use Exception;
use DB;

class BankDetailController extends Controller
{
     public function __construct()
    {

        $this->middleware('permission:bank-browse',['except' => ['show']]);
        $this->middleware('permission:bank-add', ['only' => ['store']]);
        $this->middleware('permission:bank-edit', ['only' => ['update']]);
        $this->middleware('permission:bank-read', ['only' => ['show']]);
        $this->middleware('permission:bank-delete', ['only' => ['destroy']]);
        
    }
	public function banks(Request $request)
    {
        try {
	        $user = getUser();
            $query = BankDetail::where('user_id',$user->id)
            ->orderBy('id', 'DESC')
            ->with('user:id,name');
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
                return prepareResult(true,"Bank list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Bank list",$query,config('httpcodes.success'));
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
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkAlready = BankDetail::where('user_id',$user->id)->where('bank_name',$request->bank_name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false, getLangByLabelGroups('Bank','name_already_exists'),[], config('httpcodes.bad_request')); 
        	}
	        $bankDetail = new BankDetail;
		 	$bankDetail->user_id = $user->id;
		 	$bankDetail->bank_name = $request->bank_name;
		 	$bankDetail->account_number = $request->account_number;
		 	$bankDetail->clearance_number = $request->clearance_number;
		 	$bankDetail->is_default = ($request->is_default) ? '1' :'0';
            $bankDetail->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$bankDetail->save();
            DB::commit();
		 	if($request->is_default){
			 	$update_is_default = BankDetail::where('id','!=',$bankDetail->id)->where('user_id',$user->id)->update(['is_default'=>'0']);
			}
	        return prepareResult(true,getLangByLabelGroups('Bank','create') ,$bankDetail, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id)
    {
        try {
            $user = getUser();
            $checkId= BankDetail::where('user_id',$user->id)->where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Bank','id_not_found'), [],config('httpcodes.not_found'));
            }
            
            $bankDetail = BankDetail::where('id',$id)->with('User:id,name')->first();
            return prepareResult(true,'view bank detail',$bankDetail, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
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
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkId = BankDetail::where('user_id',$user->id)->where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Bank','id_not_found'), [],config('httpcodes.not_found'));
            }
            $checkAlready = BankDetail::where('id','!=',$id)->where('user_id',$user->id)->where('bank_name',$request->bank_name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false, getLangByLabelGroups('Bank',' name_already_exists'),[], config('httpcodes.bad_request')); 
        	}
	        $bankDetail = BankDetail::find($id);
		 	$bankDetail->user_id = $user->id;
		 	$bankDetail->bank_name = $request->bank_name;
		 	$bankDetail->account_number = $request->account_number;
		 	$bankDetail->clearance_number = $request->clearance_number;
		 	$bankDetail->is_default = ($request->is_default) ? '1' :'0';
            $bankDetail->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$bankDetail->save();
            DB::commit();
		 	if($request->is_default){
			 	$update_is_default = BankDetail::where('id','!=',$bankDetail->id)->where('user_id',$user->id)->update(['is_default'=>'0']);
			}
	        return prepareResult(true, getLangByLabelGroups('Bank','update') ,$bankDetail, config('httpcodes.success'));
			  
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
	    	$user = getUser();
        	$checkId= BankDetail::where('user_id',$user->id)->where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Bank','id_not_found'), [],config('httpcodes.not_found'));
            }
            
        	$bankDetail = BankDetail::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Bank','delete'),[], config('httpcodes.success'));
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
        return($w);
    } 
}
