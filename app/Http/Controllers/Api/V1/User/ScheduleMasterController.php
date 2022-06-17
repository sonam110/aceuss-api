<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScheduleMaster;
use Validator;
use Auth;
use DB;
use App\Models\ScheduleMasterLog;
use App\Models\User;
use Exception;
use App\Models\Activity;
use PDF;
use App\Models\EmailTemplate;
use Str;

class ScheduleMasterController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:schedule-master-browse',['except' => ['show']]);
    //     $this->middleware('permission:schedule-master-add', ['only' => ['store']]);
    //     $this->middleware('permission:schedule-master-edit', ['only' => ['update']]);
    //     $this->middleware('permission:schedule-master-read', ['only' => ['show']]);
    //     $this->middleware('permission:schedule-master-delete', ['only' => ['destroy']]);
    // }
	

    public function scheduleMasters(Request $request)
    {
        try 
        {
            $user = getUser();
            $query = ScheduleMaster::orderBy('id', 'DESC');
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
                    'last_page' => ceil($total / $perPage),
                ];
                return prepareResult(true,"ScheduleMaster list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"ScheduleMaster list",$query,config('httpcodes.success'));
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
                'shifts' => 'required',  
        		'title' => 'required', 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
	        $scheduleMaster = new ScheduleMaster;
		 	$scheduleMaster->title = $request->title;
		 	$scheduleMaster->shifts = json_encode($request->shifts);
            $scheduleMaster->from_date = $request->from_date;
            $scheduleMaster->to_date = $request->to_date;
            $scheduleMaster->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$scheduleMaster->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('ScheduleMaster','message_create') ,$scheduleMaster, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request,$id){
        DB::beginTransaction();
        try 
        {
	    	$validator = Validator::make($request->all(),[   
                'shifts' => 'required',  
                'title' => 'required', 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
        	$checkId = ScheduleMaster::where('id',$id) ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('ScheduleMaster','message_id_not_found'), [],config('httpcodes.not_found'));
            }
        	$scheduleMaster = ScheduleMaster::where('id',$id)->first();
            $scheduleMaster->title = $request->title;
            $scheduleMaster->shifts = json_encode($request->shifts);
            $scheduleMaster->from_date = $request->from_date;
            $scheduleMaster->to_date = $request->to_date;
            $scheduleMaster->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$scheduleMaster->save();
		    DB::commit();
	        return prepareResult(true,getLangByLabelGroups('ScheduleMaster','message_update') ,$scheduleMaster, config('httpcodes.success'));
		}
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function destroy($id)
    {
        try 
        {
        	$checkId= ScheduleMaster::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('ScheduleMaster','message_id_not_found'), [],config('httpcodes.not_found'));
            }
        	$checkId->delete();
         	return prepareResult(true,getLangByLabelGroups('ScheduleMaster','message_delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }
    
    public function show($id)
    {
        try 
        {
        	$checkId= ScheduleMaster::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('ScheduleMaster','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            return prepareResult(true,'View ScheduleMaster' ,$checkId, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
