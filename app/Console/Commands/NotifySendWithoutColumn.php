<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Auth;
use Mail;
use Exception;
use App\Models\User;
use Edujugon\PushNotification\PushNotification;
use App\Models\Activity;
use App\Models\ActivityAssigne;
use App\Models\EmergencyContact;
use App\Models\EmailTemplate;
use Carbon\Carbon;

class notifySendWithoutColumn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:activity-notification-without-column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send activity notifications to Users.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ///bcp created by khushboo for notify send
        // return date('Y-m-d H:i');
                //   reminder before Activity start   //
        $activities = Activity::where('start_date',date('Y-m-d'))
        ->withoutGlobalScope('top_most_parent_id')
        ->where('remind_before_start', true)
        ->where('is_latest_entry', 1)
        ->get();

        foreach ($activities as $key => $activity) {
            $reminder_time = \Carbon\Carbon::parse($activity->start_time)
            ->subMinute($activity->before_minutes)
            ->format('Y-m-d H:i');

            if(strtotime(date('Y-m-d H:i')) == strtotime($reminder_time))
            {
                foreach ($activity->assignEmployee as $key => $assigne) {
                    $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$assigne->user_id)->first();
                    $data_id =  $activity->id;
                    if($getUser)
                    {
                        $notification_template = EmailTemplate::where('mail_sms_for', 'reminder-before-start')->first();
                        $variable_data = [
                            '{{name}}'              => aceussDecrypt($getUser->name),
                            '{{assigned_by}}'       => aceussDecrypt($assigne->assignedBy->name),
                            '{{activity_title}}'    => $activity->title,
                            '{{start_date}}'        => $activity->start_date,
                            '{{start_time}}'        => $activity->start_time,
                            '{{before_minutes}}'    => $activity->before_minutes
                        ];

                        // Before notify through push notification
                        if($activity->before_is_push_notify  == true ){

                            actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);

                            $update_is_notify = ActivityAssigne::where('id',$assigne->id)->update(['is_notify'=>'1']);
                        }

                        // Before notify through text notification
                        if($activity->before_is_text_notify  == true){
                            sendMessage($getUser,$notification_template,$variable_data);
                        }
                    }
                }
            }
        }
        //-------------------------------------//


        //   reminder on Activity Start   //
        $activities = Activity::where('start_date',date('Y-m-d'))
        ->withoutGlobalScope('top_most_parent_id')
        ->where('in_time', true)
        ->where('is_latest_entry', 1)
        ->get();

        foreach ($activities as $key => $activity) {
            $reminder_time = \Carbon\Carbon::parse($activity->start_time)
            ->format('Y-m-d H:i');
            if(strtotime(date('Y-m-d H:i')) == strtotime($reminder_time))
            {
                foreach ($activity->assignEmployee as $key => $assigne) {
                    $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$assigne->user_id)->first();
                    $data_id =  $activity->id;
                    if($getUser)
                    {
                        $notification_template = EmailTemplate::where('mail_sms_for', 'reminder-activity-start')->first();
                        $variable_data = [
                            '{{name}}'              => aceussDecrypt($getUser->name),
                            '{{assigned_by}}'       => aceussDecrypt($assigne->assignedBy->name),
                            '{{activity_title}}'    => $activity->title,
                            '{{start_date}}'        => $activity->start_date,
                            '{{start_time}}'        => $activity->start_time
                        ];

                        // After notify through push notification
                        if($activity->in_time_is_push_notify  == true ){
                            actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);

                            $update_is_notify = ActivityAssigne::where('id',$assigne->id)->update(['is_notify'=>'1']);
                        }

                        // Before notify through text notification
                        if($activity->in_time_is_text_notify  == true){
                            sendMessage($getUser,$notification_template,$variable_data);
                        }
                    }
                }
            }
        }
        //-------------------------------------//



        //   reminder After Activity End   //
        $activities = Activity::where('end_date',date('Y-m-d'))
        ->withoutGlobalScope('top_most_parent_id')
        ->where('remind_after_end', true)
        ->where('is_latest_entry', 1)
        ->get();

        foreach ($activities as $key => $activity) {
            $reminder_time = \Carbon\Carbon::parse($activity->end_time)
            ->addMinute($activity->after_minutes)
            ->format('Y-m-d H:i');
            if(strtotime(date('Y-m-d H:i')) == strtotime($reminder_time))
            {
                foreach ($activity->assignEmployee as $key => $assigne) {
                    $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$assigne->user_id)->first();
                    $data_id =  $activity->id;
                    if($getUser)
                    {
                        $notification_template = EmailTemplate::where('mail_sms_for', 'reminder-after-end')->first();
                        $variable_data = [
                            '{{name}}'              => aceussDecrypt($getUser->name),
                            '{{assigned_by}}'       => aceussDecrypt($assigne->assignedBy->name),
                            '{{activity_title}}'    => $activity->title,
                            '{{end_date}}'        => $activity->end_date,
                            '{{end_time}}'        => $activity->end_time
                        ];

                        // After notify through push notification
                        if($activity->after_is_push_notify  == true ){
                            actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);

                            $update_is_notify = ActivityAssigne::where('id',$assigne->id)->update(['is_notify'=>'1']);
                        }

                        // Before notify through text notification
                        if($activity->after_is_text_notify  == true){
                            sendMessage($getUser,$notification_template,$variable_data);
                        }
                    }
                }
            }
        }
        //-------------------------------------//


        //   reminder if emergency   //
        $activities = Activity::where('start_date',date('Y-m-d'))
        ->withoutGlobalScope('top_most_parent_id')
        ->where('is_emergency', true)
        ->where('is_latest_entry', 1)
        ->get();

        foreach ($activities as $key => $activity) {
            $reminder_time = \Carbon\Carbon::parse($activity->start_time)
            ->subMinute($activity->emergency_minutes)
            ->format('Y-m-d H:i');

            if(strtotime(date('Y-m-d H:i')) == strtotime($reminder_time))
            {
                foreach ($activity->assignEmployee as $key => $assigne) {
                    $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$assigne->user_id)->first();
                    $data_id =  $activity->id;
                    if($getUser)
                    {
                        $notification_template = EmailTemplate::where('mail_sms_for', 'reminder-before-start')->first();
                        $variable_data = [
                            '{{name}}'              => aceussDecrypt($getUser->name),
                            '{{assigned_by}}'       => aceussDecrypt($assigne->assignedBy->name),
                            '{{activity_title}}'    => $activity->title,
                            '{{start_date}}'        => $activity->start_date,
                            '{{start_time}}'        => $activity->start_time
                        ];

                        // Before notify through push notification
                        if($activity->emergency_is_push_notify  == true ){
                            actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);
                            $update_is_notify = ActivityAssigne::where('id',$assigne->id)->update(['is_notify'=>'1']);
                        }

                        // Before notify through text notification
                        if($activity->emergency_is_text_notify  == true){
                            sendMessage($getUser,$notification_template,$variable_data);
                        }
                    }
                }
            }
        }









                //-------------------------------------//

        // $activityAssigne = ActivityAssigne::where('is_notify','0')->where('status','0')->get();
        // foreach ($activityAssigne as $key => $assigne) {
        //  $activity = Activity::where('id',$assigne->activity_id)->withoutGlobalScope('top_most_parent_id')->first();
        //  if(!empty($activity)) {
        //      $emergencyContact = EmergencyContact::where('top_most_parent_id',$assigne->Activity->title)->where('is_default','1')->first();
        //      $is_push_notify = false;
        //      $is_text_notify = false;
        //      $currentDateTime = Carbon::now()->format('Y-m-d H:i');
     //            //$currentDateTime = '2022-04-26 10:10';
        //      $dateTime  = null;
        //      $time = Carbon::parse($activity->start_time);
        //      if($activity->remind_before_start == true){
        //          if($activity->before_is_push_notify  == true ){
        //              $is_push_notify = true;
        //          }
        //          if($activity->before_is_text_notify  == true){
        //              $is_text_notify = true;
        //          }
        //          $dateTime = Carbon::parse($activity->start_time)
        //          ->subMinute($activity->before_minutes)
        //          ->format('Y-m-d H:i');

        //      }
        //      if($activity->remind_after_end  == true ){
        //          if($activity->after_is_push_notify  == true){
        //              $is_push_notify = true;
        //          }
        //          if($activity->after_is_text_notify  == true){
        //              $is_text_notify = true;
        //          }
        //          $dateTime = Carbon::parse($activity->start_time)
        //          ->addMinutes($activity->after_minutes)
        //          ->format('Y-m-d H:i');
        //      }

        //      if($activity->is_emergency  == true){
        //          $dateTime = Carbon::parse($activity->start_time)
        //          ->addMinutes($activity->emergency_minutes)
        //          ->format('Y-m-d H:i');

        //          $check_company_type = ($assigne->employee) ? json_decode($assigne->employee->company_type_id) : null;
        //          if(in_array("3", $check_company_type) == true && $activity->emergency_is_push_notify  == true ){
        //              $is_push_notify = true;
        //          }
        //          if(in_array("3", $check_company_type) == true && $activity->emergency_is_text_notify  == true ){
        //              $is_text_notify = true;
        //          }

        //      }

        //      $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$assigne->user_id)->first();
        //      $data_id =  $activity->id;

        //      if($getUser)
        //      {
        //          if(($is_push_notify == true) && ($currentDateTime ==  $dateTime))
        //          {
        //              $notification_template = EmailTemplate::where('mail_sms_for', 'activity-assignment')->first();
        //              $variable_data = [
        //                  '{{name}}'              => aceussDecrypt($getUser->name),
        //                  '{{assigned_by}}'       => aceussDecrypt(Auth::User()->name),
        //                  '{{activity_title}}'    => $activity->title,
        //                  '{{start_date}}'        => $activity->start_date,
        //                  '{{start_time}}'        => $activity->start_time
        //              ];
        //              actionNotification($getUser,$data_id,$notification_template,$variable_data);

        //              $update_is_notify = ActivityAssigne::where('id',$assigne->id)->update(['is_notify'=>'1']);
        //          }

        //          $companyObj = companySetting($getUser->top_most_parent_id);
        //          $obj  =[
        //              "type"=> 'activity',
        //              "user_id"=> $getUser->id,
        //              "name"=> aceussDecrypt($getUser->name),
        //              "email"=> aceussDecrypt($getUser->email),
        //              "user_type"=> $getUser->user_type_id,
        //              "title"=> $activity->title,
        //              "patient_id"=> ($activity->Patient)? $activity->Patient->unique_id : null,
        //              "start_date"=> $activity->start_date,
        //              "start_time"=> $activity->start_time,
        //              "company"=>  $companyObj,
        //              "company_id"=>  $getUser->top_most_parent_id,

        //          ];
        //          if(env('IS_ENABLED_SEND_SMS')== true && ($is_text_notify == true) && ($currentDateTime  ==  $dateTime) ){
        //              sendMessage('activity',$obj,$companyObj);
        //          }
        //      }
        //  }
        // }
    }
}
