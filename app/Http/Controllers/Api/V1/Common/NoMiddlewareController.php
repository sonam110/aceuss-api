<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use DB;
use App\Models\CompanySetting;
class NoMiddlewareController extends Controller
{
    
     /*---------------Agency list------------------------------------*/
    public function agencyList(Request $request){
        try {
        $query = Agency::select('id','name')->orderby('id','ASC');
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
                return prepareResult(true,"Agency list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Agency list",$query,$this->success);
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
                
        }
        
    }
     /*---------------conntry list------------------------------------*/
    public function countryList(Request $request){
        try {
        $query = DB::table('countries')->select('id','name')->orderby('id','ASC');
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
                return prepareResult(true,"Country list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Country list",$query,$this->success);
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
                
        }
        
    }
    public function companySetting($user_id)
    { 
        try {
            $userInfo = getUser();
            
            $checkSettings = CompanySetting::select(array('company_settings.*', DB::raw("(SELECT organization_number from users WHERE users.id = ".$user_id.") organization_number")))->where('user_id',$user_id)->first();
            if(!is_object($checkSettings)){
                 return prepareResult(false,'User not found',[], $this->unprocessableEntity); 
            }
            return prepareResult(true,'CompanySettings',$checkSettings, $this->success);
                
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
}
