<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalInfoDuringIp;
use App\Models\MobileBankIdLoginLog;

class CallbackController extends Controller
{
    public function verified(Request $request, $person_id)
    {
        $getPerson = PersonalInfoDuringIp::find(base64_decode($person_id));
        if($getPerson)
        {
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
}
