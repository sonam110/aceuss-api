<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Folder;
use Validator;
use Auth;
use Exception;
use DB;
class FolderController extends Controller
{
	public function folders(Request $request)
    {
        try {
	        $user = getUser();
	    	$whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
    		    $query = Folder::whereRaw($whereRaw)
                    ->orderBy('id', 'DESC')
    		       	->with('Parent:id,name');
            } else {
                $query = Folder::orderBy('id', 'DESC')
                    ->with('Parent:id,name');
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
                return prepareResult(true,"Folder list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
            
            return prepareResult(true,"Folder list",$query,$this->success);
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }
    public function folderParentList(Request $request)
    {
        try {
            $user = getUser();
            $folderParent = Folder::select('id','name')
                ->whereNull('parent_id')
                ->get();
          
            return prepareResult(true,"Folder list",$folderParent,$this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
        
    }
    

    public function store(Request $request){
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'name' => 'required',      
	        ],
		    [
            'name.required' => 'Name is required',
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkAlready = Folder::where('name',$request->name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,"This name Already Exist.",[], $this->unprocessableEntity); 
        	}
            if($request->parent_id) {
                $checkParent = Folder::whereNull('parent_id')->where('id',$request->parent_id)->first(); 
                if(!$checkParent) {
                    return prepareResult(false,"Parent not found.",[], $this->not_found); 
                }
            }
            
	        $folder = new Folder;
		 	$folder->parent_id = ($request->parent_id) ? $request->parent_id :null;
            $folder->name = $request->name;
		 	$folder->visible_to_users = $request->visible_to_users;
            $folder->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$folder->save();
	        return prepareResult(true,'Folder Added successfully' ,$folder, $this->success);
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
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
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
        	$checkId = Folder::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,"Id Not Found", [],$this->not_found);
            }
            $checkAlready = Folder::where('id','!=',$id)->where('name',$request->name)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,"This Name Already Exist.",[], $this->unprocessableEntity); 
        	}
            $checkParent = Folder::whereNull('parent_id')->where('id',$request->parent_id)->first(); 
            if($request->parent_id) {
                $checkParent = Folder::whereNull('parent_id')->where('id',$request->parent_id)->first(); 
                if(!$checkParent) {
                    return prepareResult(false,"Parent not found.",[], $this->not_found); 
                }
            }
	        $folder = Folder::find($id);
		 	$folder->parent_id = ($request->parent_id) ? $request->parent_id :null;
            $folder->name = $request->name;
		 	$folder->visible_to_users = $request->visible_to_users;
            $folder->entry_mode = (!empty($request->entry_mode)) ? $request->entry_mode :'Web';
		 	$folder->save();
	        return prepareResult(true,'Folder Updated successfully' ,$folder, $this->success);
			    
		       
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function destroy($id){
    	
        try {
	    	$user = getUser();
        	$checkId= Folder::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,"Id Not Found", [],$this->not_found);
            }
            
        	$folder = Folder::where('id',$id)->delete();
         	return prepareResult(true,'Folder delete successfully' ,[], $this->success);
		     	
			    
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), $this->internal_server_error);
            
        }
    }
    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('status')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "status = "."'" .$request->input('status')."'".")";
        }
        return($w);

    }

    
}
