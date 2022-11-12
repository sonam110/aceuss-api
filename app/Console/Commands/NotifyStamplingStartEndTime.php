<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stampling;
use App\Models\Schedule;
use App\Models\User;
use App\Models\EmailTemplate;


class NotifyStamplingStartEndTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind:punchin-punchout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'punch in - punch out reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $check_time = date('Y-m-d H:i:00',strtotime('+10 minutes'));
        $schedules = Schedule::where('shift_date','>=',date('Y-m-d'))
        ->where('user_id','!=',null)
        ->where('is_active',1)
        ->get();
        foreach ($schedules as $key => $value) {
            $user = User::find($value->user_id);
            $data_id = $value->id;
            if($value->shift_start_time == $check_time)
            {
                $notification_template = EmailTemplate::where('mail_sms_for', 'punch-in-reminder')->first();
                $variable_data = [
                   '{{name}}'      => aceussDecrypt($user->name),
                   '{{shift_start_time}}'     => $value->shift_start_time
               ];
               actionNotification($user,$data_id,$notification_template,$variable_data);
            }
            if($value->shift_end_time == $check_time)
            {
                $notification_template = EmailTemplate::where('mail_sms_for', 'punch-out-reminder')->first();
                $variable_data = [
                   '{{name}}'      => aceussDecrypt($user->name),
                   '{{shift_end_time}}'     => $value->shift_end_time
               ];
               actionNotification($user,$data_id,$notification_template,$variable_data);
            }
        }
        return 0;
    }
}
