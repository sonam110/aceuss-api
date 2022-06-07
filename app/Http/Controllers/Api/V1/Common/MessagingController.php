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
            $query = User::select('users.id', 'users.name', 'users.avatar','users.user_type_id')
                ->where('users.status', 1)
                ->where('users.id', '!=', auth()->id())
                ->with('UserType:id,name');

            if (!empty($request->top_most_parent_id)) {
                $query->where('users.top_most_parent_id', $request->top_most_parent_id);
            }

            if (!empty($request->name)) {
                $query->where('users.name', 'LIKE', '%' . $request->name . '%');
            }

            if (!empty($request->perPage)) {
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
                foreach ($result as $key => $user) 
                {
                    $data = Message::select(DB::raw("(SELECT count(*) from messages WHERE messages.receiver_id = ".auth()->id()." AND messages.sender_id = ".$user->id." AND messages.read_at IS NULL) unread_messages_count"))->first();
                    $result[$key]['unread_messages_count'] = $data->unread_messages_count;
                    
                }

                if (auth()->user()->top_most_parent_id != '1') {
                    $adminInfo = User::select('users.id', 'users.name', 'users.avatar','users.user_type_id')
                        ->where('user_type_id', 1)
                        ->withoutGlobalScope('top_most_parent_id')
                        ->with('UserType:id,name')
                        ->first();
                    $getAdminCount = Message::where('sender_id', $adminInfo->id)
                        ->where('receiver_id', auth()->id())
                        ->whereNull('read_at')
                        ->withoutGlobalScope('top_most_parent_id')
                        ->count();
                    $result[$result->count()] = [
                        'id' => $adminInfo->id,
                        'name' => $adminInfo->name,
                        'avatar' => $adminInfo->avatar,
                        'user_type_id' => $adminInfo->user_type_id,
                        'unread_messages_count' => $getAdminCount,
                        'user_type' => [
                            'id' => $adminInfo->UserType->id,
                            'name' => $adminInfo->UserType->name,
                        ]
                    ];
                }
            } else {
                $query = $query->get();

                foreach ($query as $key => $user) {
                    $data = Message::select(DB::raw("(SELECT count(*) from messages WHERE messages.receiver_id = ".auth()->id()." AND messages.sender_id = ".$user->id." AND messages.read_at IS NULL) unread_messages_count"))->first();
                    $query[$key]['unread_messages_count'] = $data->unread_messages_count;
                }

                if (auth()->user()->top_most_parent_id != '1') {
                    $adminInfo = User::select('users.id', 'users.name', 'users.avatar','users.user_type_id')
                        ->where('user_type_id', 1)
                        ->with('UserType:id,name')
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
                        'avatar' => $adminInfo->avatar,
                        'user_type_id' => $adminInfo->user_type_id,
                        'unread_messages_count' => $getAdminCount,
                        'user_type' => [
                            'id' => $adminInfo->UserType->id,
                            'name' => $adminInfo->UserType->name,
                        ]
                    ];
                }
            }

            return prepareResult(true, 'Users List', $query, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(), [], config('httpcodes.internal_server_error'));
        }
    }

    public function getUsersWithLatestMessage(Request $request)
    {
        try {
            $query = Message::with('sender:id,name,gender,user_type_id,avatar', 'sender.UserType:id,name', 'receiver:id,name,gender,user_type_id,avatar', 'receiver.UserType:id,name')
                ->orderBy('id', 'DESC')
                ->where(function($q){
                    $q->where('sender_id', auth()->id())
                        ->orWhere('receiver_id', auth()->id());
                })
                ->get()
                ->unique('sender_id');

            foreach ($query as $key => $user) {
                $data = Message::select(DB::raw("(SELECT count(*) from messages WHERE messages.receiver_id = ".auth()->id()." AND messages.sender_id = ".$user->sender_id." AND messages.read_at IS NULL) unread_messages_count"))->first();
                $query[$key]['unread_messages_count'] = $data->unread_messages_count;
            }

            return prepareResult(true, 'Users List', $query, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(), [], config('httpcodes.internal_server_error'));
        }
    }

    public function getMessages(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $query = Message::with('sender:id,name,gender,user_type_id,avatar', 'receiver:id,name,gender,user_type_id,avatar', 'sender.UserType:id,name', 'receiver.UserType:id,name')
                ->whereIn('sender_id', [auth()->id(), $user_id])
                ->whereIn('receiver_id', [auth()->id(), $user_id]);

            if (!empty($request->from_date) && !empty($request->end_date)) {
                $query->whereBetween('created_at', [$request->from_date.' 00:00:00', $request->end_date.' 23:59:59']);
            } else {
                // last 7 days
                $query->whereBetween('created_at', [(new Carbon)->subDays(7)->startOfDay()->toDateString(), (new Carbon)->now()->endOfDay()->toDateString()]);
            }

            $query = $query->orderBy('id', 'ASC')->get();
            //return $query;

            //if message count is less than 20 then load all messages
            if ($query->count() < 20) {
                $query = Message::with('sender:id,name,gender,user_type_id,avatar', 'receiver:id,name,gender,user_type_id,avatar', 'sender.UserType:id,name', 'receiver.UserType:id,name')
                    ->whereIn('sender_id', [auth()->id(), $user_id])
                    ->whereIn('receiver_id', [auth()->id(), $user_id])
                    ->orderBy('id', 'ASC')
                    ->get();
            }

            return prepareResult(true, 'messsages List', $query, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(), [], config('httpcodes.internal_server_error'));
        }
    }
}
