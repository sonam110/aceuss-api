<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JournalAction;
use Validator;
use Auth;
use DB;
use App\Models\JournalActionLog;
use App\Models\Journal;
use Exception;
class JournalActionController extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware('permission:journal-action-browse',['except' => ['show']]);
    //     $this->middleware('permission:journal-action-add', ['only' => ['store']]);
    //     $this->middleware('permission:journal-action-edit', ['only' => ['update']]);
    //     $this->middleware('permission:journal-action-read', ['only' => ['show']]);
    //     $this->middleware('permission:journal-action-delete', ['only' => ['destroy']]);
        
    // }
    public function journalActions(Request $request)
    {
      
        try {
            $user = getUser();
            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = JournalAction::with('journal','journalActionLogs');
            

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

            if(!empty($request->journal_id))
            {
                $query = $query->where('journal_id', $request->journal_id);
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
                'journal_id' => 'required|exists:journals,id',    
                'comment_result' => 'required',   
                'comment_action' => 'required',       
            ],
            [
                'journal_id' =>  getLangByLabelGroups('JournalAction','journal_id'),   
                'comment_result' =>  getLangByLabelGroups('JournalAction','comment_result'),   
                'comment_action' =>  getLangByLabelGroups('JournalAction','comment_action'), 
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            
            $journalAction = new JournalAction;
            $journalAction->journal_id          = $request->journal_id;
            $journalAction->comment_action      = $request->comment_action;
            $journalAction->comment_result      = $request->comment_result;
            $journalAction->edit_date           = date('Y-m-d H:i:s');
            $journalAction->is_signed           = ($request->is_signed)? $request->is_signed :0;
            $journalAction->save();
            
            DB::commit();

            $data = JournalAction::with('journal','journalActionLogs')
                ->where('id', $journalAction->id)
                ->first();
            return prepareResult(true,getLangByLabelGroups('JournalAction','create') ,$data, config('httpcodes.success'));
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
                'journal_id' => 'required|exists:journals,id',    
                'comment_result' => 'required',   
                'comment_action' => 'required',       
            ],
            [
                'journal_id' =>  getLangByLabelGroups('JournalAction','journal_id'),   
                'comment_result' =>  getLangByLabelGroups('JournalAction','comment_result'),   
                'comment_action' =>  getLangByLabelGroups('JournalAction','comment_action'), 
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            
            $checkId = JournalAction::where('id',$id)
                ->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('JournalAction','id_not_found'), [],config('httpcodes.not_found'));
            }
            
            if($checkId->is_signed==1)
            {
                $journalActionLog = new JournalActionLog;
                $journalActionLog->journal_action_id = $checkId->id;
                $journalActionLog->comment_action = $checkId->comment_action;
                $journalActionLog->comment_result = $checkId->comment_result;
                $journalActionLog->reason_for_editing = $request->reason_for_editing;
                $journalActionLog->edited_by = $checkId->edited_by;
                $journalActionLog->comment_created_at = $checkId->edit_date;
                $journalActionLog->save();
            }



            $parent_id  = (is_null($checkId->parent_id)) ? $id : $checkId->parent_id;
            $journalAction                      = JournalAction::where('id',$id)->first();
            $journalAction->comment_result      = $request->comment_result;
            $journalAction->comment_action      = $request->comment_action;
            $journalAction->is_signed           = ($request->is_signed)? $request->is_signed :0;
            $journalAction->edited_by           = $user->id;
            $journalAction->edit_date           = date('Y-m-d H:i:s');
            $journalAction->reason_for_editing  = $request->reason_for_editing;
            $journalAction->save();
            DB::commit();

            $data = JournalAction::with('journal','journalActionLogs')
                ->where('id', $journalAction->id)
                ->first();
            return prepareResult(true,getLangByLabelGroups('JournalAction','update') ,$data, config('httpcodes.success'));
              
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
            $checkId= JournalAction::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('JournalAction','id_not_found'), [],config('httpcodes.not_found'));
            }
            $journalAction = JournalAction::where('id',$id)->delete();
            return prepareResult(true,getLangByLabelGroups('JournalAction','delete') ,[], config('httpcodes.success'));
                
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id){
        try {
            $user = getUser();
            $checkId= JournalAction::where('id',$id)->with('journal','journalActionLogs')
                ->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('JournalAction','id_not_found'), [],config('httpcodes.not_found'));
            }

            $data = JournalAction::with('journal','journalActionLogs')
                ->where('id', $id)
                ->first();
            return prepareResult(true,'View Journal Action' ,$data, config('httpcodes.success'));
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

    public function actionJournalAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'journal_action_ids' => 'required|array|min:1',   
            ],
            [
                'journal_action_ids' =>  getLangByLabelGroups('JournalAction','id'),   
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $journalAction = Journal::whereIn('id', $request->journal_action_ids)->update([
                'is_signed' => $request->is_signed,
                'signed_by' => auth()->id(),
                'signed_date' => date('Y-m-d')
            ]);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('JournalAction','approve') ,$journalAction, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
}
