<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckAndNotifyLicenceStatus;
use App\Console\Commands\LicenceCheckExpire;
use App\Console\Commands\notifySend;
use App\Console\Commands\taskNotify;
use App\Console\Commands\DatabaseBackUp;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CheckAndNotifyLicenceStatus::class,
        Commands\LicenceCheckExpire::class,
        Commands\notifySend::class,
        Commands\taskNotify::class,
        Commands\DatabaseBackUp::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('licence:status')->dailyAt('09:00');
        $schedule->command('licence:expire')->dailyAt('00:01');
        $schedule->command('send:activity-notification')->dailyAt('00:01');
        $schedule->command('send:task-notification')->dailyAt('00:01');
        $schedule->command('websockets:clean')->daily();
        $schedule->command('database:backup')->dailyAt('00:01');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
