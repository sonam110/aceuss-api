<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use Exception;
use App\Models\PersonalInfoDuringIp;
use App\Models\User;

class PersonController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:persons-browse',['except' => ['show']]);
        $this->middleware('permission:persons-add', ['only' => ['store']]);
        $this->middleware('permission:persons-edit', ['only' => ['update']]);
        $this->middleware('permission:persons-read', ['only' => ['show']]);
        $this->middleware('permission:persons-delete', ['only' => ['destroy']]);
        
    }

    public function patientPersonList(Request $request)
    {
        try {
            $query = PersonalInfoDuringIp::with('user','patient:id,name,email');

            if(!empty($request->patient_id))
            {
                $query->where('patient_id', $request->patient_id);
            }
            else
            {
                $query->where('patient_id', auth()->id());
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
            
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_list') ,$query, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
    public function store(Request $request)
    {
        return prepareResult(true, 'Please check API or discuss with developer, this API no longer in usage',[], config('httpcodes.success'));
    }

    public function show($id)
    {
        return prepareResult(true, 'Please check API or discuss with developer, this API no longer in usage',[], config('httpcodes.success'));
    }

    public function update(Request $request,$id)
    {
        return prepareResult(true, 'Please check API or discuss with developer, this API no longer in usage',[], config('httpcodes.success'));
    }

    public function destroy($id)
    {
        try {
            $user = getUser();
        	$checkId = PersonalInfoDuringIp::where('id', $id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            
        	$personDelete = PersonalInfoDuringIp::where('id', $id)->delete();
         	return prepareResult(true, getLangByLabelGroups('BcCommon','message_delete') ,[], config('httpcodes.success'));
		}
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
