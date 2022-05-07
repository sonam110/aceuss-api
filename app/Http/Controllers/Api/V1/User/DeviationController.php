<?php

namespace App\Http\Controllers\Api\V1\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deviation;
use Validator;
use Auth;
use DB;
use Exception;
use PDF;
use Str;

class DeviationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:deviation-browse',['except' => ['show']]);
        $this->middleware('permission:deviation-add', ['only' => ['store']]);
        $this->middleware('permission:deviation-edit', ['only' => ['update']]);
        $this->middleware('permission:deviation-read', ['only' => ['show']]);
        $this->middleware('permission:deviation-delete', ['only' => ['destroy']]);
        //$this->middleware('permission:deviation-print', ['only' => ['printDeviation']]);
        
    }

	public function deviations(Request $request)
    {
        try {
	        $user = getUser();
            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = Deviation::with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name','Employee:id,name','completedBy:id,name','branch');

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

            if(!empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereDate('date_time', '>=', $request->from_date)->whereDate('date_time', '<=', $request->end_date);
            }
            elseif(!empty($request->from_date) && empty($request->end_date))
            {
                $query->whereDate('date_time', $request->from_date);
            }
            elseif(empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereDate('date_time', '<=', $request->end_date);
            }

            if(!empty($request->critical_range))
            {
                $query->where('critical_range', $request->critical_range);
            }

            if(!empty($request->is_secret))
            {
                $query->where('is_secret', $request->is_secret);
            }

            if(!empty($request->is_signed))
            {
                $query->where('is_signed', $request->is_signed);
            }

            if(!empty($request->is_completed))
            {
                $query->where('is_completed', $request->is_completed);
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
                return prepareResult(true,"Deviation list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Deviation list",$query,config('httpcodes.success'));
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    	
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[  
                'category_id' => 'required|exists:category_masters,id',  
                'subcategory_id' => 'required|exists:category_masters,id',  
        		'date_time' => 'required',  
                'description' => 'required',       
        		'immediate_action' => 'required',       
	        ],
            [  
                'category_id' =>  getLangByLabelGroups('Deviation','category_id'),   
                'sub_category_id' =>  getLangByLabelGroups('Deviation','sub_category_id'),   
                'date_time' =>  getLangByLabelGroups('Deviation','date_time'),     
                'description' =>  getLangByLabelGroups('Deviation','description'),     
                'immediate_action' =>  getLangByLabelGroups('Deviation','immediate_action'),     
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
        	
	        $deviation = new Deviation;
		 	$deviation->activity_id = $request->activity_id;
            $deviation->branch_id = $request->branch_id;
		 	$deviation->patient_id = $request->patient_id;
		 	$deviation->emp_id = auth()->id();
		 	$deviation->category_id = $request->category_id;
		 	$deviation->subcategory_id = $request->subcategory_id;
            $deviation->date_time = $request->date_time;
            $deviation->description = $request->description;
            $deviation->immediate_action = $request->immediate_action;
            $deviation->probable_cause_of_the_incident = $request->probable_cause_of_the_incident;
            $deviation->suggestion_to_prevent_event_again = $request->suggestion_to_prevent_event_again;
            $deviation->related_factor = $request->related_factor;
            $deviation->critical_range = $request->critical_range;
            $deviation->further_investigation = !empty($request->further_investigation) ? json_encode($request->further_investigation, JSON_UNESCAPED_UNICODE) : null;
            $deviation->is_secret = $request->is_secret;
            $deviation->is_signed = $request->is_signed;
            $deviation->is_completed = $request->is_completed;
            if($request->is_completed==1)
            {
                $deviation->completed_date = date('Y-m-d');
            }		 	

            $deviation->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$deviation->save();
             DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Deviation','create') ,$deviation, config('httpcodes.success'));
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function show($id)
    {
        try {
            $user = getUser();
            $checkId= Deviation::where('id',$id)
                ->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],config('httpcodes.not_found'));
            }

            $deviation = Deviation::where('id', $id)->with('Activity:id,title','Category:id,name','Subcategory:id,name','EditedBy:id,name','Patient:id,name,personal_number,email,contact_number,patient_type_id','Employee:id,name','completedBy:id,name','branch')->first();
            return prepareResult(true,'View Deviation' ,$deviation, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[  
                'category_id' => 'required|exists:category_masters,id',  
                'subcategory_id' => 'required|exists:category_masters,id',  
                'date_time' => 'required',  
                'description' => 'required',       
                'immediate_action' => 'required',       
            ],
            [  
                'category_id' =>  getLangByLabelGroups('Deviation','category_id'),   
                'sub_category_id' =>  getLangByLabelGroups('Deviation','sub_category_id'),   
                'date_time' =>  getLangByLabelGroups('Deviation','date_time'),     
                'description' =>  getLangByLabelGroups('Deviation','description'),     
                'immediate_action' =>  getLangByLabelGroups('Deviation','immediate_action'),     
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }

            $deviation = Deviation::where('id',$id)
                ->first();
            if (!is_object($deviation)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],config('httpcodes.not_found'));
            }

            $deviation->activity_id = $request->activity_id;
            $deviation->branch_id = $request->branch_id;
            $deviation->patient_id = $request->patient_id;
            $deviation->category_id = $request->category_id;
            $deviation->subcategory_id = $request->subcategory_id;
            $deviation->date_time = $request->date_time;
            $deviation->description = $request->description;
            $deviation->immediate_action = $request->immediate_action;
            $deviation->probable_cause_of_the_incident = $request->probable_cause_of_the_incident;
            $deviation->suggestion_to_prevent_event_again = $request->suggestion_to_prevent_event_again;
            $deviation->related_factor = $request->related_factor;
            $deviation->critical_range = $request->critical_range;
            $deviation->further_investigation = !empty($request->further_investigation) ? json_encode($request->further_investigation, JSON_UNESCAPED_UNICODE) : null;
            $deviation->is_secret = $request->is_secret;
            $deviation->is_signed = $request->is_signed;
            $deviation->is_completed = $request->is_completed;
            if($request->is_completed==1)
            {
                $deviation->completed_date = date('Y-m-d');
            }           
            $deviation->edited_by = auth()->id();
            $deviation->edited_date = date('Y-m-d');
            $deviation->reason_for_editing = $request->reason_for_editing;

            $deviation->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $deviation->save();

		    DB::commit();
	        return prepareResult(true,getLangByLabelGroups('Deviation','update') ,$deviation, config('httpcodes.success'));
			  
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        try {
	    	$user = getUser();
        	$checkId= Deviation::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],config('httpcodes.not_found'));
            }
        	Deviation::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('Deviation','delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }

    public function actionDeviation(Request $request)
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'deviation_ids' => 'required|array|min:1',   
	        ],
            [
                'deviation_ids' =>  getLangByLabelGroups('Journal','id'),   
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}

            $deviation = Deviation::whereIn('id', $request->deviation_ids)->update([
                'is_signed' => $request->is_signed,
                'is_completed' => $request->is_completed,
                'completed_by' => auth()->id(),
                'completed_date' => date('Y-m-d')
            ]);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Deviation','approve') ,$deviation, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
    public function printDeviation(Request $request, $id)
    {
        try {
            $user = getUser();
            $checkId = Deviation::where('id',$id)
                ->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Deviation','id_not_found'), [],config('httpcodes.not_found'));
            }

            $deviation = Deviation::where('id', $id)->first();
            $filename = $id."-".time().".pdf";
            $data['deviation'] = $deviation;
            $pdf = PDF::loadView('print-deviation', $data);
            $pdf->save('reports/deviations/'.$filename);
            $url = env('CDN_DOC_URL').'reports/deviations/'.$filename;
            return prepareResult(true,'Print Deviation' ,$url, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
