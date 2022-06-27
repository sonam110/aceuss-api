<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScheduleTemplate;
use App\Models\Schedule;
use Validator;
use Auth;
use DB;
use App\Models\User;
use Exception;
use App\Models\Activity;
use PDF;
use App\Models\EmailTemplate;
use Str;

class ScheduleTemplateController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:schedule-template-browse',['except' => ['show']]);
    //     $this->middleware('permission:schedule-template-add', ['only' => ['store']]);
    //     $this->middleware('permission:schedule-template-edit', ['only' => ['update']]);
    //     $this->middleware('permission:schedule-template-read', ['only' => ['show']]);
    //     $this->middleware('permission:schedule-template-delete', ['only' => ['destroy']]);
    // }
	

    public function scheduleTemplates(Request $request)
    {
        try 
        {
            $user = getUser();
            $query = ScheduleTemplate::orderBy('id', 'DESC');
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
                return prepareResult(true,"ScheduleTemplate list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"ScheduleTemplate list",$query,config('httpcodes.success'));
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
        		'title' => 'required', 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
	        $scheduleTemplate = new ScheduleTemplate;
		 	$scheduleTemplate->title = $request->title;
            $scheduleTemplate->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $scheduleTemplate->status =  $request->status;
		 	$scheduleTemplate->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_create') ,$scheduleTemplate, config('httpcodes.success'));
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
                'title' => 'required', 
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	
        	$checkId = ScheduleTemplate::where('id',$id) ->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_id_not_found'), [],config('httpcodes.not_found'));
            }
        	$scheduleTemplate = ScheduleTemplate::where('id',$id)->first();
            $scheduleTemplate->title = $request->title;
            $scheduleTemplate->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $scheduleTemplate->status =  $request->status;
		 	$scheduleTemplate->save();
		    DB::commit();
	        return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_update') ,$scheduleTemplate, config('httpcodes.success'));
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
        	$checkId= ScheduleTemplate::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_id_not_found'), [],config('httpcodes.not_found'));
            }
        	$checkId->delete();
         	return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }
    
    public function show($id)
    {
        try 
        {
        	$checkId= ScheduleTemplate::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('ScheduleTemplate','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            return prepareResult(true,'View ScheduleTemplate' ,$checkId, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function changeStatus(Request $request,$id){
        DB::beginTransaction();
        try 
        {
            $validator = Validator::make($request->all(),[ 
                'status' => 'required', 
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $scheduleTemplate = ScheduleTemplate::find($id);
            $scheduleTemplate->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $scheduleTemplate->status = $request->status;
            $scheduleTemplate->save();

            $schedule = Schedule::where('schedule_template_id',$id)->update(['is_active' => $request->status]);
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('ScheduleTemplate','message_create') ,$scheduleTemplate, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
