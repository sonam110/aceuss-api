<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookmark;
use App\Models\BookmarkMaster;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;

class BookMarkController extends Controller
{
    public function bookmarks(Request $request)
    {
        try {
	        $user = getUser();
            $bookmarked = Bookmark::where('user_id', auth()->id())
                ->with('bookmarkMaster')
                ->orderBy('id','DESC')
                ->get();
            $bookmarklist = BookmarkMaster::orderBy('title','ASC')
                ->get();
            $returnObj = [
                'bookmarked' => $bookmarked,
                'bookmarklist' => $bookmarklist,
            ];
            return prepareResult(true,"Bookmark list",$returnObj,config('httpcodes.success')); 
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
            $isBookmerked = Bookmark::where('user_id', auth()->id())->where('bookmark_master_id', $request->bookmark_master_id)->first();
            if($isBookmerked)
            {
                $action = 'removed';
                $isBookmerked->delete();
            }
            else
            {
                $bookmark = new Bookmark;
                $bookmark->bookmark_master_id = $request->bookmark_master_id; 
                $bookmark->user_id = auth()->id();
                $bookmark->save();
                $action = 'added';
            }
            $bookmarked = Bookmark::where('user_id', auth()->id())
                ->with('bookmarkMaster')
                ->orderBy('id','DESC')
                ->get();
			return prepareResult(true,'Bookmark '.$action.' successfully' ,$bookmarked, config('httpcodes.success'));

        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }
}
