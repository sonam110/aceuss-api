<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\OvHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;

class OvHourController extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware('permission:OvHour-browse',['except' => ['show']]);
    //     $this->middleware('permission:OvHour-add', ['only' => ['store']]);
    //     $this->middleware('permission:OvHour-edit', ['only' => ['update']]);
    //     $this->middleware('permission:OvHour-read', ['only' => ['show']]);
    //     $this->middleware('permission:OvHour-delete', ['only' => ['destroy']]);
        
    // }

    public function OvHours(Request $request)
    {
        try {

            $query = OvHour::orderBy('id', 'DESC');
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
                return prepareResult(true,"OvHour list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"OvHour list",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if(empty($request->date))
        {
            $validation = \Validator::make($request->all(), [
                'start_time'      => 'required',
                'end_time'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        }
        elseif(empty($request->start_time))
        {
            $validation = \Validator::make($request->all(), [
                'date'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        }
        

        DB::beginTransaction();
        try {
            $OvHour = new OvHour;
            $OvHour->date = $request->date;
            $OvHour->start_time = $request->start_time;
            $OvHour->end_time = $request->end_time;
            $OvHour->entry_mode = $request->entry_mode;
            $OvHour->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('OvHour','message_create') ,$OvHour, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OvHour  $OvHour
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try 
        {
            $checkId= OvHour::find($id);
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('OvHour','message_id_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,'View OvHour' ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OvHour  $OvHour
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request,$id)
    {
        if(empty($request->date))
        {
            $validation = \Validator::make($request->all(), [
                'start_time'      => 'required',
                'end_time'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        }
        elseif(empty($request->start_time))
        {
            $validation = \Validator::make($request->all(), [
                'date'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
           }
       }
          

        DB::beginTransaction();
        try {
            $OvHour = OvHour::find($id);
            $OvHour->date = $request->date;
            $OvHour->start_time = $request->start_time;
            $OvHour->end_time = $request->end_time;
            $OvHour->entry_mode = $request->entry_mode;
            $OvHour->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('OvHour','message_create') ,$OvHour, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\OvHour $OvHour
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */

    public function destroy($id)
    {
        try 
        {
            $checkId= OvHour::find($id);
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('OvHour','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            if(auth()->user()->user_type_id=='1')
            {
                OvHour::where('id',$id)->delete();
                return prepareResult(true,getLangByLabelGroups('OvHour','message_delete') ,[], config('httpcodes.success'));
            }
           return prepareResult(false, 'Record Not Found', [],config('httpcodes.not_found'));
            
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
