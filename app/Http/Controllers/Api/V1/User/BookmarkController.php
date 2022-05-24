<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookmark;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;

class BookMarkController extends Controller
{
    public function __construct()
    {

        // $this->middleware('permission:bookmark-browse',['except' => ['show']]);
        // $this->middleware('permission:bookmark-add', ['only' => ['store']]);
        // $this->middleware('permission:bookmark-edit', ['only' => ['update']]);
        // $this->middleware('permission:bookmark-read', ['only' => ['show']]);
        // $this->middleware('permission:bookmark-delete', ['only' => ['destroy']]);
        
    }
    public function bookmarks(Request $request)
    {
        try {
	        $user = getUser();

            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = Bookmark::orderBy('id','DESC');
            // if($user->user_type_id =='2'){
                
            //     $query = $query->orderBy('id','DESC');
            // } else{
            //     $query =  $query->whereIn('branch_id',$allChilds);
            // }

            $query = $query->orderBy('id','DESC');
            
            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->with('assignEmployee.employee:id,name,email,contact_number')->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                return prepareResult(true,"Bookmark list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"Bookmark list",$query,config('httpcodes.success')); 
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function store(Request $request)
    {
        try 
        {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[   
                'title' => 'required'
            ],
            [
            	'title.required' =>  getLangByLabelGroups('message_Activity','title')
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $bookmark = new Bookmark;
		    $bookmark->target = $request->target; 
		    $bookmark->title = $request->title;
		    $bookmark->icon = $request->icon; 
		    $bookmark->link = $request->link; 
		 	$bookmark->is_bookmarked = $request->is_bookmarked;
		 	$bookmark->save();
		    
			return prepareResult(true,'Bookmark Added successfully' ,$bookmark, config('httpcodes.success'));

        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function update(Request $request,$id){
        try 
        {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[   
                'title' => 'required'
            ],
            [
            	'title.required' =>  getLangByLabelGroups('message_Activity','title')
            ]);
            if ($validator->fails()) {
                return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
            }
            $bookmark = Bookmark::find($id);
		    $bookmark->target = $request->target; 
		    $bookmark->title = $request->title;
		    $bookmark->icon = $request->icon; 
		    $bookmark->link = $request->link; 
		 	$bookmark->is_bookmarked = $request->is_bookmarked;
		 	$bookmark->save();
		    
			return prepareResult(true,'Bookmark Updated successfully' ,$bookmark, config('httpcodes.success'));

        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function destroy($id)
    {
        try {
	    	$user = getUser();
        	$checkId= Bookmark::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('message_Bookmark','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$bookmark = Bookmark::where('id',$id)->delete();
         	return prepareResult(true,getLangByLabelGroups('message_Bookmark','delete') ,[], config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
    
    public function show($id)
    {
        try {
	    	$user = getUser();
        	$checkId= Bookmark::where('id',$id)->first();
			if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('message_Bookmark','id_not_found'), [],config('httpcodes.not_found'));
            }
        	$bookmark = Bookmark::find($id);
	        return prepareResult(true,'View Task' ,$bookmark, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    
}
