<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Exception;
use DB;
use Carbon\Carbon;
use App\Models\Comment;
class CommentController extends Controller
{
    
    public function comment(Request $request){
        DB::beginTransaction();
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'source_id' => 'required',   
        		'source_name' => 'required',   
        		'comment' => 'required',         
        		    
	        ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], $this->unprocessableEntity); 
        	}
            $parent = Comment::where('id',$request->parent_id)->first();
	        $addComment = new Comment;
		    $addComment->parent_id = $request->parent_id ;
		    $addComment->source_id = $request->source_id ;
		    $addComment->source_name = $request->source_name;
		    $addComment->comment = $request->comment;
		    $addComment->replied_to = ($parent) ? $parent->created_by : null;
		    $addComment->created_by = $user->id;
		    $addComment->save();
            DB::commit();
		
	        return prepareResult(true,getLangByLabelGroups('FollowUp','create') ,$addComment, $this->success);
        }
        catch(Exception $exception) {
            \Log::error($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    }
    public function commentList(Request $request)
    {
        try {
	        $user = getUser();
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query = Comment::select(array('comments.*', DB::raw("(SELECT count(id) from comments WHERE comments.parent_id = comments.id) replyCount")))->whereNull('parent_id')->whereRaw($whereRaw)->with('reply:id,parent_id,comment,created_by')
                ->orderBy('id', 'DESC');
            } else {
                $query = Comment::select(array('comments.*', DB::raw("(SELECT count(id) from comments WHERE comments.parent_id = comments.id) replyCount")))->whereNull('parent_id')->with('reply:id,parent_id,comment,created_by')->orderBy('id', 'DESC');
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
                return prepareResult(true,"Comment list",$pagination,$this->success);
            }
            else
            {
                $query = $query->get();
            }
		    return prepareResult(true,"Comment list",$query,$this->success);
	    }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
            
        }
    	
    }
    private function getWhereRawFromRequest(Request $request) {
        $w = '';
        if (is_null($request->input('source_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "source_id = "."'" .$request->input('source_id')."'".")";
        }
        if (is_null($request->input('source_name')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "source_name = "."'" .$request->input('source_name')."'".")";
        }
        if (is_null($request->input('parent_id')) == false) {
            if ($w != '') {$w = $w . " AND ";}
            $w = $w . "(" . "parent_id = "."'" .$request->input('parent_id')."'".")";
        }
        return($w);

    }
}
