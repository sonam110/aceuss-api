<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Schedule;
use App\Models\OVHour;
use App\Models\Stampling;

class SystemLogout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:logout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'logout logged in Users';

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
        $stamplings = Stampling::where('out_time',NULL)->get();
        foreach ($stamplings as $key => $value) {
            $schedule = Schedule::find($value->schedule_id);
            $in_time = $value->in_time;
            $out_time = $schedule->shift_end_time;

            $ob = getObDuration($date,$in_time,$out_time);
            $ob_duration = $ob['duration'];

            // $ob = OVHour::where('date',$value->date)->orWhere('date','')->orderBy('id','desc')->first();
            // if($ob)
            // {
            //     $ob_start_time = $value->date.' '.$ob->start_time;
            //     $ob_end_time = $value->date.' '.$ob->end_time;
            //     $ob_duration = getObDuration($in_time,$out_time,$ob_start_time,$ob_end_time);
            // }
            // else
            // {
            //     $ob_start_time = null;
            //     $ob_end_time = null;
            //     $ob_duration = 0;
            // }


            $scheduled_duration = timeDifference($schedule->shift_start_time,$schedule->shift_end_time);
            $total_worked_duration = timeDifference($in_time,$out_time);
            $countable_scheduled_duration = $total_worked_duration - $ob_duration;
            $extra_duration =  0;
            if($countable_scheduled_duration > $scheduled_duration)
            {
                $extra_duration =  $countable_scheduled_duration - $scheduled_duration;
            }

            $total_schedule_hours = $countable_scheduled_duration/60;
            $total_ob_hours = $ob_duration/60;
            $total_extra_hours = $extra_duration/60;

            $working_percent = calculatePercentage($total_worked_duration, $scheduled_duration);

            $value->out_time                = $out_time;
            $value->out_location            = $value->in_location;
            $value->reason_for_early_out    = null;
            $value->reason_for_late_out     = null;
            $value->is_extra_hours_approved = "0";
            $value->total_schedule_hours    = $total_schedule_hours;
            $value->total_extra_hours       = $total_extra_hours;
            $value->total_ob_hours          = $total_ob_hours;
            $value->ob_type                 = $ob['type'];
            $value->working_percent         = $working_percent;
            $value->logout_by               = 'system';
            $value->entry_mode              = 'Web';
            $value->save();
        }
        return 0;
    }
}
