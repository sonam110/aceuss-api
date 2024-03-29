<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Validator;
use Auth;
use Exception;
use DB;

class PackageController extends Controller
{   
     public function __construct()
    {

        $this->middleware('permission:packages-browse',['except' => ['show']]);
        $this->middleware('permission:packages-add', ['only' => ['store']]);
        $this->middleware('permission:packages-edit', ['only' => ['update']]);
        $this->middleware('permission:packages-read', ['only' => ['show']]);
        $this->middleware('permission:packages-delete', ['only' => ['destroy']]);
        
    }
    public function packages(Request $request)
    {
        try {
            $query = Package::select(array('packages.*', DB::raw("(SELECT count(*) from subscriptions WHERE packages.id = subscriptions.package_id) purchaseCount")))
                ->where('status', '!=', '2');
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query->orderBy('id', 'DESC');
            } else {
                $query->orderBy('id', 'DESC');
            }

            if(!empty($request->is_enable_bankid_charges))
            {
                $query->where('bankid_charges', $request->is_enable_bankid_charges);
            }

            if(!empty($request->is_sms_enable))
            {
                $query->where('is_sms_enable', $request->is_sms_enable);
            }

            if(!empty($request->name))
            {
                $query->where('name', 'LIKE', '%'.$request->name.'%');
            }
            
            if(!empty($request->number_of_employees))
            {
                $query->where('number_of_employees', $request->number_of_employees);
            }
            if(!empty($request->number_of_patients))
            {
                $query->where('number_of_patients', $request->number_of_patients);
            }
            if(!empty($request->price))
            {
                $query->where('price', $request->price);
            }
            if(!empty($request->status))
            {
                $query->where('status', $request->status);
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
            return prepareResult(true,getLangByLabelGroups('Package','message_list'),$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
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
                'price' => 'required',   
                'validity_in_days' => 'required|numeric', 
                'number_of_patients' => 'required|numeric',    
                'number_of_employees' => 'required|numeric',    
            ],
            [
            'name.required' =>  getLangByLabelGroups('Package','message_name'),
            'price.required' =>  getLangByLabelGroups('Package','message_price'),
            'validity_in_days.required' =>  getLangByLabelGroups('Package','message_validity_in_days'),
            'number_of_patients.required' =>  getLangByLabelGroups('Package','message_number_of_patients'),
            'number_of_employees.required' =>  getLangByLabelGroups('Package','message_number_of_employees'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $discounted_price  = 0;
            if($request->is_on_offer){
                $validator = Validator::make($request->all(),[
                'discount_type' => 'required|in:1,2',
                'discount_value' => 'required|numeric',    
                ],
                [
                'discount_type.required' => getLangByLabelGroups('Package','message_discount_type'),
                'discount_value.required' => getLangByLabelGroups('Package','message_discount_value'),
                ]); 
                if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

                if($request->discount_type == '1'){
                    $price = ($request->price*$request->discount_value)/100;
                    $discounted_price =  $request->price -$price;
                }
                if($request->discount_type == '2'){
                    $discounted_price = $request->price -$request->discount_value;
                }

            }
            $package = new Package;
            $package->name = $request->name;
            $package->price = $request->price;
            $package->is_on_offer = ($request->is_on_offer) ? 1 : 0;
            $package->discount_type = (!empty($request->discount_type)) ? $request->discount_type :1;
            $package->discount_value = ($request->discount_value)? $request->discount_value :0;
            $package->discounted_price = $discounted_price;
            $package->validity_in_days = $request->validity_in_days;
            $package->number_of_patients = $request->number_of_patients;
            $package->number_of_employees = $request->number_of_employees;
            $package->bankid_charges = $request->bankid_charges;
            $package->sms_charges = $request->sms_charges;
            $package->is_sms_enable = ($request->is_sms_enable) ? 1 : 0;
            $package->is_enable_bankid_charges = ($request->is_enable_bankid_charges) ? 1 : 0;
            $package->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $package->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Package','message_create') ,$package, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function show($id)
    {
        try {
            $checkId= Package::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Package','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $package = Package::where('id',$id)->first();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_show'),$package, config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = getUser();
            $validator = Validator::make($request->all(),[   
                'name' => 'required',   
                'price' => 'required',   
                'validity_in_days' => 'required|numeric', 
                'number_of_patients' => 'required|numeric',    
                'number_of_employees' => 'required|numeric',    
           ],
            [
            'name.required' =>  getLangByLabelGroups('Package','message_name'),
            'price.required' =>  getLangByLabelGroups('Package','message_price'),
            'validity_in_days.required' =>  getLangByLabelGroups('Package','message_validity_in_days'),
            'number_of_patients.required' =>  getLangByLabelGroups('Package','message_number_of_patients'),
            'number_of_employees.required' =>  getLangByLabelGroups('Package','message_number_of_employees'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkId = Package::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Package','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $discounted_price  = 0;
            if($request->is_on_offer){
                $validator = Validator::make($request->all(),[
                'discount_type' => 'required|in:1,2',
                'discount_value' => 'required|numeric',    
                ],
                [
                'discount_type.required' => getLangByLabelGroups('Package','message_discount_type'),
                'discount_value.required' => getLangByLabelGroups('Package','message_discount_value'),
                ]); 
                if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
                }

                if($request->discount_type == '1'){
                    $price = ($request->price*$request->discount_value)/100;
                    $discounted_price =  $request->price -$price;
                }
                if($request->discount_type == '2'){
                    $discounted_price = $request->price -$request->discount_value;
                }

            }
            $package = Package::find($id);
            $package->name = $request->name;
            $package->price = $request->price;
            $package->is_on_offer = ($request->is_on_offer) ? 1 : 0;
            $package->discount_type = (!empty($request->discount_type)) ? $request->discount_type :1;
            $package->discount_value = ($request->discount_value)? $request->discount_value :0;
            $package->discounted_price = $discounted_price;
            $package->validity_in_days = $request->validity_in_days;
            $package->number_of_patients = $request->number_of_patients;
            $package->number_of_employees = $request->number_of_employees;
            $package->bankid_charges = $request->bankid_charges;
            $package->sms_charges = $request->sms_charges;
            $package->is_sms_enable = ($request->is_sms_enable) ? 1 : 0;
            $package->is_enable_bankid_charges = ($request->is_enable_bankid_charges) ? 1 : 0;
            $package->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
            $package->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Package','message_update') ,$package, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));  
        }
    }

    public function destroy($id)
    {
        try {
            $checkId= Package::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Package','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $package = Package::where('id',$id)->update(['status'=>'2']);
            return prepareResult(true,getLangByLabelGroups('Package','message_delete'),[], config('httpcodes.success'));
                
                
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    public function restorePackage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[   
                'id' => 'required',      
            ],
            [
            'id.required' => 'Id field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $id = $request->id;
            $checkId= Package::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('Package','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $package = Package::where('id',$id)->update(['status'=>'1']);
            return prepareResult(true,getLangByLabelGroups('Package','message_restore'),[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }

    private function getWhereRawFromRequest(Request $request) 
    {
        $w = '';
        
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        if (is_null($request->input('name')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "name like '%" .trim(strtolower($request->input('name'))) . "%')";

             
        }
        if (is_null($request->input('discounted_price')) == false) {
            if ($w != '') {$w = $w . " AND ";}
             $w = $w . "(" . "discounted_price like '%" .trim(strtolower($request->input('discounted_price'))) . "%')";

             
        }
        if (is_null($request->input('number_of_employees')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "number_of_employees = "."'" .$request->input('number_of_employees')."'".")";

             
        }
        if (is_null($request->input('number_of_patients')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "number_of_patients = "."'" .$request->input('number_of_patients')."'".")";

             
        }
        if (is_null($request->input('price')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "price = "."'" .$request->input('price')."'".")";

             
        }
        if (is_null($request->input('is_sms_enable')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "is_sms_enable = "."'" .$request->input('is_sms_enable')."'".")";

             
        }
        if (is_null($request->input('is_enable_bankid_charges')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "is_enable_bankid_charges = "."'" .$request->input('is_enable_bankid_charges')."'".")";
        }
        return($w);
    }
}
