<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Http\Resources\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LanguagesImport;
use Auth;
use App\Models\Label;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
    	try{

            $query = Language::orderBy('id', 'DESC');
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
                return prepareResult(true,"Language List",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Language List",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
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
    		'title'      => 'required',
    	]);

    	if ($validation->fails()) {
    		return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    	}

    	DB::beginTransaction();
    	try 
    	{
    		$count = Language::where('title',$request->title)->where('value',$request->value)->count();
    		if($count>=1)
    		{
    			return prepareResult(false,getLangByLabelGroups('Language','message_Dublicate Entry'), [],config('httpcodes.not_found'));

    		}
    		$language = new Language;
    		$language->title                = $request->title;
    		$language->value                = $request->value;
    		$language->status               = $request->status;
    		$language->entry_mode           = $request->entry_mode;
    		$language->save();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Language','message_create') ,$language, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function show(Language $language)
    {
    	return prepareResult(true,getLangByLabelGroups('Language','message_language_create') ,$language, config('httpcodes.success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Language  $language
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request,Language $language)
    {
    	$validation = \Validator::make($request->all(), [
    		'title'      => 'required',
    	]);

    	if ($validation->fails()) {
    		return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
    	}

    	DB::beginTransaction();
    	try 
    	{
    		$language->title                = $request->title;
    		$language->value                = $request->value;
    		$language->status               = $request->status;
    		$language->entry_mode           = $request->entry_mode;
    		$language->save();
    		DB::commit();
    		return prepareResult(true,getLangByLabelGroups('Language','message_language_update') ,$language, config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Language $language
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Language $language)
    {
    	try 
    	{
    		if($language->title == 'english' || $language->id == 1)
    		{
    			return prepareResult(true,getLangByLabelGroups('Language','message_language_cannot_be_deleted') ,['English language can not be deleted'], config('httpcodes.success'));
    		}
    		else
    		{  
    			Label::where('language_id',$language->id)->delete();
    			$language->delete();
    		}

    		return prepareResult(true,getLangByLabelGroups('Language','message_language_deleted') ,[], config('httpcodes.success'));
    	} catch (\Throwable $exception) {
    		\Log::error($exception);
    		DB::rollback();
    		return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
    	}
    }

}