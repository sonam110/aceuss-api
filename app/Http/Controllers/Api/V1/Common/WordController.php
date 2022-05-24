<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Word;
use Validator;
use Auth;
use Exception;
use DB;

class WordController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:words-browse',['except' => ['show']]);
        $this->middleware('permission:words-add', ['only' => ['store']]);
        $this->middleware('permission:words-edit', ['only' => ['update']]);
        $this->middleware('permission:words-read', ['only' => ['show']]);
        $this->middleware('permission:words-delete', ['only' => ['destroy']]);
        
    }
    public function words(Request $request)
    {
        try {
            $query = Word::orderBy('id', 'DESC');
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
                return prepareResult(true,"Word list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Word list",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
        
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required',   
            ],
            [
            'name.required' => getLangByLabelGroups('CompanyType','message_name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $Word = new Word;
            $Word->name = $request->name;
            $Word->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_create') ,$Word, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required',   
            ],
            [
            'name.required' => getLangByLabelGroups('CompanyType','message_name'),
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkId = Word::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('CompanyType','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            
            $Word = Word::find($id);
            $Word->name = $request->name;
            $Word->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_update'),$Word, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function destroy($id)
    {
        try {
            $checkId= Word::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('CompanyType','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            
            $Word = Word::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('CompanyType','message_delete') ,[], config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
