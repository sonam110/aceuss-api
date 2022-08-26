<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyType;
use Validator;
use Auth;
use Exception;
use DB;
class CompanyTypeController extends Controller
{
    public function __construct()
    {

        /*$this->middleware('permission:companyType-browse',['except' => ['show']]);
        $this->middleware('permission:companyType-add', ['only' => ['store']]);
        $this->middleware('permission:companyType-edit', ['only' => ['update']]);
        $this->middleware('permission:companyType-read', ['only' => ['show']]);
        $this->middleware('permission:companyType-delete', ['only' => ['destroy']]);*/
        
    }
    public function companyTypes(Request $request)
    {
        try {
            $user = getUser();
            $whereRaw = $this->getWhereRawFromRequest($request);
            $query = CompanyType::select('*');

            if($user->top_most_parent_id!=1)
            {
                $getAssignedCompanyType = getTopMostParent();
                $query->whereIn('id', json_decode($getAssignedCompanyType->company_type_id, true));
            }

            if($whereRaw != '') { 
                $query->whereRaw($whereRaw)
                ->orderBy('id', 'DESC');
            } else {
                $query->orderBy('id', 'DESC');
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
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_list'),$query,config('httpcodes.success'));
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
                'name' => 'required',   
            ],
            [
            'name.required' => getLangByLabelGroups('CompanyType','message_name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkAlready = CompanyType::where('name',$request->name)->first(); 
            if($checkAlready) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','message_name_already_exists'),[], config('httpcodes.bad_request')); 

            }
            $companyType = new CompanyType;
            $companyType->created_by = $user->id;
            $companyType->name = $request->name;
            $companyType->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $companyType->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_create') ,$companyType, config('httpcodes.success'));
        }
        catch(Exception $exception) {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function show($id)
    {
        try 
        {
            $user = getUser();
            $checkId= CompanyType::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $companyType = CompanyType::where('id',$id)->first();
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_show') ,$companyType, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try 
        {
            $user = getUser();
            $validator = Validator::make($request->all(),[
                'name' => 'required',   
            ],
            [
            'name.required' => getLangByLabelGroups('CompanyType','message_name'),
            ]);
            if ($validator->fails()) 
            {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkId = CompanyType::where('id',$id)->first();
            if (!is_object($checkId)) 
            {
                return prepareResult(false,getLangByLabelGroups('CompanyType','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $checkAlready = CompanyType::where('id','!=',$id)->where('name',$request->name)->first(); 
            if($checkAlready) 
            {
                return prepareResult(false,getLangByLabelGroups('CompanyType','message_name_already_exists'),[], config('httpcodes.bad_request')); 
            }
            $companyType = CompanyType::find($id);
            $companyType->name = $request->name;
            $companyType->status = ($request->status) ? $request->status:'1';
            $companyType->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $companyType->save();
              DB::commit();
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_update'),$companyType, config('httpcodes.success'));
        }
        catch(Exception $exception) 
        {
             \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function destroy($id)
    {
        try 
        {
            $user = getUser();
            $checkId= CompanyType::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            
            $companyType = CompanyType::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('CompanyType','message_delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) 
        {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        return($w);
    }
}
