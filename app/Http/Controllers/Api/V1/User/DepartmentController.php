<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use Validator;
use Auth;
use Exception;
use DB;
class DepartmentController extends Controller
{
     public function __construct()
    {

        $this->middleware('permission:departments-browse',['except' => ['show']]);
        $this->middleware('permission:departments-add', ['only' => ['store']]);
        $this->middleware('permission:departments-edit', ['only' => ['update']]);
        $this->middleware('permission:departments-read', ['only' => ['show']]);
        $this->middleware('permission:departments-delete', ['only' => ['destroy']]);
        
    }
	public function departments(Request $request)
    {
        try {
	        $user = getUser();
	        $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  Department::whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query = Department::orderBy('id', 'DESC');
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
                return prepareResult(true,"Department list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Department list",$query,config('httpcodes.success'));
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    	
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'name' => 'required',   
	        ],
		    [
            'name.required' =>  getLangByLabelGroups('Department','name'),
            ]);

	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkAlready = Department::where('name',$request->name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false, getLangByLabelGroups('Department','name_already_exists'),[], config('httpcodes.bad_request')); 
        	}

            $topParent = findTopParentId($request->parent_id);
            $level = $this->checkLevel($topParent);
            if(!empty($request->parent_id) && $level == '5'){
                return prepareResult(false,'Child level exceed you do not create department more than five level ',[], config('httpcodes.bad_request'));

            }
	        $department = new Department;
		 	$department->user_id = $user->id;
            $department->branch_id = getBranchId();
		 	$department->parent_id = $request->parent_id;
            $department->name = $request->name;
            $department->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$department->save();
              DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Department','create') ,$department, config('httpcodes.success'));
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function checkLevel($parent_id){
        $firstLevel =  Department::where('parent_id',$parent_id)->get();
            $level = 1;
            foreach ($firstLevel as $key => $level1) {
                if (count($level1->children)>0) {
                    $level = $level+1 ;
                    $secondLevel =  Department::where('parent_id',$level1->id)->get();
                    foreach ($secondLevel as $key => $level2) {
                       if (count($level2->children)>0) {
                        $level = $level+1 ;
                        $thirdLevel =  Department::where('parent_id',$level2->id)->get();
                        foreach ($thirdLevel as $key => $level3) {
                           if (count($level3->children)>0) {
                            $level = $level+1 ;
                            $fourthLevel =  Department::where('parent_id',$level3->id)->get();
                            foreach ($fourthLevel as $key => $level4) {
                               if (count($level4->children)>0) {
                                $level = $level+1 ;
                                $fiveLevel =  Department::where('parent_id',$level4->id)->get();
                                foreach ($fiveLevel as $key => $level5) {
                                   if (count($level5->children)>0) {
                                    //$level = $level+1 ;
                                    
                                   }
                                }

                               }
                            }

                           }
                        }

                       }
                    }

                }
            }
        return $level;
    }
    public function show($id){
        
        try {
            $user = getUser();
            $checkId= Department::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Department','id_not_found'), [],config('httpcodes.not_found'));
            }
            
            $department = Department::where('id',$id)->with('User:id,name')->first();
            return prepareResult(true,'View Department',$department, config('httpcodes.success'));
                 
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
	           	'name' => 'required',   
			
	        ],
	    	[
            'name.required' => getLangByLabelGroups('Department','name'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkId = Department::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Department','id_not_found'), [],config('httpcodes.not_found'));
            }
            $checkAlready = Department::where('id','!=',$id)->where('name',$request->name)->first(); 
        	if($checkAlready) {

              	return prepareResult(false,getLangByLabelGroups('Department','name_already_exists'),[], config('httpcodes.bad_request')); 

        	}

            $topParent = findTopParentId($request->parent_id);
            $level = $this->checkLevel($topParent);
            if(!empty($request->parent_id) && $level == '5'){
                return prepareResult(false,'Child level exceed you do not create department more than five level ',[], config('httpcodes.bad_request'));

            }
	        $department = Department::find($id);
		 	$department->name = $request->name;
            $department->parent_id = $request->parent_id;
            $department->status = ($request->name) ? $request->status:'1';
            $department->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$department->save();
              DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Department','update'),$department, config('httpcodes.success'));
			    
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= Department::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Department','id_not_found'), [],config('httpcodes.not_found'));
            }
            
        	$department = Department::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Department','delete'),[], config('httpcodes.success'));
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('user_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "user_id = "."'" .$request->input('user_id')."'".")";
        }
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        if (is_null($request->input('name')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "name like '%" .trim(strtolower($request->input('name'))) . "%')";
        }

        return($w);

    }
    
}
