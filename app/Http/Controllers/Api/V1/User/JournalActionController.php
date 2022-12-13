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
            $query = JournalAction::with('journal','journalActionLogs','editedBy:id,name','signedBy:id,name');
            $whereRaw = $this->getWhereRawFromRequest($request);

            if($whereRaw != '') { 
                $query =  $query->whereRaw($whereRaw)
                 ->orderBy('id', 'DESC');
            }
            $query = $query->orderBy('id', 'DESC');

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
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,getLangByLabelGroups('JournalAction','message_list'),$query,config('httpcodes.success'));
        
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'journal_id' => 'required|exists:journals,id',    
            ],
            [
                'journal_id.required' =>  getLangByLabelGroups('JournalAction','message_journal_id_required'),   
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            if(empty($request->comment_action) && empty($request->comment_result))
            {
                return prepareResult(false,getLangByLabelGroups('JournalAction','message_atleast_one_field_required'),[], config('httpcodes.bad_request')); 
            }
            
            $journalAction = new JournalAction;
            $journalAction->journal_id          = $request->journal_id;
            $journalAction->comment_action      = $request->comment_action;
            $journalAction->comment_result      = $request->comment_result;
            $journalAction->edit_date           = date('Y-m-d H:i:s');
            $journalAction->is_signed           = ($request->is_signed)? $request->is_signed :0;
            $journalAction->signed_method = ($request->signed_method)? $request->signed_method :NULL;
            $journalAction->signed_response = ($request->signed_response)? $request->signed_response :NULL;
            if($request->is_signed==1)
            {
                $journalAction->signed_by   = auth()->id();
                $journalAction->signed_date = date('Y-m-d H:i:s');
            }
            $journalAction->save();
            
            DB::commit();

            $data = JournalAction::with('journal','journalActionLogs','editedBy:id,name','signedBy:id,name')
                ->where('id', $journalAction->id)
                ->first();
            return prepareResult(true,getLangByLabelGroups('JournalAction','message_create') ,$data, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
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
            ],
            [
                'journal_id.required' =>  getLangByLabelGroups('JournalAction','message_journal_id_required'),    
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            if(empty($request->comment_action) && empty($request->comment_result))
            {
                return prepareResult(false,getLangByLabelGroups('JournalAction','message_atleast_one_field_required'),[], config('httpcodes.bad_request')); 
            }
            
            $checkId = JournalAction::where('id',$id)
                ->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('JournalAction','message_record_not_found'), [],config('httpcodes.not_found'));
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
            $journalAction->signed_method = ($request->signed_method)? $request->signed_method :NULL;
            $journalAction->signed_response = ($request->signed_response)? $request->signed_response :NULL;
            $journalAction->edited_by           = $user->id;
            $journalAction->edit_date           = date('Y-m-d H:i:s');
            if($request->is_signed==1)
            {
                $journalAction->signed_by   = auth()->id();
                $journalAction->signed_date = date('Y-m-d H:i:s');
            }
            
            $journalAction->reason_for_editing  = $request->reason_for_editing;
            $journalAction->save();
            DB::commit();

            $data = JournalAction::with('journal','journalActionLogs','editedBy:id,name','signedBy:id,name')
                ->where('id', $journalAction->id)
                ->first();
            return prepareResult(true,getLangByLabelGroups('JournalAction','message_update') ,$data, config('httpcodes.success'));
              
        }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        try {
            $user = getUser();
            $checkId= JournalAction::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('JournalAction','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $journalAction = JournalAction::where('id',$id)->delete();
            return prepareResult(true,getLangByLabelGroups('JournalAction','message_delete') ,[], config('httpcodes.success'));
                
                
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id)
    {
        try {
            $user = getUser();
            $checkId= JournalAction::where('id',$id)->with('journal','journalActionLogs','editedBy:id,name','signedBy:id,name')
                ->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('JournalAction','message_record_not_found'), [],config('httpcodes.not_found'));
            }

            $data = JournalAction::with('journal','journalActionLogs')
                ->where('id', $id)
                ->first();
            return prepareResult(true,getLangByLabelGroups('JournalAction','message_show') ,$data, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
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
                'journal_action_ids' =>  getLangByLabelGroups('JournalAction','message_id'),   
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            if($request->signed_method=='bankid' && !empty(auth()->user()->personal_number))
            {
                $userInfo = getUser();
                $top_most_parent_id = $userInfo->top_most_parent_id;
                $response = bankIdVerification($userInfo->personal_number, $userInfo->id, $request->journal_action_ids[0], $userInfo->id, 'journal-action-approval', $top_most_parent_id, 2, 'journal action sign');
                /*if($response['error']==1) 
                {
                    return prepareResult(false, $response,$response, config('httpcodes.internal_server_error'));
                }*/
                $url[] = $response;
                $url[0]['person_id'] = $userInfo->id;
                $url[0]['group_token'] = $request->journal_action_ids[0];
                $url[0]['uniqueId'] = $userInfo->unique_id;
                DB::commit();
                return prepareResult(true,'Mobile BankID Link', $url, config('httpcodes.success'));
            }
            else
            {
                $journalAction = JournalAction::whereIn('id', $request->journal_action_ids)->update([
                    'is_signed' => $request->is_signed,
                    'signed_by' => auth()->id(),
                    'signed_date' => date('Y-m-d H:i:s')
                ]);
                DB::commit();
            }
            return prepareResult(true,getLangByLabelGroups('JournalAction','message_sign') ,$journalAction, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
}
