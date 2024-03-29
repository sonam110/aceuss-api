<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalInfoDuringIp;
use App\Models\RequestForApproval;
use App\Models\Journal;
use App\Models\JournalAction;
use App\Models\Deviation;
use App\Models\Schedule;
use App\Models\EmailTemplate;
use App\Models\AgencyWeeklyHour;
use App\Models\MobileBankIdLoginLog;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\Notification;
use App\Events\BankIdVerified;
use App\Events\NotificationForAll;
use App\Events\EventNotification;
use Edujugon\PushNotification\PushNotification;

class CallbackController extends Controller
{
    public function verified(Request $request, $person_id, $user_id, $from, $method)
    {
        sleep(3);
        $isSuccess = false;
        $sessionId = $request->grandidsession;
        $checkSession = mobileBankIdLoginLog::where('sessionId', $sessionId)->whereNotNull('name')->count();
        if($checkSession>0)
        {
            return view('verified');
        }

        $sessionInfo = MobileBankIdLoginLog::where('sessionId', $sessionId)->first();
        $person_id = base64_decode($person_id);
        $user_id = base64_decode($user_id);
        $loggedInUser = User::find($user_id);
        $personalNumber = null;

        if($sessionInfo)
        {
            if(env('IS_MOBILE_BANK_ON', false))
            {
                //get User info
                $ch = curl_init();

                //$method = 1 (Auth) else 2 (Sign)
                if($method==1)
                {
                    curl_setopt($ch, CURLOPT_URL, env('BANKIDAPIURL', 'https://client.grandid.com').'/json1.1/GetSession?apiKey='.env('BANKIDAPIKEY', '479fedcee8e6647423d3b4614c25f50b').'&authenticateServiceKey='.env('BANKIDAPISECRET', '18c7f582c64cdf0ae758e2b1e80ae396').'&sessionid='.$sessionId);
                }
                else
                {
                    curl_setopt($ch, CURLOPT_URL, env('BANKIDSIGNAPIURL', 'https://client.grandid.com').'/json1.1/GetSession?apiKey='.env('BANKIDAPIKEY', '479fedcee8e6647423d3b4614c25f50b').'&authenticateServiceKey='.env('BANKIDSIGNAPISECRET', 'ad462cb0fe1aa1b0adabca6ffffe1d59').'&sessionid='.$sessionId);
                }
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);

                $headers = array();
                $headers[] = 'Accept: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                \Log::info('BankID callback data');
                \Log::info($result);
                curl_close($ch);
                $resDecode = json_decode($result, true);
                if(!empty($resDecode['errorObject']))
                {
                    $message = $resDecode['errorObject']['message'];
                    \Log::error('mobileID callback error:');
                    \Log::error($message);
                }
                elseif(empty($resDecode))
                {
                    \Log::error('mobileID callback error:');
                    \Log::error($resDecode);
                }
                else
                {
                    $name = $resDecode['userAttributes']['name'];
                    $ip = $resDecode['userAttributes']['ipAddress'];
                }
            }
            else
            {
                $faker = \Faker\Factory::create();
                $isSuccess = true;
                $name = $faker->name;
                $ip = $request->ip(); // long2ip(mt_rand());
            }

            if($from=='IP-approval')
            {
                $getPerson = User::select('id','personal_number')->find($person_id);
                if($getPerson)
                {
                    $personalNumber = aceussDecrypt($getPerson->personal_number);
                    $isSuccess = true;
                    //update status
                    $requestForApproval = RequestForApproval::where('group_token', $sessionInfo->extra_info)
                    ->where('requested_to', $getPerson->id)
                    ->update([
                        'status' => '2',
                        'sessionId' => $sessionId
                    ]);

                    //check all approved
                    $checkTotalPerson = RequestForApproval::select(\DB::raw('COUNT(id) as total_request_for_approve'),
                    \DB::raw('COUNT(IF(status = "2", 0, NULL)) as total_approved'))
                        ->where('group_token', $sessionInfo->extra_info)
                        ->first();

                    if($checkTotalPerson->total_request_for_approve == $checkTotalPerson->total_approved)
                    {
                        $ip_ids = RequestForApproval::where('group_token', $sessionInfo->extra_info)
                            ->groupBy('request_type_id')
                            ->pluck('request_type_id');

                        //update IP as Approved
                        PatientImplementationPlan::whereIn('id', $ip_ids)
                        ->update([
                            'status' => 1,
                            'approved_date' => date('Y-m-d')
                        ]);
                    }
                }
            }
            elseif($from=='journal-approval')
            {
                $journal = Journal::where('id', $sessionInfo->extra_info)->update([
                    'is_signed' => 1,
                    'signed_by' => $user_id,
                    'signed_date' => date('Y-m-d'),
                    'sessionId' => $sessionId
                ]);
                $isSuccess = true;
            }
            elseif($from=='journal-action-approval')
            {
                $journalAction = JournalAction::where('id', $sessionInfo->extra_info)->update([
                    'is_signed' => 1,
                    'signed_by' => $user_id,
                    'signed_date' => date('Y-m-d'),
                    'sessionId' => $sessionId
                ]);
                $isSuccess = true;
            }
            elseif($from=='deviation-approval')
            {
                $deviation = Deviation::where('id', $sessionInfo->extra_info)->update([
                    'is_signed' => 1,
                    'is_completed' => 1,
                    'completed_by' => $user_id,
                    'completed_date' => date('Y-m-d'),
                    'sessionId' => $sessionId
                ]);
                $isSuccess = true;
            }
            elseif($from=='schedule-company-approval')
            {
                foreach (json_decode($sessionInfo->extra_info, true) as $key => $id) 
                {
                    $schedule= Schedule::find($id);
                    $user = User::find($schedule->user_id);
                    if($user && $user->report_verify == 'yes')
                    {
                        //----notify-employee-schedule-approved----//
                        $data_id =  $id;
                        $notification_template = EmailTemplate::where('mail_sms_for', 'schedule-approved')->first();
                        $variable_data = [
                            '{{name}}'  => aceussDecrypt($user->name),
                            '{{schedule_title}}'=>$schedule->title,
                            '{{date}}' => $schedule->shift_date,
                            '{{start_time}}'=> $schedule->shift_start_time,
                            '{{end_time}}'=> $schedule->shift_end_time,
                            '{{approved_by}}'=> aceussDecrypt($loggedInUser->name)
                        ];
                        actionNotification($user,$data_id,$notification_template,$variable_data, null, null, true);
                        //--------------------------------------//
                        $schedule->update([
                            'approved_by_company' => 1,
                            'company_sessionId' => $sessionId
                        ]);
                    }
                    else
                    {
                        if(!empty($schedule->patient_id))
                        {
                            $patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)
                            ->where('start_date','>=' ,$schedule->shift_date)
                            ->where('end_date','<=',$schedule->shift_date)
                            ->orderBy('id','desc')->first();
                            if(empty($patientAssignedHours))
                            {
                                $patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)->orderBy('id','desc')->first();
                            }
                            if($patientAssignedHours)
                            {
                                $workedHours = $schedule->scheduled_work_duration + $schedule->emergency_work_duration + $schedule->ob_work_duration + $schedule->extra_work_duration;
                                $completedHours = $patientAssignedHours->completed_hours + $workedHours;
                                $remainingHours = $patientAssignedHours->remaining_hours - $workedHours;
                                $patientAssignedHours->update(['completed_hours'=>$completedHours,'remaining_hours'=>$remainingHours]);
                            }
                        }
                        $schedule->update([
                            'status' => 1,
                            'approved_by_company' => 1,
                            'company_sessionId' => $sessionId
                        ]);
                    }
                }
                $isSuccess = true;
            }
            elseif($from=='schedule-employee-approval')
            {
                foreach (json_decode($sessionInfo->extra_info, true) as $key => $id) 
                {
                    $schedule = Schedule::find($id);
                    if(!empty($schedule->patient_id))
                    {
                        $patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)
                        ->where('start_date','>=' ,$schedule->shift_date)
                        ->where('end_date','<=',$schedule->shift_date)
                        ->orderBy('id','desc')->first();
                        if(empty($patientAssignedHours))
                        {
                            $patientAssignedHours = AgencyWeeklyHour::where('user_id',$schedule->patient_id)->orderBy('id','desc')->first();
                        }
                        $workedHours = $schedule->scheduled_work_duration + $schedule->emergency_work_duration + $schedule->ob_work_duration + $schedule->extra_work_duration;
                        $completedHours = $patientAssignedHours->completed_hours + $workedHours;
                        $remainingHours = $patientAssignedHours->remaining_hours - $workedHours;

                        $patientAssignedHours->update([
                            'completed_hours' => $completedHours,
                            'remaining_hours' => $remainingHours
                        ]);
                    }
                    if($loggedInUser->verification_method =='normal')
                    {
                        $schedule->update([
                            'status' => 1,
                            'verified_by_employee' => 1,
                            'employee_sessionId' => $sessionId
                        ]);
                    }
                    else
                    {
                        //add code for verification by bank_id
                        $schedule->update([
                            'status' => 1,
                            'verified_by_employee' => 1,
                            'employee_sessionId' => $sessionId
                        ]);
                    }
                    $company = User::find($loggedInUser->top_most_parent_id);
                    //----notify-company-schedule-verified----//
                    $exra_param = ['employee_id'=>$schedule->user_id, 'shift_date' => $schedule->shift_date,
                        'shift_start_time'=> $schedule->shift_start_time,
                        'shift_end_time'=> $schedule->shift_end_time,];
                    $data_id =  $id;
                    $notification_template = EmailTemplate::where('mail_sms_for', 'schedule-verified')->first();
                    $variable_data = [
                        '{{name}}'  => aceussDecrypt($company->name),
                        '{{schedule_title}}'=>$schedule->title,
                        '{{date}}' => $schedule->shift_date,
                        '{{start_time}}'=> $schedule->shift_start_time,
                        '{{end_time}}'=> $schedule->shift_end_time,
                        '{{verified_by}}'=> aceussDecrypt($loggedInUser->name)
                    ];
                    actionNotification($company,$data_id,$notification_template,$variable_data,$exra_param, null, true);
                    //--------------------------------------//
                }
                $isSuccess = true;
            }
        }
        
        //Event Fire here
        $userUniqueId = User::select('unique_id')->find($user_id);
        if($isSuccess)
        {
            //update Mobile BankID log
            $sessionInfo->name = $name;
            $sessionInfo->ip = $ip;
            $sessionInfo->save();
            if($userUniqueId)
            {
                //Notification create
                $notification = new Notification;
                $notification->user_id          = $user_id;
                $notification->sender_id        = $person_id;
                $notification->type             = 'request-for-approval';
                $notification->status_code      = 'info';
                $notification->title            = 'BankID Request approved';
                $notification->message          = 'BankID '.$from.' request has been approved.';
                $notification->read_status      = false;
                $notification->save();

                if(env('IS_WS_SOCKET_IN', false))
                {
                    \broadcast(new EventNotification($notification, $user_id, $userUniqueId->unique_id, $from, $personalNumber, false));
                }
            }
            return view('verified');
        }
        else
        {
            $getPerson = User::select('id','personal_number')->find($person_id);
            if($getPerson)
            {
                if(env('IS_WS_SOCKET_IN', false))
                {
                    \broadcast(new EventNotification(null, $user_id, $userUniqueId->unique_id, $from, aceussDecrypt($getPerson->personal_number), true));
                }
            }
            
            return view('not-verified');
        }
    }

    public function checkEvent()
    {
        $user = User::find(2);
        $data = [
            "module" => 'Activity',
            "user_id" => $user->id,
            "message" => 'test message',
            "message_type" => 'success',
        ];
        //$stat = \broadcast(new NotificationForAll($data));
        //$stat = \broadcast(new BankIdVerified($data, $user, $user->unique_id, 'required'));
        $stat = \broadcast(new EventNotification($data, $user->id, $user->unique_id, 'required'));
        //$stat = event(new BankIdVerified('Hello World'));
        dd($stat);
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

    public function checkBankId()
    {
        $arr = json_encode([1,2,3,4]);
        $res = bankIdVerification('7710037933', 1, $arr, 1, 'test', 1, 1, null);
        return $res;
    }

    public function checkNotification(Request $request)
    {
        $push = new PushNotification('fcm');
        $response = $push->setMessage([
                "notification"=>[
                    'title' => (!empty($request->title) ? $request->title : "content_header"),
                    'body'  => (!empty($request->body) ? $request->body : "content_body"),
                    'sound' => 'default',
                    'android_channel_id' => '1',
                    //'timestamp' => date('Y-m-d G:i:s')
                ],
                'data'=>[
                    'id'  => 1,
                    'user_type'  => 1,
                    'module'  => (!empty($request->module) ? $request->module : "module"),
                    'screen'  => (!empty($request->screen) ? $request->screen : "screen"),
                ]                        
            ])
            ->setApiKey(env('FIREBASE_KEY'))
            ->setDevicesToken($request->device_token)
            ->send()
            ->getFeedback();

        return $response;
    }

    public function trigger(Request $request, WebSocketService $service) 
    {
        $service->Send("A trigger was called");
        return response()->json(['success' => 'OK'], $this->successStatus);
    }

    public function checkCompanyType($userId)
    {
        return isShowAllPatient($userId);
    }
}
