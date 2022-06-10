<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DateTime;

class DatabaseBackUp extends Command
{
    protected $signature = 'database:backup';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today    = new DateTime(date('Y-m-d'));
        $day_name = strtolower($today->format('l'));
        $filename = "backup-" . $day_name . ".sql";
        $command = "".env('DUMP_PATH', 'mysqldump')." --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  > " . storage_path() . "/db-backups/" . $filename;

        $returnVar = NULL;
        $output = NULL;

        exec($command, $output, $returnVar);

        //Delete old database backup file
        $before2day = date('Y-m-d', strtotime("-3 days"));
        $deleteFileDate = new DateTime($before2day);
        $old_day_name = strtolower($deleteFileDate->format('l'));
        $oldFile = "backup-" . $old_day_name . ".sql";
        if(file_exists(storage_path() . "/db-backups/" . $oldFile)) 
        { 
            unlink(storage_path() . "/db-backups/" . $oldFile);
        }
    }
}
