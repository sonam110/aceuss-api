<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class NotifyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:birthday';

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
        $users =User::where('status',1)->get(['id','name','user_type_id','personal_number']);
        foreach ($users as $key => $user) {
            $date_of_birth    = date('Y-m-d', strtotime(substr($user->personal_number,0,8)));
            if($date_of_birth == date('Y-m-d'))
            {
                $notification_template = EmailTemplate::where('mail_sms_for', 'birthday-wish')->first();
                $variable_data = [
                    '{{name}}'              => $user->name
                ];
                actionNotification($user,$user->id,$notification_template,$variable_data);
            }
        }
        return true;
    }
}
