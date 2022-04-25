<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\File;
use Validator;
use Auth;
use Exception;
use DB;
class FileController extends Controller
{
	public function files(Request $request)
    {
       
        try {
	        $user = getUser();
     		$query = File::orderBy('id', 'DESC')
            ->with('Folder:id,name'); 
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
                return prepareResult(true,"File list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"File list",$query,config('httpcodes.success'));
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    	
    }
    

    public function store(Request $request){
       
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'folder_id' => 'required',      
        		'source_id' => 'required',      
        		'source_name' => 'required',      
        		'file_url' => 'required',      
	        ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
	        $file = new File;
            $file->folder_id = $request->folder_id;
		 	$file->source_id = $request->source_id;
		 	$file->source_name = $request->source_name;
		 	$file->file_url = $request->file_url;
		 	$file->file_type = $request->file_type;
		 	$file->file_extension = $request->file_extension;
		 	$file->is_compulsory = ($request->is_compulsory) ? 1:0;
		 	$file->approval_required = ($request->approval_required) ? 1:0;
		 	$file->created_by = $user->id;
		 	$file->visible_to_users = $request->visible_to_users;
            $file->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$file->save();
	        return prepareResult(true,'file Added successfully' ,$file, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id){
       
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'name' => 'required',   
	        ],
		    [
            'name.required' => 'Name is required',
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkId = File::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,"Id Not Found", [],config('httpcodes.not_found'));
            }
	        $file = File::find($id);
		 	$file->top_most_parent_id = $user->top_most_parent_id;
            $file->folder_id = $request->folder_id;
		 	$file->source_id = $request->source_id;
		 	$file->source_name = $request->source_name;
		 	$file->file_url = $request->file_url;
		 	$file->file_type = $request->file_type;
		 	$file->file_extension = $request->file_extension;
		 	$file->is_compulsory = ($request->is_compulsory) ? 1:0;
		 	$file->approval_required = ($request->approval_required) ? 1:0;
		 	$file->visible_to_users = $request->visible_to_users;
            $file->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$file->save();
	        return prepareResult(true,'file Updated successfully' ,$file, config('httpcodes.success'));
			    
		       
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function destroy($id){
    	
       
        try {
	    	$user = getUser();
        	$checkId= File::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,"Id Not Found", [],config('httpcodes.not_found'));
            }
        	$file = File::where('id',$id)->delete();
         	return prepareResult(true,'file delete successfully' ,[], config('httpcodes.success'));
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
    public function approvedFile(Request $request){
       
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'id' => 'required',   
	        ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$id = $request->id;
        	$checkId= File::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,"Id Not Found", [],config('httpcodes.not_found'));
            }
            $file = File::find($id);
		 	$file->approved_by = $user->id;
		 	$file->approved_date = date('Y-m-d');
		 	$file->status = '1';
		 	$file->save();
	        return prepareResult(true,'File successfully approved' ,$file, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    public function show($id){
       
        try {
	    	$user = getUser();
        	$checkId= File::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,"Id Not Found", [],config('httpcodes.not_found'));
            }
            $file = File::where('id',$id)->with('Folder:id,name')->first();
	        return prepareResult(true,'File successfully approved' ,$file, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
}
