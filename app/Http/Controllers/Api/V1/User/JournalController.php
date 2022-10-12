<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journal;
use Validator;
use Auth;
use DB;
use App\Models\JournalLog;
use App\Models\User;
use Exception;
use App\Models\Activity;
use PDF;
use App\Models\EmailTemplate;
use Str;

class JournalController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:journal-browse',['except' => ['show']]);
        $this->middleware('permission:journal-add', ['only' => ['store']]);
        $this->middleware('permission:journal-edit', ['only' => ['update']]);
        $this->middleware('permission:journal-read', ['only' => ['show']]);
        $this->middleware('permission:journal-delete', ['only' => ['destroy']]);
        //$this->middleware('permission:journal-print', ['only' => ['printJournal']]);
        
    }
	

    public function journals(Request $request)
    {
        try {
            $user = getUser();
            if(!empty($user->branch_id)) {
                $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
            
            $query = Journal::select('journals.*')
                ->with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','JournalLogs','journalActions.journalActionLogs.editedBy', 'branch:id,name,branch_name')
                ->withCount('journalActions')
                //->orderByRaw('date(date) DESC')
                //->orderByRaw('time(time) DESC');
                ->orderBy('created_at', 'DESC');
            if(in_array($user->user_type_id, [2,3,4,5,11]))
            {

            }
            else
            {
                $query->where('journals.is_secret', '!=', 1);
            }

            if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $query->where(function ($q) use ($user) {
                    $q->where('journals.patient_id', $user->id)
                        ->orWhere('journals.patient_id', $user->parent_id);
                });
            }
            
            if($user->user_type_id !='2') 
            {
                if($user->user_type_id =='3') 
                {
                    $user_records = getAllowUserList('visible-all-patients-journal');
                    $query->whereIn('journals.patient_id', $user_records);
                }
                else
                {
                    $query->whereIn('journals.branch_id',$allChilds);
                }
            }

            if(!empty($request->activity_id))
            {
                $query->where('journals.activity_id', $request->activity_id);
            }

            if(!empty($request->branch_id))
            {
                $query->where('journals.branch_id', $request->branch_id);
            }

            if(!empty($request->patient_id))
            {
                $query->where('journals.patient_id', $request->patient_id);
            }

            if(!empty($request->emp_id))
            {
                $query->where('journals.emp_id', $request->emp_id);
            }

            if(!empty($request->category_id))
            {
                $query->where('journals.category_id', $request->category_id);
            }

            if(!empty($request->subcategory_id))
            {
                $query->where('journals.subcategory_id', $request->subcategory_id);
            }

            if($request->is_secret=='yes')
            {
                $query->where('journals.is_secret', 1);
            }
            elseif($request->is_secret=='no')
            {
                $query->where('journals.is_secret', 0);
            }

            if($request->is_signed=='yes')
            {
                $query->where('journals.is_signed', 1);
            }
            elseif($request->is_signed=='no')
            {
                $query->where('journals.is_signed', 0);
            }

            if($request->is_active=='yes')
            {
                $query->where('journals.is_active', 1);
            }
            elseif($request->is_active=='no')
            {
                $query->where('journals.is_active', 0);
            }

            if($request->with_activity=='yes')
            {
                $query->whereNotNull('journals.activity_id');
            }
            elseif($request->with_activity=='no')
            {
                $query->whereNull('journals.activity_id');
            }

            if(!empty($request->data_of))
            {
                $date = date('Y-m-d',strtotime('-1'.$request->data_of.''));
                $query->where('journals.created_at','>=', $date);
            }
            if(!empty($request->perPage))
            {
                ////////Counts
                $journalCounts = Journal::select([
                    \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                    \DB::raw('COUNT(IF(is_active = 1, 0, NULL)) as total_active'),
                    \DB::raw('COUNT(IF(is_secret = 1, 0, NULL)) as total_secret'),
                    \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                    \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
                ]);
                if(in_array($user->user_type_id, [2,3,4,5,11]))
                {

                }
                else
                {
                    $journalCounts->where('is_secret', '!=', 1);
                }
                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $journalCounts->where(function ($q) use ($user) {
                        $q->where('journals.patient_id', $user->id)
                            ->orWhere('journals.patient_id', $user->parent_id);
                    });
                }
                
                if($user->user_type_id !='2') {
                    if($user->user_type_id =='3') 
                    {
                        $user_records = getAllowUserList('visible-all-patients-journal');
                        $journalCounts->whereIn('journals.patient_id', $user_records);
                    }
                    else
                    {
                        $journalCounts->whereIn('journals.branch_id',$allChilds);
                    }
                }

                if(!empty($request->activity_id))
                {
                    $journalCounts->where('journals.activity_id', $request->activity_id);
                }

                if(!empty($request->branch_id))
                {
                    $journalCounts->where('journals.branch_id', $request->branch_id);
                }

                if(!empty($request->patient_id))
                {
                    $journalCounts->where('journals.patient_id', $request->patient_id);
                }

                if(!empty($request->emp_id))
                {
                    $journalCounts->where('journals.emp_id', $request->emp_id);
                }

                if(!empty($request->category_id))
                {
                    $journalCounts->where('journals.category_id', $request->category_id);
                }

                if(!empty($request->subcategory_id))
                {
                    $journalCounts->where('journals.subcategory_id', $request->subcategory_id);
                }

                if(!empty($request->data_of))
                {
                    $date = date('Y-m-d',strtotime('-1'.$request->data_of.''));
                    $journalCounts->where('journals.created_at','>=', $date);
                }
                $journalCounts = $journalCounts->first();

                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage),
                    'total_signed' => $journalCounts->total_signed,
                    'total_active' => $journalCounts->total_active,
                    'total_secret' => $journalCounts->total_secret,
                    'total_with_activity' => $journalCounts->total_with_activity,
                    'total_without_activity' => $journalCounts->total_without_activity,
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,getLangByLabelGroups('Journal','message_list'),$query,config('httpcodes.success'));
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
                'category_id' => 'required|exists:category_masters,id',  
        		'subcategory_id' => 'required|exists:category_masters,id',  
        		// 'description' => 'required',       
	        ],
            [   
                'category_id' =>  getLangByLabelGroups('Journal','message_category_id'), 
                'subcategory_id' =>  getLangByLabelGroups('Journal','message_subcategory_id'), 
                // 'description' =>  getLangByLabelGroups('Journal','message_description'), 
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
            $journal->edit_date = date('Y-m-d H:i:s');
		 	$journal->save();

            /*-----------notify-user-journal-created--------------------*/
            $exra_params = ['patient_id'=>$journal->patient_id];
            $user = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number','branch_name')->where('id',getBranchId())->first();
            $data_id =  $journal->id;
            $notification_template = EmailTemplate::where('mail_sms_for', 'journal')->first();
            $variable_data = [
                '{{name}}'              => $user->name,
                '{{created_by}}'        => Auth::User()->name,
            ];
            actionNotification($user,$data_id,$notification_template,$variable_data,$exra_params);
            //-----------------------------------------------//

            DB::commit();

            $data = getJournal($journal->id);
	        return prepareResult(true,getLangByLabelGroups('Journal','message_create') ,$data, config('httpcodes.success'));
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
                'category_id' => 'required|exists:category_masters,id',  
                'subcategory_id' => 'required|exists:category_masters,id',  
                // 'description' => 'required',       
            ],
            [   
                'category_id' =>  getLangByLabelGroups('Journal','message_category_id'), 
                'subcategory_id' =>  getLangByLabelGroups('Journal','message_subcategory_id'), 
                // 'description' =>  getLangByLabelGroups('Journal','message_description'), 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
        	$checkId = Journal::where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','message_record_not_found'), [],config('httpcodes.not_found'));
            }

            if($checkId->is_signed == 1)
            {
                $journalLog                     = new JournalLog;
                $journalLog->journal_id         = $checkId->id;
                $journalLog->description        = $checkId->description;
                $journalLog->edited_by          = $user->id;
                $journalLog->reason_for_editing = $request->reason_for_editing;
                $journalLog->description_created_at =$checkId->edit_date;
                $journalLog->save();
            }

        	$parent_id  = (is_null($checkId->parent_id)) ? $id : $checkId->parent_id;
        	$journal = Journal::where('id',$id)->with('Category:id,name','Subcategory:id,name')->first();
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
            $journal->edit_date = date('Y-m-d H:i:s');
            $journal->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $journal->is_signed = ($request->is_signed)? $request->is_signed :0;
            $journal->is_secret = ($request->is_secret)? $request->is_secret :0;
            $journal->is_active = ($request->is_active)? $request->is_active :0;
		 	$journal->save();

		    DB::commit();
            
            $data = getJournal($journal->id);
	        return prepareResult(true,getLangByLabelGroups('Journal','message_update') ,$data, config('httpcodes.success'));
		}
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id){
  
        try {
	    	$user = getUser();
        	$checkId= Journal::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','message_record_not_found'), [],config('httpcodes.not_found'));
            }
        	$journal = Journal::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Journal','message_delete') ,[], config('httpcodes.success'));
		     	
			    
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id){
        try {
	    	$user = getUser();
        	$checkId= Journal::with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','JournalLogs','journalActions.journalActionLogs.editedBy', 'branch:id,name,branch_name')
                ->withCount('journalActions')
                ->where('id',$id)
                ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Journal','message_record_not_found'), [],config('httpcodes.not_found'));
            }

        	$data = getJournal($id);
            return prepareResult(true,getLangByLabelGroups('Journal','message_show') ,$data, config('httpcodes.success'));
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

    public function actionJournal(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'journal_ids' => 'required|array|min:1',   
            ],
            [
                'journal_ids' =>  getLangByLabelGroups('Journal','message_id'),   
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            if($request->signed_method=='bankid' && !empty(auth()->user()->personal_number))
            {
                $userInfo = getUser();
                $top_most_parent_id = $userInfo->top_most_parent_id;
                $response = bankIdVerification($userInfo->personal_number, $userInfo->id, $request->journal_ids[0], $userInfo->id, 'journal-approval', $top_most_parent_id, 2, 'journal sign');
                /*if($response['error']==1) 
                {
                    return prepareResult(false, $response,$response, config('httpcodes.internal_server_error'));
                }*/
                $url[] = $response;
                $url[0]['person_id'] = $userInfo->id;
                $url[0]['group_token'] = $request->journal_ids[0];
                $url[0]['uniqueId'] = $userInfo->unique_id;
                DB::commit();
                return prepareResult(true,'Mobile BankID Link', $url, config('httpcodes.success'));
            }
            else
            {
                $journal = Journal::whereIn('id', $request->journal_ids)->update([
                    'is_signed' => $request->is_signed,
                    'signed_by' => auth()->id(),
                    'signed_date' => date('Y-m-d')
                ]);
                DB::commit();
            }

            $data = getJournals($request->journal_ids);
            return prepareResult(true,getLangByLabelGroups('Journal','message_sign') ,$data, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            \Log::info($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function printJournal(Request $request)
    {
        try {
            $user = getUser();

            $journals = Journal::where('patient_id', $request->patient_id);
            if(!empty($request->journal_id))
            {
                $journals->where('id', $request->journal_id);
            }
            if(!empty($request->from_date) && !empty($request->end_date))
            {
                $journals->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->end_date);
            }
            elseif(!empty($request->from_date) && empty($request->end_date))
            {
                $journals->whereDate('created_at', '>=', $request->from_date);
            }
            elseif(empty($request->from_date) && !empty($request->end_date))
            {
                $journals->whereDate('created_at', '<=', $request->end_date);
            }
            if($request->print_with_secret!='yes')
            {
                $journals->where('is_secret', 0);
            }

            $journals = $journals->where('is_signed', 1)
                ->get();
            if (!is_object($journals)) {
                return prepareResult(false,getLangByLabelGroups('Patient','record_not_found'), [],config('httpcodes.not_found'));
            }
            $filename = $request->patient_id."-".time().".pdf";
            $data['journals'] = $journals;
            $data['print_reason'] = $request->reason;
            $pdf = PDF::loadView('print-journal', $data);
            $pdf->save('reports/journals/'.$filename);
            $url = env('CDN_DOC_URL').'reports/journals/'.$filename;
            return prepareResult(true,getLangByLabelGroups('Journal','message_print') ,$url, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function isActiveJournal(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'journal_id' => 'required',   
            'is_active' => 'required',   
        ]);
        if ($validator->fails()) {
            return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        }
        DB::beginTransaction();
        try {
            $user = getUser();

            $journal = Journal::where('id', $request->journal_id)->where('is_signed', 1)->update([
                'is_active' => $request->is_active
            ]);
            DB::commit();
            $data = getJournal($request->journal_id);
            return prepareResult(true,getLangByLabelGroups('Journal','message_active') ,$data, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
}
