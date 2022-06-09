<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;

class CreateDirectory extends Command
{
    protected $signature = 'create:directory';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        File::ensureDirectoryExists('public/export');
        File::ensureDirectoryExists('public/reports');
        File::ensureDirectoryExists('public/reports/deviations');
        File::ensureDirectoryExists('public/reports/followups');
        File::ensureDirectoryExists('public/reports/ip');
        File::ensureDirectoryExists('public/reports/journals');
        File::ensureDirectoryExists('public/sample');
        File::ensureDirectoryExists('public/uploads');
        File::ensureDirectoryExists('storage/db-backups');
        return true;
    }
}
