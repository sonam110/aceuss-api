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
                $query = $query->where('language_id',$request->language_id);
            }
            if(!empty($request->label_name))
            {
                $query = $query->where('label_name','like', '%'.$request->label_name.'%');
            }

            $query = $query->get();

            $data = [];
            foreach ($query as $key => $q) {
                $data[$q->label_name] = $q->label_value;
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

    public function getLanguages()
    {
        try{
            $query = Language::get();

            return prepareResult(true,"Language List",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
