<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stampling;
use App\Models\Schedule;
use App\Models\User;
use App\Models\ScheduleStamplingDatewiseReport;

class GenerateStamplingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:stampling-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $date = date('Y-m-d',strtotime('-1 day'));
        $schedules = Schedule::where('shift_date',$date)
        ->where('user_id','!=',null)
        ->groupBy('user_id')
        ->where('is_active',1)
        ->where('leave_applied',0)
        ->get();
        foreach ($schedules as $key => $value) 
        {
            $scheduled_duration = Schedule::select([
                \DB::raw('SUM(scheduled_work_duration) + SUM(extra_work_duration) + SUM(ob_work_duration) + SUM(emergency_work_duration) as scheduled_duration')
            ])
            ->where('user_id',$value->user_id)
            ->where('is_active',1)
            ->where('shift_date',$date)
            ->where('leave_applied',0)
            ->first()->scheduled_duration;

            $stampling_duration = 0;

            $stamplings = Stampling::where('date',$date)
            ->where('user_id',$value->user_id)
            ->get();

            foreach ($stamplings as $key => $stampling) {
                $in_time = $stampling->in_time;
                $out_time = $stampling->out_time;
                $rest_start_time = $stampling->rest_start_time;
                $rest_end_time = $stampling->rest_end_time;
                $ob = getObDuration($date,$in_time,$out_time,$rest_start_time,$rest_end_time);
                $ob_duration = $ob['duration'];

                $rest_duration = 0;

                if(($rest_start_time != null) && ($rest_end_time != null) && ($rest_end_time < $out_time))
                {
                    $rest_duration = timeDifference($rest_start_time,$rest_end_time);
                }
                $stampling_duration = $stampling_duration + (timeDifference($in_time,$out_time) - $rest_duration);
            }

            $ob_duration = Stampling::where('user_id',$value->user_id)
            ->where('date',$date)
            ->sum('total_ob_hours');

            $regular_duration = $stampling_duration - $ob_duration;
            $extra_duration = $regular_duration-$scheduled_duration;



            $insert = new ScheduleStamplingDatewiseReport;
            $insert->user_id = $value->user_id;
            $insert->date = $date;
            $insert->scheduled_duration = $scheduled_duration;
            $insert->stampling_duration = $stampling_duration;
            $insert->ob_duration = $ob_duration;
            $insert->regular_duration = $regular_duration;
            $insert->extra_duration = $extra_duration;
            $insert->save();
        }
        return 0;
    }
}
