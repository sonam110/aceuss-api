<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Str;
use DB;
use Auth;
use Log;
use Edujugon\PushNotification\PushNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $query =  Notification::where('user_id',Auth::id())->orderBy('id','DESC');
            if($request->mark_all_as_read == 'true' || $request->mark_all_as_read == 1)
            {
                Notification::where('user_id',Auth::id())->update(['read_status' => 1]);
            }
            if($request->read_status)
            {
                $query->where('read_status',$request->read_status);
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
            return prepareResult(true,getLangByLabelGroups('Notification','message_list'), $query,config('httpcodes.success'));
        }
        catch (\Throwable $exception) 
        {
            DB::rollback();
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function store(Request $request)
    {        
        $validation = Validator::make($request->all(), [
            'message'  => 'required'
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->messages()), [], config('http_response.bad_request'));
        }

        DB::beginTransaction();
        try
        {
            $notification = new Notification;
            $notification->user_id              = 2;
            $notification->sender_id            = Auth::id();
            $notification->device_id            = $request->device_id;
            $notification->device_platform      = $request->device_platform;
            $notification->type                 = $request->type;
            $notification->status_code          = $request->status_code;
            $notification->title                = $request->title;
            $notification->sub_title            = $request->sub_title;
            $notification->message              = $request->message;
            $notification->image_url            = $request->image_url;
            $notification->read_status          = false;
            $notification->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('Notification','message_create'), $notification,config('httpcodes.success'));
        }
        catch (\Throwable $exception)
        {
            DB::rollback();
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function show(Notification $notification)
    {
        return prepareResult(true,getLangByLabelGroups('Notification','message_show'), $notification,config('httpcodes.success'));
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return prepareResult(true,getLangByLabelGroups('Notification','message_delete'), [],config('httpcodes.success'));
    }

    public function read($id)
    {
        try
        {
            $notification = Notification::find($id);
            $notification->update(['read_status' => true]);
            return prepareResult(true,getLangByLabelGroups('Notification','message_read'), $notification,config('httpcodes.success'));
        }
        catch (\Throwable $exception)
        {
            DB::rollback();
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function userNotificationReadAll()
    {
        try
        {
            Notification::where('user_id', Auth::id())->update(['read_status' => true]);
            return prepareResult(true,getLangByLabelGroups('Notification','message_read'), [],config('httpcodes.success'));
        }
        catch (\Throwable $exception)
        {
            DB::rollback();
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function userNotificationDelete()
    {
        try
        {
            Notification::where('user_id', Auth::id())->delete();
            return prepareResult(true,getLangByLabelGroups('Notification','message_delete'), [],config('httpcodes.success'));
        }
        catch (\Throwable $exception)
        {
            DB::rollback();
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function unreadNotificationsCount()
    {
        try
        {
            
            $count = Notification::where('user_id',Auth::id())->where('read_status',0)->count();
            return prepareResult(true,getLangByLabelGroups('Notification','message_count'), $count,config('httpcodes.success'));
        }
        catch (\Throwable $exception)
        {
            DB::rollback();
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function notificationCheck(Request $request)
    {
        $push = new PushNotification('fcm');
        $push->setMessage([
            "notification"=>[
                'title' => 'Testing Title',
                'body'  => 'Testing Body',
                'sound' => 'default',
                'android_channel_id' => '1',
                //'timestamp' => date('Y-m-d G:i:s')
            ],
            'data'=>[
                'id'  => 1,
                'user_type'  => 'Company',
                'module'  => 'Activity',
                'screen'  => 'home'
            ]                        
        ])
        ->setApiKey(env('FIREBASE_KEY'))
        ->setDevicesToken($request->device_token)
        ->send();

        return prepareResult(true,$push->getFeedback(), [],config('httpcodes.success'));
    }
}