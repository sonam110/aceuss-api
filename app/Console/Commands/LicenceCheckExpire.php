<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LicenceKeyManagement;

class LicenceCheckExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licence:expire';

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
        $yesterday_date = date('Y-m-d', strtotime('-1 days'));
        $getLicences = LicenceKeyManagement::whereDate('expire_at', $yesterday_date)->get();
        foreach($getLicences as $key => $getLicence)
        {
            //update company id
            $licenceUpdate = User::find($getLicence->top_most_parent_id);
            $licenceUpdate->license_status = 0;
            $licenceUpdate->save();
        }

        return true;
    }
}
