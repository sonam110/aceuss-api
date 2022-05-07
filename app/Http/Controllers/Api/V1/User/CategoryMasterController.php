<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryMaster;
use Validator;
use Auth;
use Exception;
use DB;
class CategoryMasterController extends Controller
{
     public function __construct()
    {

        $this->middleware('permission:categories-browse',['except' => ['show']]);
        $this->middleware('permission:categories-add', ['only' => ['store']]);
        $this->middleware('permission:categories-edit', ['only' => ['update']]);
        $this->middleware('permission:categories-read', ['only' => ['show']]);
        $this->middleware('permission:categories-delete', ['only' => ['destroy']]);
        
       
    }
    
    public function categories(Request $request)
    {
        
        try {
	        $user = getUser();
	    	$whereRaw = $this->getWhereRawFromRequest($request);
	    	$categoryMaster =[]; 
            if($whereRaw != '') { 
    		    $query = CategoryMaster::whereRaw($whereRaw)
                    ->orderBy('id', 'DESC')
    		       	->with('Parent:id,name','CategoryType:id,name');
    			
            } else {
                $query = CategoryMaster::orderBy('id', 'DESC')
                    ->with('Parent:id,name','CategoryType:id,name');

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
                return prepareResult(true,"Category list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Category list",$query,config('httpcodes.success'));
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    	
    }
    public function categoryParentList(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            
            $query = CategoryMaster::select('id','name','category_type_id')
                ->whereNull('parent_id')
                ->where(function ($q) use ($request) {
                    $q->whereNull('top_most_parent_id')
                        ->orWhere('top_most_parent_id', 1)
                        ->orWhere('top_most_parent_id', auth()->user()->top_most_parent_id);
                })
            ->withoutGlobalScope('top_most_parent_id');

            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query->whereRaw($whereRaw)
                    ->orderBy('id', 'DESC');
            } else {
                $query->orderBy('id', 'DESC');
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
                return prepareResult(true,"Category Parent  list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Category Parent  list",$query,config('httpcodes.success'));
          
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }
    public function categoryChildList(Request $request)
    {
       
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'parent_id' => 'required',   
            ],
            [
            'parent_id.required' => 'Category parent id  is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $whereRaw = $this->getWhereRawFromRequest($request);
            $query = CategoryMaster::select('id','name')
                ->where('parent_id',$request->parent_id)
                ->where(function ($q) use ($request) {
                    $q->whereNull('top_most_parent_id')
                        ->orWhere('top_most_parent_id', 1)
                        ->orWhere('top_most_parent_id', auth()->user()->top_most_parent_id);
                })
            ->withoutGlobalScope('top_most_parent_id');
            if($whereRaw != '') { 
                $query->whereRaw($whereRaw)
                    ->orderBy('id', 'DESC');
            } else {
                $query->orderBy('id', 'DESC');
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
                return prepareResult(true,"Category Childs  list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Category Childs  list",$query,config('httpcodes.success'));
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
        		'category_type_id' => 'required',   
        		'name' => 'required',   
	        ],
		    [
            'category_type_id.required' => getLangByLabelGroups('CategoryMaster','category_type_id'),
            'name.required' => getLangByLabelGroups('CategoryMaster','name'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkAlready = CategoryMaster::where('category_type_id',$request->category_type_id)->where('name',$request->name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,getLangByLabelGroups('CategoryMaster','name_already_exists'),[], config('httpcodes.bad_request')); 
        	}
            if($request->parent_id) {
                $checkParent = CategoryMaster::whereNull('parent_id')->where('id',$request->parent_id)->first(); 
                if(!$checkParent) {
                    return prepareResult(false,getLangByLabelGroups('CategoryMaster','parent_id_not_found'),[], config('httpcodes.not_found')); 
                }
            }
            
	        $categoryMaster = new CategoryMaster;
		 	$categoryMaster->top_most_parent_id = $user->top_most_parent_id;
		 	$categoryMaster->created_by = $user->id;
		 	$categoryMaster->parent_id = ($request->parent_id) ? $request->parent_id :null;
		 	$categoryMaster->category_type_id = $request->category_type_id;
            $categoryMaster->name = $request->name;
            $categoryMaster->category_color = $request->category_color;
		 	$categoryMaster->follow_up_image = $request->follow_up_image;
		 	$categoryMaster->is_global = ($request->is_global) ? '1' :'0';
            $categoryMaster->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$categoryMaster->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('CategoryMaster','create') ,$categoryMaster, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[ 
        		'category_type_id' => 'required',   
        		'name' => 'required',   
	        ],
		    [
            'category_type_id.required' => getLangByLabelGroups('CategoryMaster','category_type_id'),
            'name.required' => getLangByLabelGroups('CategoryMaster','name'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkId = CategoryMaster::where('category_type_id',$request->category_type_id)->where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CategoryMaster','id_not_found'), [],config('httpcodes.not_found'));
            }
            $checkAlready = CategoryMaster::where('id','!=',$id)->where('name',$request->name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,getLangByLabelGroups('CategoryMaster','name_already_exists'),[], config('httpcodes.bad_request')); 
        	}
            $checkParent = CategoryMaster::whereNull('parent_id')->where('id',$request->parent_id)->first(); 
            if($request->parent_id) {
                $checkParent = CategoryMaster::whereNull('parent_id')->where('id',$request->parent_id)->first(); 
                if(!$checkParent) {
                    return prepareResult(false,getLangByLabelGroups('CategoryMaster','parent_id_not_found'),[], config('httpcodes.not_found')); 
                }
            }
	        $categoryMaster = CategoryMaster::find($id);
		 	$categoryMaster->parent_id = ($request->parent_id) ? $request->parent_id :null;
		 	$categoryMaster->name = $request->name;
            $categoryMaster->category_type_id = $request->category_type_id;
		 	$categoryMaster->category_color = $request->category_color;
		 	$categoryMaster->is_global = ($request->is_global) ? '1' :'0';
            $categoryMaster->status = ($request->status) ? $request->status :'1';
            $categoryMaster->follow_up_image = $request->follow_up_image;
            $categoryMaster->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$categoryMaster->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('CategoryMaster','update') ,$categoryMaster, config('httpcodes.success'));
			    
		       
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
        	$checkId= CategoryMaster::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CategoryMaster','id_not_found'), [],config('httpcodes.not_found'));
            }
            
        	$categoryMaster = CategoryMaster::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('CategoryMaster','delete') ,[], config('httpcodes.success'));
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    public function show($id){
        
        try {
            $user = getUser();
            $checkId= CategoryMaster::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CategoryMaster','id_not_found'), [],config('httpcodes.not_found'));
            }
            
            $categoryMaster = CategoryMaster::where('id',$id)->with('Parent:id,name','CategoryType:id,name','children')->first();
            return prepareResult(true,'Category view',$categoryMaster, config('httpcodes.success'));
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('category_type_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "category_type_id = "."'" .$request->input('category_type_id')."'".")";
        }
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('name')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "name like '%" .trim(strtolower($request->input('name'))) . "%')";
        }
        return($w);

    }
}
