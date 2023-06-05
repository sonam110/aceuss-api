<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckAndNotifyLicenceStatus;
use App\Console\Commands\LicenceCheckExpire;
use App\Console\Commands\notifySend;
use App\Console\Commands\taskNotify;
use App\Console\Commands\SystemLogout;
use App\Console\Commands\DatabaseBackUp;
use App\Console\Commands\NotifyBirthday;
use App\Console\Commands\VerifyScheduleReminder;
use App\Console\Commands\AutoVerifySchedule;
use App\Console\Commands\NotifyStamplingStartEndTime;
use App\Console\Commands\GenerateStamplingReport;


class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckAndNotifyLicenceStatus::class,
        Commands\LicenceCheckExpire::class,
        Commands\notifySend::class,
        Commands\taskNotify::class,
        Commands\DatabaseBackUp::class,
        Commands\NotifyBirthday::class,
        Commands\VerifyScheduleReminder::class,
        Commands\AutoVerifySchedule::class,
        Commands\NotifyStamplingStartEndTime::class,
        Commands\GenerateStamplingReport::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('licence:status')->dailyAt('09:00');
        $schedule->command('licence:expire')->dailyAt('00:01');
        $schedule->command('send:activity-notification')->everyMinute();
        $schedule->command('send:task-notification')->dailyAt('00:01');
        $schedule->command('websockets:clean')->daily();
        $schedule->command('database:backup')->dailyAt('00:01');
        $schedule->command('system:logout')->sundays('06:00');
        $schedule->command('notify:birthday')->dailyAt('09:00');
        $schedule->command('verify_schedule:reminder')->dailyAt('09:00');
        $schedule->command('auto_verify:schedule')->monthlyOn(1, '00:01');
        $schedule->command('remind:punchin-punchout')->everyMinute();
        $schedule->command('generate:stampling-report')->dailyAt('09:00');
        
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
