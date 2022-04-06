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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                return prepareResult(true,"Email Template list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Email Template list",$companyType,$this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
        
    }

    public function store(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'mail_sms_for'      => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], $this->unprocessableEntity); 
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
            return prepareResult(true,getLangByLabelGroups('CompanyType','create') ,$EmailTemplate, $this->success);
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
        }
    }

    public function show($id)
    {
        try {
            $checkId= EmailTemplate::where('id',$id)->withoutGlobalScope('top_most_parent_id')->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
             return prepareResult(true,'View Template' ,$checkId, $this->success);
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
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
            return prepareResult(true,getLangByLabelGroups('CompanyType','update') ,$EmailTemplate, $this->success);
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
        }
    }

    public function destroy($id)
    {
        try {
            $checkId= EmailTemplate::where('id',$id)->withoutGlobalScope('top_most_parent_id')->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Activity','id_not_found'), [],$this->not_found);
            }
            if(auth()->user()->user_type_id=='1')
            {
                EmailTemplate::where('id',$id)->delete();
                return prepareResult(true,getLangByLabelGroups('CompanyType','delete') ,[], $this->success);
            }
           return prepareResult(false, 'Record Not Found', [],$this->not_found);
            
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
        }
    }
}
