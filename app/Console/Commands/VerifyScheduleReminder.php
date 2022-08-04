<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Edujugon\PushNotification\PushNotification;
use App\Models\Schedule;
use App\Models\EmailTemplate;

class VerifyScheduleReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify_schedule:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder notification to verify schedule';

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
    	$dates = [];
    	$user_ids = [];
    	$last_date = date('Y-m-t');
    	for($i = 0; $i<=2; $i++)
    	{
    		$dates[] = date('Y-m-d',strtotime('-'.$i.' day',strtotime($last_date)));
    	}
    	foreach ($dates as $key => $date) {
    		if($date == date('Y-m-d'))
    		{
    			$schedules = Schedule::whereRaw('MONTH(shift_date) = '.date('m'))
    			->where('verified_by_employee',0)
    			->where('shift_date','<',date('Y-m-d'))
    			->where('is_active',1)
    			->where('user_id','!=',null)
    			->get(['user_id']);
    			foreach ($schedules as $key => $value) {
    				$user = User::find($value->user_id);
    				if($user->report_verify == true)
    				{
    					$notification_template = EmailTemplate::where('mail_sms_for', 'verify-schedule-reminder')->first();
    					$variable_data = [
    						'{{name}}'              => $user->name
    					];
    					actionNotification($user,$user->id,$notification_template,$variable_data);
    				}
    			}
    		}
    	}
        return 0;
    }
}
