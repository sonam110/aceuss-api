<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookmarkMaster;
use Validator;
use Auth;
use Exception;
use DB;

class BookmarkMasterController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:bookmark_masters-browse',['except' => ['show']]);
        // $this->middleware('permission:bookmark_masters-add', ['only' => ['store']]);
        // $this->middleware('permission:bookmark_masters-edit', ['only' => ['update']]);
        // $this->middleware('permission:bookmark_masters-read', ['only' => ['show']]);
        // $this->middleware('permission:bookmark_masters-delete', ['only' => ['destroy']]);
        
    }
    public function bookmarkMasters(Request $request)
    {
        try {
            $query = BookmarkMaster::orderBy('id', 'DESC');
            if(!empty($request->title))
            {
                $query->where('title','like','%'.$request->title.'%');
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'title' => 'required',   
	        ],
		    [
            'title.required' => getLangByLabelGroups('BcValidation','message_title_required'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkAlready = BookmarkMaster::where('title',$request->title)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_already_exists'),[], config('httpcodes.bad_request')); 
        	}
	        $BookmarkMaster = new BookmarkMaster;
            $BookmarkMaster->title = $request->title;
            $BookmarkMaster->target = $request->target;
            $BookmarkMaster->icon = $request->icon;
            $BookmarkMaster->icon_type = $request->icon_type;
            $BookmarkMaster->link = $request->link;
            $BookmarkMaster->user_types = json_encode($request->user_types); 
		 	$BookmarkMaster->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$BookmarkMaster, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id)
    {
        try {
            $checkId= BookmarkMaster::where('id',$id)->first();
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $bookmark_master = BookmarkMaster::where('id',$id)->first();
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_show'),$bookmark_master, config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function update(Request $request,$id) 
    {
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
	           	'title' => 'required', 
	        ],
	    	[
            'title.required' => getLangByLabelGroups('BcValidation','message_title_required'),
            ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
        	$checkId = BookmarkMaster::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            $checkAlready = BookmarkMaster::where('id','!=',$id)->where('title',$request->title)->first(); 
        	if($checkAlready) {
              	return prepareResult(false,getLangByLabelGroups('BcCommon','message_record_already_exists'),[], config('httpcodes.bad_request')); 

        	}
	        $BookmarkMaster = BookmarkMaster::find($id);
		 	$BookmarkMaster->title = $request->title;
            $BookmarkMaster->target = $request->target;
            $BookmarkMaster->icon = $request->icon;
            $BookmarkMaster->icon_type = $request->icon_type;
            $BookmarkMaster->link = $request->link;
            $BookmarkMaster->user_types = json_encode($request->user_types); 
            $BookmarkMaster->save();
            DB::commit();
	        return prepareResult(true,getLangByLabelGroups('BcCommon','message_update') ,$BookmarkMaster, config('httpcodes.success'));
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
        	$checkId= BookmarkMaster::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false, getLangByLabelGroups('BcCommon','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            
        	$BookmarkMaster = BookmarkMaster::findOrFail($id);
            $BookmarkMaster->delete();
         	return prepareResult(true, getLangByLabelGroups('BcCommon','message_delete') ,[], config('httpcodes.success'));
		     	
			    
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
            
        }
    }
}
