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
use App\Models\Label;
use App\Models\Language;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Hash;

class NoAuthController extends Controller
{
    

    public function getLabels(Request $request)
    {
        // $label = Label::all();
        // foreach ($label as $key => $value) {
        //    $label_name = 'message_'.$value->label_name;
        //    $value->update(['label_name'=>$label_name]);
        // }
        try{
            $query = Label::select('label_name','label_value')->orderBy('created_at','asc');
            if(!empty($request->language_id))
            {
                $language = Language::find($request->language_id);
                
            }
            else
            {
                $language = Language::first();
            }
            $query = $query->where('language_id',$language->id);
            if(!empty($request->label_name))
            {
                $query = $query->where('label_name','like', '%'.$request->label_name.'%');
            }

            $query = $query->get();

            $data = [];
            $data['language'] = $language;
            foreach ($query as $key => $q) {
                $data['labels'][$q->label_name] = $q->label_value;
            }

            return prepareResult(true,"Label list",$data,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function getLabelByLanguageId($language_id)
    {
        try{
            $query = Label::select('label_name','label_value')->orderBy('created_at','asc')->where("language_id",$language_id)->get();

            $data = [];
            foreach ($query as $key => $q) {
                $data[$q->label_name] = $q->label_value;
            }

            return $data;
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function getLanguages(Request $request)
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
}
