<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MessagingController extends Controller
{
    public function getUsers(Request $request)
    {
        try {
            $query = User::select('users.id','users.name')
                    ->where('users.status', 1)
                    ->where('users.id', '!=', auth()->id())
                    ->withCount('unreadMessages');
            
            if(!empty($request->top_most_parent_id))
            {
                $query->where('users.top_most_parent_id', $request->top_most_parent_id);
            }

            if(!empty($request->name))
            {
                $query->where('users.name', 'LIKE', '%'.$request->name.'%');
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

            if(auth()->user()->top_most_parent_id!='1')
            {
                $adminInfo = User::select('users.id', 'users.name')
                    ->where('user_type_id', 1)
                    ->withoutGlobalScope('top_most_parent_id')
                    ->first();
                $getAdminCount = Message::where('sender_id', $adminInfo->id)
                    ->where('receiver_id', auth()->id())
                    ->whereNull('read_at')
                    ->withoutGlobalScope('top_most_parent_id')
                    ->count();
                $query[$query->count()] = [
                    'id' => $adminInfo->id, 
                    'name' => $adminInfo->name,
                    'unread_messages_count' => $getAdminCount
                ];
            }
            
            return prepareResult(true,'Users List' ,$query, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function getMessages(Request $request)
    {
        try {
            $query = Message::with('sender:id,name,gender,user_type_id', 'receiver:id,name,gender,user_type_id', 'sender.UserType:id,name', 'receiver.UserType:id,name')
            ->where(function($q) {
                $q->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            });

            if(!empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereBetween('created_at', [$request->from_date, $request->end_date]);
            }
            else
            {
                // last 7 days
                $query->whereBetween('created_at',[(new Carbon)->subDays(7)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()]);
            }

            $query = $query->get();

            //if message count is less than 20 then load all messages
            if($query->count()<20)
            {
                $query = Message::with('sender:id,name,gender,user_type_id', 'receiver:id,name,gender,user_type_id', 'sender.UserType:id,name', 'receiver.UserType:id,name')
                ->where(function($q) {
                    $q->where('sender_id', auth()->id())
                        ->orWhere('receiver_id', auth()->id());
                })
                ->get();
            }
            
            return prepareResult(true,'messsages List' ,$query, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
