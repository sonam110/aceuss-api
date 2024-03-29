<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Auth;
use Mail;
use Exception;
use App\Models\User;
use Edujugon\PushNotification\PushNotification;
use App\Models\Task;
use App\Models\AssignTask;
use App\Models\EmergencyContact;
use App\Models\EmailTemplate;
use Carbon\Carbon;
class taskNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:task-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send task notifications to Users';

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
         $taskAssigned = AssignTask::where('is_notify','0')->where('status','0')->get();
        foreach ($taskAssigned as $key => $assigne) {
            $task = Task::where('id',$assigne->task_id)->withoutGlobalScope('top_most_parent_id')->first();
            if(!empty($task)) {
                //$emergencyContact = EmergencyContact::where('top_most_parent_id',$assigne->task->top_most_parent_id)->where('is_default','1')->first();
                $is_push_notify = false;
                $is_text_notify = false;
                $currentDateTime = Carbon::now()->format('Y-m-d H:i');
                $dateTime  = null;
                $time = Carbon::parse($task->start_time);
                if($task->remind_before_start  == true){
                    if($task->before_is_push_notify  == true){
                        $is_push_notify = true;
                    }
                    if($task->before_is_text_notify  == true){
                        $is_text_notify = true;
                    }
                    $dateTime = Carbon::parse($task->start_time)
                    ->subMinute($task->before_minutes)
                    ->format('Y-m-d H:i');

                }
                if($task->remind_after_end  == true ){
                    if($task->after_is_push_notify  == true){
                        $is_push_notify = true;
                    }
                    if($task->after_is_text_notify  == true){
                        $is_text_notify = true;
                    }
                    $dateTime = Carbon::parse($task->start_time)
                    ->addMinutes($task->after_minutes)
                    ->format('Y-m-d H:i');
                }
                if($task->is_emergency  == true){
                    $dateTime = Carbon::parse($task->start_time)
                    ->addMinutes($task->emergency_minutes)
                    ->format('Y-m-d H:i');

                    $check_company_type = ($assigne->employee) ? json_decode($assigne->employee->company_type_id) : null;
                    if(in_array("3", $check_company_type) == true && $task->emergency_is_push_notify  == true ){
                        $is_push_notify = true;
                    }
                    if(in_array("3", $check_company_type) == true && $task->emergency_is_text_notify  == true){
                        $is_text_notify = true;
                    }
                }

                $getUser = User::select('id','unique_id','name','email','user_type_id','top_most_parent_id','contact_number')->where('id',$assigne->user_id)->first();
                $data_id = $task->id;

                if($getUser)
                {
                   if($currentDateTime  ==  $dateTime)
                   {
                       $notification_template = EmailTemplate::where('mail_sms_for', 'task-assignment')->first();
                       $variable_data = [
                           '{{name}}'              => aceussDecrypt($getUser->name),
                           '{{assigned_by}}'       => aceussDecrypt($task->createdBy->name),
                           '{{task_title}}'        => $task->title
                       ];
                       if($is_push_notify = true){
                         actionNotification($getUser,$data_id,$notification_template,$variable_data, null, null, true);
                       }
                       
                       $update_is_notify = AssignTask::where('id',$assigne->id)->update(['is_notify'=>'1']);
                       if($is_text_notify == true){
                           sendMessage($getUser,$notification_template,$variable_data);
                       }
                   }
                }
            }
        }
    }
}
