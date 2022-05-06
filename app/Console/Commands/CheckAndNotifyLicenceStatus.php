<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAndNotifyLicenceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'licence:status';

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
        $fifteenDaysAfter = date('Y-m-d', strtotime('15 days'));
        $oneMonthAfter = date('Y-m-d', strtotime('1 month'));
        $twoMonthAfter = date('Y-m-d', strtotime('2 month'));
        $threeMonthAfter = date('Y-m-d', strtotime('3 month'));
        $dates = [$fifteenDaysAfter, $oneMonthAfter, $twoMonthAfter, $threeMonthAfter];
        $getCompanies = User::select('id','name','email','organization_number','license_end_date')
            ->where('user_type_id', '2')
            ->whereIn('license_end_date', $dates)
            ->get();
        foreach ($getCompanies as $key => $value) {
            $todayDate  = strtotime(date('Y-m-d'));
            $licEndDate = strtotime($value->license_end_date);
            $daysLeft = (($licEndDate - $todayDate)/(60*60*24));

            //Notification send with days left
        }
        return 0;
    }
}
