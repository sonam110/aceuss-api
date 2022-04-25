<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityClassification;
use Validator;
use Auth;
use Exception;
use DB;

class ActivityClsController extends Controller
{
    public function activitycls(Request $request)
    {
        try {
            
            $query = ActivityClassification::orderBy('id', 'DESC');
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
                return prepareResult(true,"Activity Classification list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Activity Classification list",$query,config('httpcodes.success'));
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
                'name' => 'required',   
            ],
            [
            'name.required' => getLangByLabelGroups('Activity','name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkAlready = ActivityClassification::where('name',$request->name)->first(); 
            if($checkAlready) {
                return prepareResult(false,getLangByLabelGroups('Activity','name_already_exists'),[], config('httpcodes.bad_request')); 
            }
            $activityClassification = new ActivityClassification;
            $activityClassification->name = $request->name;
            $activityClassification->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $activityClassification->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Activity','create') ,$activityClassification, config('httpcodes.success'));
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
            $checkId= ActivityClassification::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $activityClassification = ActivityClassification::where('id',$id)->first();
            return prepareResult(true,'View Activity Classification',$activityClassification, config('httpcodes.success'));
                
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
                'name' => 'required',   
            
            ],
            [
            'name.required' => getLangByLabelGroups('Activity','name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkId = ActivityClassification::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $checkAlready = ActivityClassification::where('id','!=',$id)->where('name',$request->name)->first(); 
            if($checkAlready) {

                return prepareResult(false,getLangByLabelGroups('Activity','name_already_exists'),[], config('httpcodes.bad_request')); 

            }
            $activityClassification = ActivityClassification::find($id);
            $activityClassification->name = $request->name;
            $activityClassification->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $activityClassification->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Activity','update'),$activityClassification, config('httpcodes.success'));
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
            $checkId= ActivityClassification::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],config('httpcodes.not_found'));
            }
            $activityClassification = ActivityClassification::findOrFail($id);
            $activityClassification->delete();
            return prepareResult(true,getLangByLabelGroups('Activity','delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
