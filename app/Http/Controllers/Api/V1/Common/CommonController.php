<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\PermissionExtend;
use DB;
use Validator;
use Auth;
use Exception;
use App\Models\User;
use App\Models\EmployeeType;

use App\Models\Label;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Hash;

class CommonController extends Controller
{
	protected $top_most_parent_id;
	public function __construct()
	{
		$this->middleware(function ($request, $next) {
			$this->top_most_parent_id = auth()->user()->top_most_parent_id;
			return $next($request);
		});
	}

	public function permissionList(Request $request)
	{
		try {
			$query = Permission::select('*');
			

			if(!empty($request->belongs_to))
			{
				$query->where('belongs_to',$request->belongs_to)->orWhere('belongs_to','3');
			}
			if(!empty($request->name))
			{
				$query->where('name', 'LIKE', '%'.$request->name.'%');
			}

			if(!empty($request->se_name))
			{
				$query->where('se_name', 'LIKE', '%'.$request->se_name.'%');
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

			return prepareResult(true,getLangByLabelGroups('permission','message_list'),$query,'200');
		} catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], '500');
			
		}
	}

	public function getUserList(Request $request)
	{
		try {
			$user = getUser();
			$query = User::select('id','user_type_id','name')->where('top_most_parent_id',$this->top_most_parent_id)->where('status','1') ;
			$whereRaw = $this->getWhereRawFromRequest($request);
			if($whereRaw != '') {
				$query = $query->whereRaw($whereRaw)->orderBy('id', 'DESC');
			} else {
				$query = $query->orderBy('id', 'DESC');
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
			return prepareResult(true,getLangByLabelGroups('User','message_list'),$query,'200');
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], '500');
			
		}
	}

	

	public function pateintTypes(Request $request)
	{
		try {

			$query =EmployeeType::where('type','patient') ;
			if(!empty($request->perPage))
			{
				$perPage = $request->perPage;
				$page = $request->input('page', 1);
				$total = $query->count();
				$result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get(['id','designation']);

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
				$query = $query->get(['id','designation']);
			}
			return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,'200');
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], '500');
			
		}
	}

	private function getWhereRawFromRequest(Request $request) 
	{
		$w = '';
		
		if (is_null($request->input('user_type_id')) == false) {
			if ($w != '') {$w = $w . " AND ";}
			$w = $w . "(" . "user_type_id = "."'" .$request->input('user_type_id')."'".")";
		}
		if (is_null($request->input('name')) == false) {
			if ($w != '') {$w = $w . " AND ";}
			$w = $w . "(" . "name like '%" .trim(strtolower($request->input('name'))) . "%')";
		}
		return($w);
	}

	public function smsCallback(Request $request)
	{
		$updateSmsStatus = SmsLog::where('mobile', $request['number'])
		->where('status', null)
		->orderBy('id_inc', 'ASC')
		->first();
		if($updateSmsStatus)
		{
			$updateSmsStatus->status = $request['type'];
			$updateSmsStatus->save();
		}
		Log::channel('smslog')->info($request);
		return true;
	}

	public function patientPasswordChange(Request $request)
	{
		try {
			$validator = Validator::make($request->all(),[   
				"user_id"  => "required|exists:users,id",    
				"password"  => 'required|min:8|max:30',   
			]);
			if ($validator->fails()) {
				return prepareResult(false,$validator->errors()->first(),[], '422'); 
			}
			$checkUser =User::where('id',$request->user_id)->where('top_most_parent_id',$this->top_most_parent_id)->first();
			if(!is_object($checkUser)){
				return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'),[], '422'); 
			}
			$updatePass = User::find($checkUser->id);
			$updatePass->password = Hash::make($request->password);
			$updatePass->is_password_change = '0';
			$updatePass->save();
			return prepareResult(true,getLangByLabelGroups('User','message_password_change'),$updatePass,'200');
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], '500');

		}

	}

	public function testMessageSend()
	{
		$response = sendMessage('2fa-login','','a211aefd-6167-4766-95d5-ade6e6dcf70b','827ec7a7-1370-40fb-883e-ae7b946073ec');
		return prepareResult(true,"Msg Send Successfully",$response,'200');
	}
}
