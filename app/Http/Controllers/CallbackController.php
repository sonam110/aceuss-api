<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalInfoDuringIp;
use App\Models\RequestForApproval;
use App\Models\MobileBankIdLoginLog;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\Notification;
use App\Events\BankIdVerified;
use App\Events\NotificationForAll;
use App\Events\EventNotification;

class CallbackController extends Controller
{
    public function verified(Request $request, $person_id, $group_token, $user_id, $from)
    {
        $getPerson = PersonalInfoDuringIp::find(base64_decode($person_id));
        if($getPerson)
        {
            //update status
            $requestForApproval = RequestForApproval::where('group_token', $group_token)
            ->where('requested_to', $getPerson->id)
            ->update([
                'status' => 2
            ]);

            //check all approved
            $checkTotalPerson = RequestForApproval::select(\DB::raw('COUNT(id) as total_request_for_approve'),
            \DB::raw('COUNT(IF(status = 2, 0, NULL)) as total_approved'))
                ->where('group_token', $group_token)
                ->first();

            if($checkTotalPerson->total_request_for_approve == $checkTotalPerson->total_approved)
            {
                $ip_ids = RequestForApproval::where('group_token', $group_token)
                    ->groupBy('request_type_id')
                    ->pluck('request_type_id');

                //update IP as Approved
                PatientImplementationPlan::whereIn('id', $ip_ids)
                ->update([
                    'status' => 1,
                    'approved_date' => date('Y-m-d')
                ]);
            }

            $sessionId = $request->grandidsession;
            $checkSession = mobileBankIdLoginLog::where('sessionId', $sessionId)->count();
            if($checkSession>0)
            {
                return view('verified');
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('BANKIDAPIURL', 'https://client.grandid.com').'/json1.1/GetSession?apiKey='.env('APIKEY', '479fedcee8e6647423d3b4614c25f50b').'&authenticateServiceKey='.env('APISECRET', '18c7f582c64cdf0ae758e2b1e80ae396').'&sessionid='.$sessionId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = 'Accept: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $message = curl_error($ch);
                curl_close($ch);
                \Log::error('mobileID callback error: '.$message);
                return view('not-verified');
            }
            curl_close($ch);
            $resDecode = json_decode($result, true);
            if(!empty($resDecode['errorObject']))
            {
                $message = $resDecode['errorObject']['message'];
                \Log::error('mobileID callback error: '.$message);
                return view('not-verified');
            }
            elseif(empty($resDecode))
            {
                return view('not-verified');
            }

            //BankID log generated here
            $name = $resDecode['userAttributes']['name'];
            $personnel_number = $resDecode['userAttributes']['personalNumber'];
            $ip = $resDecode['userAttributes']['ipAddress'];
            $top_most_parent_id = $getPerson->patient->top_most_parent_id;
            mobileBankIdLoginLog($top_most_parent_id, $sessionId, substr($personnel_number,0,8), $name, $ip, $from);

            //Event Fire here
            $user_id = base64_decode($user_id);
            $userUniqueId = User::select('unique_id')->find($user_id);
            if($userUniqueId)
            {
                //Notification create
                $notification = new Notification;
                $notification->user_id          = $user_id;
                $notification->sender_id        = base64_decode($person_id);
                $notification->type             = 'request-for-approval';
                $notification->status_code      = 'info';
                $notification->title            = 'BankID Request approved';
                $notification->message          = 'BankID IP request has been approved.';
                $notification->read_status      = false;
                $notification->save();

                \broadcast(new EventNotification($notification, $user_id, $userUniqueId->unique_id, null));
            }

            return view('verified');
        }
        else
        {
            return view('not-verified');
        }
        
    }

    public function checkEvent()
    {
        $user = User::first();
        $data = [
            "module" => 'Activity',
            "user_id" => 1,
            "message" => 'test message',
            "message_type" => 'success',
        ];
        //\broadcast(new NotificationForAll($data));
        \broadcast(new BankIdVerified($data, $user, $user->unique_id, 'required'));
        \broadcast(new EventNotification($data, $user->id, $user->unique_id, 'required'));
        //event(new BankIdVerified('Hello World'));
        return 'Success';
    }

    public function checkChilds()
    {
        $getBtachId = User::select('branch_id')->find(15);
        if(!empty($getBtachId->branch_id))
        {
            $data = userChildBranches(User::find($getBtachId->branch_id));
        }
        else
        {
            $data = userChildBranches(User::find(15));
        }
        return $data;
    }
}
