<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\AssigneModule;
use App\Models\Package;
use App\Models\Subscription;
use Validator;
use Auth;
use Exception;
use DB;

class ModuleController extends Controller
{
    public function modules(Request $request)
    {
        try {
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query = Module::orderBy('id', 'DESC')->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = Module::orderBy('id', 'DESC');
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
                return prepareResult(true,"Module list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
		    
		    return prepareResult(true,"Module list",$query,$this->success);
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'name' => 'required',   
	        ],
		    [
            'name.required' => getLangByLabelGroups('Module','name'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkAlready = Module::where('name',$request->name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,getLangByLabelGroups('Module','name_already_exists'),[], $this->unprocessableEntity); 
        	}
	        $Module = new Module;
		 	$Module->name = $request->name;
            $Module->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$Module->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Module','create') ,$Module, $this->success);
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    
    public function show($id)
    {
        try {
            $checkId= Module::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Module','id_not_found'), [],$this->not_found);
            }
            $module = Module::where('id',$id)->first();
            return prepareResult(true,'View Module',$module, $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }

    public function update(Request $request,$id) 
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
	           	'name' => 'required',   
			
	        ],
	    	[
            'name.required' => getLangByLabelGroups('Module','name'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkId = Module::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Module','id_not_found'), [],$this->not_found);
            }
            $checkAlready = Module::where('id','!=',$id)->where('name',$request->name)->first(); 
        	if($checkAlready) {

              	return prepareResult(false,getLangByLabelGroups('Module','name_already_exists'),[], $this->unprocessableEntity); 

        	}
	        $Module = Module::find($id);
		 	$Module->name = $request->name;
            $Module->status = ($request->name) ? $request->status:'1';
            $Module->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$Module->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Module','update') ,$Module, $this->success);
			    
		       
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
        }
    }

    public function destroy($id) 
    {
        try {
        	$checkId= Module::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('Module','id_not_found'), [],$this->not_found);
            }
            
        	$Module = Module::findOrFail($id);
            $Module->delete();
         	return prepareResult(true, getLangByLabelGroups('Module','delete') ,[], $this->success);
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }

    /* -------------Assigne Package ---------------------*/
    public function assigenPackage(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'user_id' =>  'required',
                'package_id' =>  'required',
            ]);
            if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            $checkId= Package::where('id',$request->package_id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,"Id Not Found", [],$this->not_found);
            }
            $checkAlreadyAssigne= Subscription::where('user_id',$request->user_id)->where('id',$request->package_id)->first();
            if (is_object($checkAlreadyAssigne)) {

                $package = Package::where('id',$request->package_id)->first();
                $packageSubscribe = Subscription::find($checkAlreadyAssigne->id);
                $packageSubscribe->user_id = $request->user_id;
                $packageSubscribe->package_id = $request->package_id;
                $packageSubscribe->package_details = $package;
                $packageSubscribe->start_date = date('Y-m-d');
                $packageSubscribe->end_date = date('Y-m-d', strtotime("+".$package->validity_in_days." days"));
                $packageSubscribe->status = '1';
                $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
                $packageSubscribe->save();
            }

            $package = Package::where('id',$request->package_id)->first();
            $packageSubscribe = new Subscription;
            $packageSubscribe->user_id = $request->user_id;
            $packageSubscribe->package_id = $request->package_id;
            $packageSubscribe->package_details = $package;
            $packageSubscribe->start_date = date('Y-m-d');
            $packageSubscribe->end_date = date('Y-m-d', strtotime("+".$package->validity_in_days." days"));
            $packageSubscribe->status = '1';
            $packageSubscribe->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $packageSubscribe->save();
            DB::commit();
            return prepareResult(true,'Package Assigned successfully' ,$packageSubscribe, $this->success);
                
                
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);  
        }
    }

    public function assigenModule(Request $request)
    {
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'user_id' =>  'required',
                'module_id' =>  'required',
            ]);
            if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
            }
            if(is_array($request->module_id) ){
                $deletOld = AssigneModule::where('user_id',$request->user_id)->delete();
                for ($i = 0;$i < sizeof($request->module_id);$i++) {
                    if (!empty($request->module_id[$i])) {
                        $assigneModule = new AssigneModule;
                        $assigneModule->user_id = $request->user_id;
                        $assigneModule->module_id = $request->module_id[$i] ;
                        $assigneModule->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web'; 
                        $assigneModule->save();
                    }
                }
            }
            $assignemodules = AssigneModule::where('user_id',$request->user_id)->with('Module:id,name')->get();
            return prepareResult(true,'Module assigne successfully' ,$assignemodules, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
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
