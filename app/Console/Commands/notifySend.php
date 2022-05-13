<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Auth;
use Mail;
use Exception;
use Edujugon\PushNotification\PushNotification;
use App\Models\ActivityAssigne;
use App\Models\User;
use App\Models\Activity;
use App\Models\EmergencyContact;
use Carbon\Carbon;

class notifySend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:activity-notification';

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
        $activityAssigne = ActivityAssigne::where('is_notify','0')->where('status','0')->get();
        foreach ($activityAssigne as $key => $assigne) {
            $activity = Activity::where('id',$assigne->activity_id)->withoutGlobalScope('top_most_parent_id')->first();
            if(!empty($activity)) {
                $emergencyContact = EmergencyContact::where('top_most_parent_id',$assigne->Activity->title)->where('is_default','1')->first();
                $is_push_notify = false;
                $is_text_notify = false;
               // $currentDateTime = Carbon::now()->format('Y-m-d H:i');
                $currentDateTime = '2022-04-26 10:10';
                $dateTime  = null;
                $time = Carbon::parse($activity->start_time);
                if($activity->remind_before_start == true){
                    if($activity->before_is_push_notify  == true ){
                        $is_push_notify = true;
                    }
                    if($activity->before_is_text_notify  == true){
                        $is_text_notify = true;
                    }
                    $dateTime = Carbon::parse($activity->start_time)
                    ->subMinute($activity->before_minutes)
                    ->format('Y-m-d H:i');

                }
                if($activity->remind_after_end  == true ){
                    if($activity->after_is_push_notify  == true){
                        $is_push_notify = true;
                    }
                    if($activity->after_is_text_notify  == true){
                        $is_text_notify = true;
                    }
                    $dateTime = Carbon::parse($activity->start_time)
                    ->addMinutes($activity->after_minutes)
                    ->format('Y-m-d H:i');
                }

                if($activity->is_emergency  == true){
                    $dateTime = Carbon::parse($activity->start_time)
                    ->addMinutes($activity->emergency_minutes)
                    ->format('Y-m-d H:i');

                    $check_company_type = ($assigne->employee) ? json_decode($assigne->employee->company_type_id) : null;
                    if(in_array("3", $check_company_type) == true && $activity->emergency_is_push_notify  == true ){
                        $is_push_notify = true;
                    }
                    if(in_array("3", $check_company_type) == true && $activity->emergency_is_text_notify  == true ){
                        $is_text_notify = true;
                    }
                    
                }

                $getUser = User::select('id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$assigne->user_id)->first();
                $user_type =  $getUser->user_type_id;
                $module =  "";
                $id =  $assigne->id;
                $screen =  "";
                $companyObj = companySetting($getUser->top_most_parent_id);
                $obj  =[
                    "type"=> 'activity',
                    "user_id"=> $getUser->id,
                    "name"=> $getUser->name,
                    "email"=> $getUser->email,
                    "user_type"=> $getUser->user_type_id,
                    "title"=> $activity->title,
                    "patient_id"=> ($activity->Patient)? $activity->Patient->unique_id : null,
                    "start_date"=> $activity->start_date,
                    "start_time"=> $activity->start_time,
                    "company"=>  $companyObj,
                    "company_id"=>  $getUser->top_most_parent_id,

                ];
                
                if(env('IS_NOTIFICATION_ENABLE')== true && ($is_push_notify == true) && ($currentDateTime  ==  $dateTime)){
                     
                        pushNotification('activity',$companyObj,$obj,'1',$module,$id,$screen, 'info');
                        $update_is_notify = ActivityAssigne::where('id',$assigne->id)->update(['is_notify'=>'1']);
                }
                if(env('IS_ENABLED_SEND_SMS')== true && ($is_text_notify == true) && ($currentDateTime  ==  $dateTime) ){
                    sendMessage('activity',$obj,$companyObj);
                }
            }
            
        }
    }



}
