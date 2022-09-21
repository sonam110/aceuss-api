<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LabelsImport;
use App\Models\Language;
use App\Exports\LabelsExport;

class LabelController extends Controller
{
	// public function __construct()
 //    {

 //        $this->middleware('permission:label-browse',['except' => ['show']]);
 //        $this->middleware('permission:label-add', ['only' => ['store']]);
 //        $this->middleware('permission:label-edit', ['only' => ['update']]);
 //        $this->middleware('permission:label-read', ['only' => ['show']]);
 //        $this->middleware('permission:label-delete', ['only' => ['destroy']]);
        
 //    }
	public function labels(Request $request)
	{

		try {

			$query = Label::orderBy('created_at','asc');
			if(!empty($request->language_id))
			{
				$query = $query->where('language_id',$request->language_id);
			}
			if(!empty($request->label_name))
			{
				$query = $query->where('label_name','like', '%'.$request->label_name.'%');
			}

			if(!empty($request->label_value))
			{
				$query = $query->where('label_value','like', '%'.$request->label_value.'%');
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
			return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
			
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */

	public function store(Request $request)
	{

		$validation = \Validator::make($request->all(), [
			'label_name'      => 'required',
		]);

		if ($validation->fails()) {
		   return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
		}

		DB::beginTransaction();
		try 
		{
			$count = Label::where('label_name',$request->label_name)->where('label_value',$request->label_value)->where('language_id',$request->language_id)->count();
			if($count>=1)
			{
				return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_already_exists'), [],config('httpcodes.bad_request'));
			}
			$label = new Label;
			$label->group_id         = $request->group_id;
			$label->language_id      = $request->language_id;
			$label->label_name       = $request->label_name;
			$label->label_value      = $request->label_value;
			$label->status           = $request->status;
			$label->entry_mode       = $request->entry_mode;
			$label->save();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$label, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Label  $label
	 * @return \Illuminate\Http\Response
	 */

	public function show($id)
	{
		try 
		{
			$checkId= Label::find($id);
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			 return prepareResult(true,getLangByLabelGroups('BcCommon','message_show') ,$checkId, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Label  $label
	 * @return \Illuminate\Http\Response
	 */
	
	public function update(Request $request,$id)
	{
		$validation = \Validator::make($request->all(), [
			'label_name'      => 'required',
		]);

		if ($validation->fails()) {
		   return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
		}

		DB::beginTransaction();
		try 
		{
			$label = Label::find($id);
			$label->group_id         = $request->group_id;
			$label->language_id      = $request->language_id;
			$label->label_name       = $request->label_name;
			$label->label_value      = $request->label_value;
			$label->status           = $request->status;
			$label->entry_mode       = $request->entry_mode;
			$label->save();
			DB::commit();
			return prepareResult(true,getLangByLabelGroups('BcCommon','message_update') ,$label, config('httpcodes.success'));
		} catch (\Throwable $exception) {
			logException($exception);
			DB::rollback();
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Label $label
	 * @return \Illuminate\Http\Response
	 * @throws \Exception
	 */
	public function destroy($id)
	{
		try 
		{
			$checkId= Label::find($id);
			if (!is_object($checkId)) {
				return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
			}
			if(auth()->user()->user_type_id=='1')
			{
				Label::where('id',$id)->delete();
				return prepareResult(true,getLangByLabelGroups('BcCommon','message_delete') ,[], config('httpcodes.success'));
			}
		   return prepareResult(false, getLangByLabelGroups('BcCommon','message_unauthorized'), [],config('httpcodes.unauthorized'));
			
		} catch (\Throwable $exception) {
			logException($exception);
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
		}
	}

	public function labelsImport(Request $request)
	{
		if($request->language_id)
		{
			$language =  Language::find($request->language_id);
		}
        else
        {
            $language = new Language;
        }
        $language->title	= $request->language_title;
        $language->value    = $request->language_value;
        $language->status   = 1;
        $language->save();
		$data = ['language_id' => $language->id];
		Label::where('language_id',$language->id)->delete();
		$import = Excel::import(new LabelsImport($data),request()->file('file'));

		return prepareResult(true,getLangByLabelGroups('BcCommon','message_import') ,[], config('httpcodes.success'));
	}

	public function labelsExport(Request $request) 
    {
        $rand = rand(1,9);
        
        $excel = Excel::store(new LabelsExport(), 'export/'.$rand.'.xlsx' , 'export_path');
        
        return prepareResult(true,getLangByLabelGroups('BcCommon','message_export') ,['url' => env('APP_URL').'export/'.$rand.'.xlsx'], config('httpcodes.success'));
         
    }
}
