<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalInfoDuringIp;
use App\Models\MobileBankIdLoginLog;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Events\BankIdVerified;
use App\Events\NotificationForAll;

class CallbackController extends Controller
{
    public function verified(Request $request, $person_id, $group_token)
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
            curl_setopt($ch, CURLOPT_URL, env('BANKIDAPIURL', 'https://client.grandid.com').'/json1.1/GetSession?apiKey='.env('APIKEY', '945610088ce511434ad87fa50e567c7d').'&authenticateServiceKey='.env('APISECRET', '3e73749b89a9ee32369fa25910c4c4e9').'&sessionid='.$sessionId);
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
            $top_most_parent_id = $getPerson->patient->top_most_parent_id;
            mobileBankIdLoginLog($top_most_parent_id, $sessionId, $personnel_number, $name);

            //Event Fire here

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
        \broadcast(new BankIdVerified($data, $user, $user->unique_id));
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
