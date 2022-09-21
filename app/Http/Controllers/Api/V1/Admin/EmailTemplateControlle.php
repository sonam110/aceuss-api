<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Validator;
use Auth;
use DB;

class EmailTemplateControlle extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:EmailTemplate-browse',['except' => ['show']]);
        $this->middleware('permission:EmailTemplate-add', ['only' => ['store']]);
        $this->middleware('permission:EmailTemplate-edit', ['only' => ['update']]);
        $this->middleware('permission:EmailTemplate-read', ['only' => ['show']]);
        $this->middleware('permission:EmailTemplate-delete', ['only' => ['destroy']]);
        
    }
    public function emailTemplates(Request $request)
    {
        try {

            $query = EmailTemplate::orderBy('id', 'DESC');
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
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    public function store(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'mail_sms_for'      => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
            $EmailTemplate = new EmailTemplate;
            $EmailTemplate->mail_sms_for = $request->mail_sms_for;
            $EmailTemplate->mail_subject = $request->mail_subject;
            $EmailTemplate->mail_body = $request->mail_body;
            $EmailTemplate->sms_body = $request->sms_body;
            $EmailTemplate->notify_body = $request->notify_body;
            $EmailTemplate->custom_attributes  = $request->custom_attributes;
            $EmailTemplate->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$EmailTemplate, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function show($id)
    {
        try {
            $checkId= EmailTemplate::where('id',$id)->withoutGlobalScope('top_most_parent_id')->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,getLangByLabelGroups('BcCommon','message_show') ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $EmailTemplate = EmailTemplate::find($id);
            $EmailTemplate->mail_subject = $request->mail_subject;
            $EmailTemplate->mail_body = $request->mail_body;
            $EmailTemplate->sms_body = $request->sms_body;
            $EmailTemplate->notify_body = $request->notify_body;
            $EmailTemplate->custom_attributes  = $request->custom_attributes;
            $EmailTemplate->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_update') ,$EmailTemplate, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function destroy($id)
    {
        try {
            $checkId= EmailTemplate::where('id',$id)->withoutGlobalScope('top_most_parent_id')->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            if(auth()->user()->user_type_id=='1')
            {
                EmailTemplate::where('id',$id)->delete();
                return prepareResult(true,getLangByLabelGroups('BcCommon','message_delete') ,[], config('httpcodes.success'));
            }
           return prepareResult(false, getLangByLabelGroups('BcCommon','message_unauthorized'), [],config('httpcodes.unauthorized'));
            
        } catch (\Throwable $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
