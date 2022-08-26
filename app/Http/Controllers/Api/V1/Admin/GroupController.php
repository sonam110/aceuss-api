<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;

class GroupController extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware('permission:group-browse',['except' => ['show']]);
    //     $this->middleware('permission:group-add', ['only' => ['store']]);
    //     $this->middleware('permission:group-edit', ['only' => ['update']]);
    //     $this->middleware('permission:group-read', ['only' => ['show']]);
    //     $this->middleware('permission:group-delete', ['only' => ['destroy']]);
        
    // }

    public function groups(Request $request)
    {
        try {

            $query = Group::orderBy('id', 'DESC');
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
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
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
        $validation = \Validator::make($request->all(), [
            'name'      => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
            $group = new Group;
            $group->name = $request->name;
            $group->status = $request->status;
            $group->entry_mode = $request->entry_mode;
            $group->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$group, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try 
        {
            $checkId= Group::find($id);
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,getLangByLabelGroups('BcCommon','message_show') ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request,$id)
    {
         $validation = \Validator::make($request->all(), [
            'name'      => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
            $group = Group::find($id);
            $group->name = $request->name;
            $group->status = $request->status;
            $group->entry_mode = $request->entry_mode;
            $group->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_update') ,$group, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Group $group
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        try 
        {
            $checkId= Group::find($id);
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            if(auth()->user()->user_type_id=='1')
            {
                Group::where('id',$id)->delete();
                return prepareResult(true,getLangByLabelGroups('BcCommon','message_delete') ,[], config('httpcodes.success'));
            }
           return prepareResult(false, getLangByLabelGroups('BcCommon','message_unauthorized'), [],config('httpcodes.unauthorized'));
            
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
