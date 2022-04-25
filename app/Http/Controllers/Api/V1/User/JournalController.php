<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journal;
use Validator;
use Auth;
use DB;
use Exception;
class JournalController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:journal-browse',['except' => ['show']]);
        $this->middleware('permission:journal-add', ['only' => ['store']]);
        $this->middleware('permission:journal-edit', ['only' => ['update']]);
        $this->middleware('permission:journal-read', ['only' => ['show']]);
        $this->middleware('permission:journal-delete', ['only' => ['destroy']]);
        
    }
	public function journals(Request $request)
    {
      
        try {
	        $user = getUser();
	        $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = Journal::with('Parent:id,title','Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name')';'
            if($user->user_type_id =='2'){
                
                $query = $query->orderBy('id','DESC');
            } else{
                $query =  $query->whereIn('branch_id',$allChilds);
            }
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query =  $query->whereRaw($whereRaw)
                 ->orderBy('id', 'DESC');
                
               
            } else {
                $query = $query->orderBy('id', 'DESC');
                
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
                return prepareResult(true,"Journal list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Journal list",$query,config('httpcodes.success'));
	    
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
        		'activity_id' => 'required|exists:activities,id',   
        		'category_id' => 'required|exists:category_masters,id',   
        		'title' => 'required',   
        		'description' => 'required',       
	        ],
            [
                'activity_id' =>  getLangByLabelGroups('Journal','activity_id'),   
                'category_id' =>  getLangByLabelGroups('Journal','category_id'),   
                'title' =>  getLangByLabelGroups('Journal','title'),   
                'description' =>  getLangByLabelGroups('Journal','description'), 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
	        $Journal = new Journal;
		 	$Journal->activity_id = $request->activity_id;
		 	$Journal->branch_id = getBranchId();
            $Journal->deviation_id = $request->deviation_id;
		 	$Journal->ip_id = $request->ip_id;
		 	$Journal->patient_id = $request->patient_id;
		 	$Journal->emp_id = $request->emp_id;
		 	$Journal->category_id = $request->category_id;
		 	$Journal->subcategory_id = $request->subcategory_id;
		 	$Journal->title = $request->title;
		 	$Journal->description = $request->description;
		 	$Journal->is_deviation = ($request->is_deviation)? $request->is_deviation :0;
            $Journal->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$Journal->save();
             DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Journal','create') ,$Journal, config('httpcodes.success'));
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
                'activity_id' => 'required|exists:activities,id',   
        		'category_id' => 'required|exists:category_masters,id',   
        		'title' => 'required',   
        		'description' => 'required',    
	        ],
            [   
                'activity_id' =>  getLangByLabelGroups('Journal','activity_id'),   
                'category_id' =>  getLangByLabelGroups('Journal','category_id'),   
                'title' =>  getLangByLabelGroups('Journal','title'),   
                'description' =>  getLangByLabelGroups('Journal','description'), 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
        	$checkId = Journal::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$parent_id  = (is_null($checkId->parent_id)) ? $id : $checkId->parent_id;
        	$Journal = new  Journal;
	       	$Journal->parent_id = $parent_id;
		 	$Journal->activity_id = $request->activity_id;
            $Journal->branch_id = getBranchId();
		 	$Journal->deviation_id = $request->deviation_id;
		 	$Journal->ip_id = $request->ip_id;
		 	$Journal->patient_id = $request->patient_id;
		 	$Journal->emp_id = $request->emp_id;
		 	$Journal->category_id = $request->category_id;
		 	$Journal->subcategory_id = $request->subcategory_id;
		 	$Journal->title = $request->title;
		 	$Journal->description = $request->description;
		 	$Journal->is_deviation = ($request->is_deviation)? $request->is_deviation :0;
		 	$Journal->edited_by = $user->id;
		 	$Journal->reason_for_editing = $request->reason_for_editing;
            $Journal->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$Journal->save();
		       DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Journal','create') ,$Journal, config('httpcodes.success'));
			  
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
        	$checkId= Journal::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$Journal = Journal::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Journal','delete') ,[], config('httpcodes.success'));
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    public function approvedJournal(Request $request){
    
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'id' => 'required',   
	        ],
            [
                'id' =>  getLangByLabelGroups('Journal','id'),   
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$id = $request->id;
        	$checkId= Journal::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','id_not_found'), [],config('httpcodes.not_found'));
            }
            $Journal = Journal::find($id);
		 	$Journal->approved_by = $user->id;
		 	$Journal->approved_date = date('Y-m-d');
		 	$Journal->status = '1';
		 	$Journal->save();
	        return prepareResult(true,getLangByLabelGroups('Journal','delete'),$Journal, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function show(Request $request){
        try {
	    	$user = getUser();
        	$checkId= Journal::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','id_not_found'), [],config('httpcodes.not_found'));
            }

        	$Journal = Journal::where('id',$id)->with('Parent:id,title','Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name','children')->first();
	        return prepareResult(true,'View Patient plan' ,$Journal, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
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
