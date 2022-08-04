<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Edujugon\PushNotification\PushNotification;
use App\Models\Schedule;
use App\Models\EmailTemplate;

class AutoVerifySchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto_verify:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'verify schedules automatically';

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
        $month = date('m',strtotime('-1 months'));
        $schedules = Schedule::whereRaw('MONTH(shift_date) = '.$month)
        ->where('verified_by_employee',0)
        ->where('shift_date','<',date('Y-m-d'))
        ->where('is_active',1)
        ->where('user_id','!=',null)
        ->get();
        foreach ($schedules as $key => $value) {
            $user = User::find($value->user_id);
            if($user->report_verify == true)
            {
                $value->update(['verified_by_employee'=>1,'status'=>1]);
            }
        }
        return 0;
    }
}
