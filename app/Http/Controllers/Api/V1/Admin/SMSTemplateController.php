<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Exception;
use DB;

class SMSTemplateController extends Controller
{
   /* public function __construct()
    {
        //Permission
        $this->middleware('permission:message-template-list');
        $this->middleware('permission:message-template-add', ['only' => ['store']]);
        $this->middleware('permission:message-template-edit', ['only' => ['update']]);
        $this->middleware('permission:message-template-view', ['only' => ['show']]);
        $this->middleware('permission:message-template-delete', ['only' => ['destroy']]);
    }*/
    
    public function smsTemplates(Request $request)
    {
        try {
            $query = SmsTemplate::select('*');
            if(!empty($request->sms_for))
            {
                $query->where('sms_for', 'LIKE', '%'.$request->sms_for.'%');
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

             return prepareResult(true,"Template list",$query,config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function store(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'sms_for'      => 'required',
            'sms_body'     => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
            $smsTemplate = new SmsTemplate;
            $smsTemplate->sms_for = $request->sms_for;
            $smsTemplate->sms_body = $request->sms_body;
            $smsTemplate->custom_attributes  = $request->custom_attributes;
            $smsTemplate->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('message_CompanyType','create') ,$smsTemplate, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function show(SmsTemplate $smsTemplate)
    {
        try {
            if($smsTemplate)
            {
                return prepareResult(true,'show Template' ,$smsTemplate, config('httpcodes.success'));
            }
            return prepareResult(false, 'Record Not Found', [],config('httpcodes.not_found'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request, SmsTemplate $smsTemplate)
    {
        $validation = \Validator::make($request->all(), [
            'sms_body'     => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        DB::beginTransaction();
        try {
            $smsTemplate->sms_body = $request->sms_body;
            $smsTemplate->custom_attributes  = $request->custom_attributes;
            $smsTemplate->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('message_CompanyType','update') ,$smsTemplate, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function destroy(SmsTemplate $smsTemplate)
    {
        try {
            if(auth()->user()->user_type_id=='1')
            {
                $smsTemplate->delete();
                return prepareResult(true,getLangByLabelGroups('message_CompanyType','delete') ,[], config('httpcodes.success'));
            }
           return prepareResult(false, 'Record Not Found', [],config('httpcodes.not_found'));
            
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}

