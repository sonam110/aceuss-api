<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminFile;
use App\Models\FileAccessLog;
use Log;

class AdminFileController extends Controller
{
    public function __construct()
    {
        
    }

    public function adminFiles(Request $request)
    {
        try {
            $query = AdminFile::select('*')->orderBy('created_at', 'DESC')
            ->where(function ($q) use ($request) {
                $q->whereNull('top_most_parent_id')
                    ->orWhere('top_most_parent_id', 1);
            })
            ->withoutGlobalScope('top_most_parent_id');

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

    public function companyFiles(Request $request)
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
            $adminFile = AdminFile::where('id',$id)->where('top_most_parent_id', auth()->user()->top_most_parent_id)->delete();
            if($adminFile)
            {
                return prepareResult(true,'file deleted successfully',[], config('httpcodes.success'));
            }
            return prepareResult(false, 'File not found.',[], config('httpcodes.internal_server_error'));
            
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    } 

    public function fileAccessLog(Request $request)
    {
        try {
            $user = getUser();            
            $fileAccessLog = new FileAccessLog;
            $fileAccessLog->top_most_parent_id = auth()->user()->top_most_parent_id; 
            $fileAccessLog->admin_file_id = $request->admin_file_id; 
            $fileAccessLog->user_id = auth()->id(); 
            $fileAccessLog->save(); 
            return prepareResult(false, 'File access log added',[], config('httpcodes.internal_server_error'));
            
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
