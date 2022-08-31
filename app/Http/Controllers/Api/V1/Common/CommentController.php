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
use App\Models\Activity;
use App\Models\EmailTemplate;
use App\Models\User;

class CommentController extends Controller
{
    public function comment(Request $request)
    {
        try {
	    	$user = getUser();
	    	$validator = Validator::make($request->all(),[
        		'source_id' => 'required',   
        		'source_name' => 'required',   
        		'comment' => 'required',         
        		    
	        ]);
	        if ($validator->fails()) {
            	return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
        	}
            $parent = Comment::where('id', $request->parent_id)->first();
	        $addComment = new Comment;
		    $addComment->parent_id = $request->parent_id;
		    $addComment->source_id = $request->source_id;
		    $addComment->source_name = $request->source_name;
		    $addComment->comment = $request->comment;
		    $addComment->replied_to = ($parent) ? $parent->created_by : null;
		    $addComment->created_by = $user->id;
		    $addComment->save();

            $getCommentInfo = Comment::where('source_id', $request->source_id)
                ->where('source_name', $request->source_name)
                ->count();
            $addComment['total_comment'] = $getCommentInfo;


            if($request->source_name == 'Activity')
            {
                /*-----------Send notification---------------------*/
                $activity = Activity::find($request->source_id);
                $extra_param = ['status'=>$activity->status,'title'=>$activity->title];
                $user = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$activity->top_most_parent_id)->first();
                $data_id =  $activity->id;
                $notification_template = EmailTemplate::where('mail_sms_for', 'activity-comment')->first();
                $variable_data = [
                    '{{name}}'              => $user->name,
                    '{{comment_by}}'        => Auth::User()->name,
                    '{{activity_title}}'    => $activity->title
                ];
                actionNotification($user,$data_id,$notification_template,$variable_data,$extra_param);
            }
	        return prepareResult(true,getLangByLabelGroups('BcCommon','message_create') ,$addComment, config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function commentList(Request $request)
    {
        try {
	        $user = getUser();
            $whereRaw = $this->getWhereRawFromRequest($request);
            if($whereRaw != '') { 
                $query = Comment::select(array('comments.*', DB::raw("(SELECT count(id) from comments WHERE comments.parent_id = comments.id) replyCount")))->whereNull('parent_id')->whereRaw($whereRaw)->with('replyThread')
                ->orderBy('id', 'DESC');
            } else {
                $query = Comment::select(array('comments.*', DB::raw("(SELECT count(id) from comments WHERE comments.parent_id = comments.id) replyCount")))->whereNull('parent_id')->with('replyThread')->orderBy('id', 'DESC');
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
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    private function getWhereRawFromRequest(Request $request) 
    {
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
