<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminFile;
use Log;

class AdminFileController extends Controller
{
    public function __construct()
    {
        
    }

    public function adminFiles(Request $request)
    {
        try {
            $query = AdminFile::select('*')->orderBy('created_at', 'DESC');

            if(!empty($request->title))
            {
                $query->where('title', 'LIKE', '%'.$request->title.'%');
            }

            if(!empty($request->per_page_record))
            {
                $perPage = $request->per_page_record;
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

            return prepareResult(true,"Admin Files",$query,config('httpcodes.success'));
        } catch (\Throwable $e) {
            Log::error($e);
           return prepareResult(false, $e->getMessage(),[], config('httpcodes.internal_server_error'));
        }
        
    }

    public function destroy($id)
    {
        try {
            $user = getUser();            
            $bankDetail = AdminFile::where('id',$id)->delete();
            return prepareResult(true,getLangByLabelGroups('Bank','delete'),[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    } 
}
