<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journal;
use Validator;
use Auth;
use DB;
use App\Models\JournalLog;
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
            $query = Journal::with('Parent:id','Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name');
            

            if($user->user_type_id =='2'){
                
                $query = $query->orderBy('id','DESC');
            } else{
                $query =  $query->whereIn('id',$allChilds);
            }
            $whereRaw = $this->getWhereRawFromRequest($request);

            return $whereRaw;
            if($whereRaw != '') { 
                $query =  $query->whereRaw($whereRaw)
                 ->orderBy('id', 'DESC');
                
               
            } else {
                $query = $query->orderBy('id', 'DESC');
                
            }

            // if(!empty($request->type)){
            //     $query = $query->orderBy('id','DESC');
            // }

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
        		// 'activity_id' => 'required|exists:activities,id',   
        		'category_id' => 'required|exists:category_masters,id', 
                'subcategory_id' => 'required|exists:category_masters,id',   
        		// 'title' => 'required',   
        		'description' => 'required',       
	        ],
            [
                // 'activity_id' =>  getLangByLabelGroups('Journal','activity_id'),   
                'category_id' =>  getLangByLabelGroups('Journal','category_id'), 
                'subcategory_id' =>  getLangByLabelGroups('Journal','subcategory_id'),   
                // 'title' =>  getLangByLabelGroups('Journal','title'),   
                'description' =>  getLangByLabelGroups('Journal','description'), 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
	        $journal = new Journal;
		 	$journal->activity_id = $request->activity_id;
		 	$journal->branch_id = getBranchId();
            $journal->deviation_id = $request->deviation_id;
		 	// $journal->ip_id = $request->ip_id;
		 	$journal->patient_id = $request->patient_id;
		 	$journal->emp_id = $request->emp_id;
		 	$journal->category_id = $request->category_id;
		 	$journal->subcategory_id = $request->subcategory_id;
		 	$journal->description = $request->description;
		 	$journal->is_deviation = ($request->is_deviation)? $request->is_deviation :0;
            $journal->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $journal->is_signed = ($request->is_signed)? $request->is_signed :0;
            $journal->date = ($request->date)? $request->date :date('Y/m/d');
            $journal->time = ($request->time)? $request->time :date('h:i:sa');
            $journal->type = $request->type;
		 	$journal->save();
             DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Journal','create') ,$journal, config('httpcodes.success'));
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
                // 'activity_id' => 'required|exists:activities,id',   
        		'category_id' => 'required|exists:category_masters,id',   
        		// 'title' => 'required',   
        		'description' => 'required',    
	        ],
            [   
                // 'activity_id' =>  getLangByLabelGroups('Journal','activity_id'),   
                'category_id' =>  getLangByLabelGroups('Journal','category_id'),   
                // 'title' =>  getLangByLabelGroups('Journal','title'),   
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



            if($checkId->is_signed == 1){
                $journalLog                     = new JournalLog;
                $journalLog->journal_id         = $checkId->journal_id;
                $journalLog->parent_id          = $checkId->parent_id;
                $journalLog->activity_id        = $checkId->activity_id;
                $journalLog->branch_id          = $checkId->branch_id;
                $journalLog->deviation_id       = $checkId->deviation_id;
                $journalLog->patient_id         = $checkId->patient_id;
                $journalLog->emp_id             = $checkId->emp_id;
                $journalLog->category_id        = $checkId->category_id;
                $journalLog->subcategory_id     = $checkId->subcategory_id;
                $journalLog->description        = $checkId->description;
                $journalLog->edited_by          = $request->edited_by;
                $journalLog->reason_for_editing = $request->reason_for_editing;
                $journalLog->date                  = $checkId->date;
                $journalLog->time                  = $checkId->time;
                $journalLog->type                  = $checkId->type;
                $journalLog->save();
            }



        	$parent_id  = (is_null($checkId->parent_id)) ? $id : $checkId->parent_id;
        	$journal = Journal::where('id',$id)->first();
	       	$journal->parent_id = $parent_id;
		 	$journal->activity_id = $request->activity_id;
            $journal->branch_id = getBranchId();
		 	$journal->deviation_id = $request->deviation_id;
		 	$journal->patient_id = $request->patient_id;
		 	$journal->emp_id = $request->emp_id;
		 	$journal->category_id = $request->category_id;
		 	$journal->subcategory_id = $request->subcategory_id;
		 	$journal->description = $request->description;
		 	$journal->is_deviation = ($request->is_deviation)? $request->is_deviation :0;
		 	$journal->edited_by = $user->id;
		 	$journal->reason_for_editing = $request->reason_for_editing;
            $journal->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $journal->is_signed = ($request->is_signed)? $request->is_signed :0;
            $journal->date = ($request->date)? $request->date :date('Y/m/d');
            $journal->time = ($request->time)? $request->time :date('h:i:sa');
            $journal->type = $request->type;
		 	$journal->save();
		       DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Journal','update') ,$journal, config('httpcodes.success'));
			  
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
        	$journal = Journal::where('id',$id)->delete();
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
            $journal = Journal::find($id);
		 	$journal->approved_by = $user->id;
		 	$journal->approved_date = date('Y-m-d');
		 	$journal->status = '1';
		 	$journal->save();
	        return prepareResult(true,getLangByLabelGroups('Journal','delete'),$journal, config('httpcodes.success'));
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

        	$journal = Journal::where('id',$id)->with('Parent:id,title','Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name','children')->first();
	        return prepareResult(true,'View Patient plan' ,$journal, config('httpcodes.success'));
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
