<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paragraph;
use Validator;
use Auth;
use Exception;
use DB;

class ParagraphController extends Controller
{
    public function __construct()
    {

        /*$this->middleware('permission:paragraphs-browse',['except' => ['show']]);
        $this->middleware('permission:paragraphs-add', ['only' => ['store']]);
        $this->middleware('permission:paragraphs-edit', ['only' => ['update']]);
        $this->middleware('permission:paragraphs-read', ['only' => ['show']]);
        $this->middleware('permission:paragraphs-delete', ['only' => ['destroy']]);*/
        
    }
    public function paragraphs(Request $request)
    {
        try {
            $query = Paragraph::where(function ($q) use ($request) {
                $q->whereNull('top_most_parent_id')
                    ->orWhere('top_most_parent_id', 1)
                    ->orWhere('top_most_parent_id', auth()->user()->top_most_parent_id);
            })
            ->withoutGlobalScope('top_most_parent_id')
            ->orderBy('id', 'DESC');
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
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(),[
                'paragraph' => 'required',   
            ],
            [
            'paragraph.required' => 'Paragraph Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $Paragraph = new Paragraph;
            $Paragraph->paragraph = $request->paragraph;
            $Paragraph->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$Paragraph, config('httpcodes.success'));
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
        try {
            $validator = Validator::make($request->all(),[
                'paragraph' => 'required',   
            ],
            [
            'paragraph.required' => 'Paragraph Field is required',
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $checkId = Paragraph::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            
            $Paragraph = Paragraph::find($id);
            $Paragraph->paragraph = $request->paragraph;
            $Paragraph->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_update'),$Paragraph, config('httpcodes.success'));
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
            $checkId= Paragraph::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $Paragraph = Paragraph::where('id',$id)->delete();
            return prepareResult(true, getLangByLabelGroups('BcCommon','message_delete') ,[], config('httpcodes.success'));
                
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
