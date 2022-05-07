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
            $query = Journal::with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','journalActions','JournalLogs')->withCount('journalActions');

            if($user->user_type_id=='2' || $user->user_type_id=='3' || $user->user_type_id=='4' || $user->user_type_id=='5' || $user->user_type_id=='11')
            {

            }
            else
            {
                $query = $query->where('is_secret', '!=', 1);
            }
            
            if($user->user_type_id !='2') {
                $query =  $query->whereIn('id',$allChilds);
            }

            if(!empty($request->with_or_without_activity))
            {
                if($request->with_or_without_activity=='yes')
                {
                    $query->whereNotNull('activity_id');
                }
                else
                {
                    $query->whereNull('activity_id');
                }
            }

            if(!empty($request->activity_id))
            {
                $query->where('activity_id', $request->activity_id);
            }

            if(!empty($request->branch_id))
            {
                $query->where('branch_id', $request->branch_id);
            }

            if(!empty($request->patient_id))
            {
                $query->where('patient_id', $request->patient_id);
            }

            if(!empty($request->emp_id))
            {
                $query->where('emp_id', $request->emp_id);
            }

            if(!empty($request->category_id))
            {
                $query->where('category_id', $request->category_id);
            }

            if(!empty($request->subcategory_id))
            {
                $query->where('subcategory_id', $request->subcategory_id);
            }

            if(!empty($request->is_secret))
            {
                $query->where('is_secret', $request->is_secret);
            }

            if(!empty($request->is_signed))
            {
                $query->where('is_signed', $request->is_signed);
            }

            if(!empty($request->data_of))
            {
                $date = date('Y-m-d',strtotime('-1'.$request->data_of.''));
                $query->where('created_at','>=', $date);
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
        		'category_id' => 'required|exists:category_masters,id',  
        		'description' => 'required',       
	        ],
            [   
                'category_id' =>  getLangByLabelGroups('Journal','category_id'), 
                'description' =>  getLangByLabelGroups('Journal','description'), 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
	        $journal = new Journal;
		 	$journal->activity_id = $request->activity_id;
		 	$journal->branch_id = getBranchId();
		 	$journal->patient_id = $request->patient_id;
		 	$journal->emp_id = $user->id;
		 	$journal->category_id = $request->category_id;
		 	$journal->subcategory_id = $request->subcategory_id;
            $journal->date = ($request->date)? $request->date :date('Y-m-d');
            $journal->time = ($request->time)? $request->time :date('h:i');
		 	$journal->description = $request->description;
            $journal->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $journal->is_signed = ($request->is_signed)? $request->is_signed :0;
            $journal->is_secret = ($request->is_secret)? $request->is_secret :0;
            $journal->is_active = ($request->is_active)? $request->is_active :0;
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
        		'category_id' => 'required|exists:category_masters,id',  
        		'description' => 'required',    
	        ],
            [  
                'category_id' =>  getLangByLabelGroups('Journal','category_id'), 
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
                $journalLog->description        = $checkId->description;
                $journalLog->edited_by          = $user->id;
                $journalLog->reason_for_editing = $request->reason_for_editing;
                $journalLog->save();
            }



        	$parent_id  = (is_null($checkId->parent_id)) ? $id : $checkId->parent_id;
        	$journal = Journal::where('id',$id)->first();
		 	$journal->activity_id = $request->activity_id;
            $journal->branch_id = getBranchId();
		 	$journal->patient_id = $request->patient_id;
		 	$journal->emp_id = $user->id;
		 	$journal->category_id = $request->category_id;
		 	$journal->subcategory_id = $request->subcategory_id;
		 	$journal->description = $request->description;
            $journal->date = ($request->date)? $request->date :date('Y-m-d');
            $journal->time = ($request->time)? $request->time :date('h:i');
		 	$journal->edited_by = $user->id;
		 	$journal->reason_for_editing = $request->reason_for_editing;
            $journal->edit_date = date('Y-m-d');
            $journal->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $journal->is_signed = ($request->is_signed)? $request->is_signed :0;
            $journal->is_secret = ($request->is_secret)? $request->is_secret :0;
            $journal->is_active = ($request->is_active)? $request->is_active :0;
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
		 	$journal->save();
	        return prepareResult(true,getLangByLabelGroups('Journal','delete'),$journal, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= Journal::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','id_not_found'), [],config('httpcodes.not_found'));
            }

        	$journal = Journal::where('id',$id)->with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','ApprovedBy:id,name','Patient:id,name','Employee:id,name','journalLogs')->first();
	        return prepareResult(true,'View Journal' ,$journal, config('httpcodes.success'));
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

    public function actionJournal(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'journal_ids' => 'required|array|min:1',   
            ],
            [
                'journal_ids' =>  getLangByLabelGroups('Journal','id'),   
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $journal = Journal::whereIn('id', $request->journal_ids)->update([
                'is_signed' => $request->is_signed,
                // 'is_approved' => $request->is_approved,
                'approved_by' => auth()->id(),
                'approved_date' => date('Y-m-d')
            ]);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Journal','approve') ,$journal, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
}
