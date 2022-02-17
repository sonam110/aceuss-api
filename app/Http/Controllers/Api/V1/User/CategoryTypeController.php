<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryType;
use Validator;
use Auth;
use Exception;
use DB;
class CategoryTypeController extends Controller
{
    public function categoryTypes(Request $request)
    {
        try {
	        $user = getUser();
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query = CategoryType::select('id','name')->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = CategoryType::select('id','name')->orderBy('id', 'DESC');
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
                return prepareResult(true,"CategoryType list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"CategoryType list",$query,$this->success);
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }

    public function store(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'name' => 'required',   
	        ],
		    [
            'name.required' => getLangByLabelGroups('CategoryType','name'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkAlready = CategoryType::where('name',$request->name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,getLangByLabelGroups('CategoryType','name_already_exists'),[], $this->unprocessableEntity); 
        	}
	        $categoryType = new CategoryType;
		 	$categoryType->created_by = $user->id;
		 	$categoryType->name = $request->name;
            $categoryType->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$categoryType->save();
	        return prepareResult(true,getLangByLabelGroups('CategoryType','create') ,$categoryType, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function show($id){
        
        try {
            $user = getUser();
            $checkId= CategoryType::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CategoryType','id_not_found'), [],$this->not_found);
            }
            
            $categoryType = CategoryType::where('id',$id)->first();
            return prepareResult(true,'Category Type view' ,$categoryType, $this->success);
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
	           	'name' => 'required',   
			
	        ],
	    	[
            'name.required' => getLangByLabelGroups('CategoryType','name'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkId = CategoryType::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CategoryType','id_not_found'), [],$this->not_found);
            }
            $checkAlready = CategoryType::where('id','!=',$id)->where('name',$request->name)->first(); 
        	if($checkAlready) {

              	return prepareResult(false,getLangByLabelGroups('CategoryType','name_already_exists'),[], $this->unprocessableEntity); 

        	}
	        $categoryType = CategoryType::find($id);
		 	$categoryType->name = $request->name;
            $categoryType->status = ($request->status) ? $request->status:'1';
            $categoryType->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$categoryType->save();
	        return prepareResult(true,getLangByLabelGroups('CategoryType','update') ,$categoryType, $this->success);
			    
		       
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= CategoryType::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CategoryType','id_not_found'), [],$this->not_found);
            }
            
        	$categoryType = CategoryType::findOrFail($id);
            $categoryType->delete();
         	return prepareResult(true,getLangByLabelGroups('CategoryType','delete') ,[], $this->success);
		     	
			    
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
        return($w);

    }
}
