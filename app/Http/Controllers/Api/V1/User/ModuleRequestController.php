<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModuleRequest;
use App\Models\Module;
use App\Models\ModuleRequestData;
use App\Models\User;
use App\Models\EmailTemplate;
use Validator;
use Auth;
use DB;
use Str;
class ModuleRequestController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:module-request-browse',['except' => ['show']]);
    //     $this->middleware('permission:module-request-add', ['only' => ['store']]);
    //     $this->middleware('permission:module-request-edit', ['only' => ['update']]);
    //     $this->middleware('permission:module-request-read', ['only' => ['show']]);
    //     $this->middleware('permission:module-request-delete', ['only' => ['destroy']]);
    // }
	

	public function moduleRequests(Request $request)
	{
		try 
		{
			$user = getUser();
			$query = ModuleRequest::orderBy('id', 'DESC')->with('user:id,name');
			if($user->user_type_id != 1 && $user->user_type_id != 16)
			{
				$query->where(function ($q) {
	                $q->where('user_id', auth()->id())
	                    ->orWhere('user_id', auth()->user()->top_most_parent_id);
	            });
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

			return prepareResult(true,getLangByLabelGroups('ModuleRequest','message_list'),$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function store(Request $request)
	{
		DB::beginTransaction();
		try 
		{
			$validator = Validator::make($request->all(),[ 
				'modules' => 'required', 
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}
			$moduleRequest = new ModuleRequest;
			$moduleRequest->user_id = Auth::id();
			$moduleRequest->modules = json_encode($request->modules);
			$moduleRequest->request_comment = $request->request_comment;
			$moduleRequest->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
			$moduleRequest->status =  '0';
			$moduleRequest->save();

			//----------------notification------------------------//
			$modules = implode(',', json_decode($moduleRequest->modules));
			$user = User::first();
			$data_id =  $moduleRequest->id;
			$notification_template = EmailTemplate::where('mail_sms_for', 'module-request')->first();
			if($user)
			{
				$variable_data = [
	            	'{{name}}' => aceussDecrypt($user->name),
	                '{{requested_by}}'  => aceussDecrypt(Auth::User()->name),
	                '{{modules}}'       => $modules,
	                '{{request_date}}'   => date('Y-m-d'),
	                '{{request_comment}}'=> $request->request_comment
	            ];
	            actionNotification($user,$data_id,$notification_template,$variable_data, null, null, true);
	        }
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('ModuleRequest','message_create') ,$moduleRequest, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function update(Request $request,$id)
	{
		DB::beginTransaction();
		try 
		{
			$validator = Validator::make($request->all(),[ 
				'modules' => 'required', 
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
			}

			$checkId = ModuleRequest::where('id',$id) ->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ModuleRequest','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			$moduleRequest = $checkId;
			$moduleRequest->user_id = Auth::id();
			$moduleRequest->modules = json_encode($request->modules);
			$moduleRequest->request_comment = $request->request_comment;
			$moduleRequest->entry_mode =  (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
			$moduleRequest->status =  '0';
			$moduleRequest->save();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('ModuleRequest','message_update') ,$moduleRequest, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function destroy($id)
	{
		try 
		{
			$checkId= ModuleRequest::where('id',$id)->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ModuleRequest','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			$checkId->delete();
			return prepareResult(true,getLangByLabelGroups('ModuleRequest','message_delete') ,[], config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
		}
	}

	public function show($id)
	{
		try 
		{
			$checkId= ModuleRequest::where('id',$id)->first();
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('ModuleRequest','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			return prepareResult(true,getLangByLabelGroups('ModuleRequest','message_show') ,$checkId, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}
	}

	public function changeStatus(Request $request,$id)
	{
		DB::beginTransaction();
		try 
		{
			$moduleRequest = ModuleRequest::find($id);
			if (!is_object($moduleRequest)) {
				return prepareResult(false,getLangByLabelGroups('ModuleRequest','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			$moduleRequest->reply_comment = $request->reply_comment;
			$moduleRequest->reply_date = date('Y-m-d');
			$moduleRequest->status = $request->status;
			$moduleRequest->save();

			$modules = implode(',', json_decode($moduleRequest->modules));
			$user = User::find($moduleRequest->user_id);
			$data_id =  $moduleRequest->id;
			if($request->status == 1 && $user)
			{
				$notification_template = EmailTemplate::where('mail_sms_for', 'module-request-approved')->first();
                $variable_data = [
                	'{{name}}' => aceussDecrypt($user->name),
                    '{{approved_by}}'  => aceussDecrypt(Auth::User()->name),
                    '{{modules}}'          => $modules,
                    '{{reply_date}}'          => date('Y-m-d'),
                    '{{reply_comment}}'        => $request->reply_comment
                ];
			}
			elseif($request->status == 2 && $user)
			{
				$notification_template = EmailTemplate::where('mail_sms_for', 'module-request-rejected')->first();
                $variable_data = [
                	'{{name}}' => aceussDecrypt($user->name),
                    '{{approved_by}}'  => aceussDecrypt(Auth::User()->name),
                    '{{modules}}'          => $modules,
                    '{{reply_date}}'          => date('Y-m-d'),
                    '{{reply_comment}}'        => $request->reply_comment
                ];
			}

			actionNotification($user,$data_id,$notification_template,$variable_data, null, null, true);
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('ModuleRequest','message_change_status') ,$moduleRequest, config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}
}
