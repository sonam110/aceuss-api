<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Log;
use Auth;
use Mail;
use Exception;
use Edujugon\PushNotification\PushNotification;
class notifySend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usersend:notification {user} {type} {msg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to Users.';

   

    const FIREBASE_API_KEY = 'AAAAagFpi5A:APA91bHTETyPzIHVozCtF8TJklpF7dTnkXigTL_BbIcs-3o29fbH7YGEFOh6adAJ1wfMTOCKxkHm9dOvTbvgdJJH5WpuEX1nS3WynlDtAjLiB3db-qZq5JK8LD3bFM9jPeiPEqj6EUoq';
    //const FIREBASE_API_KEY = 'AIzaSyARjklTTBau5w2a0LzVH46Lx5SqfdQtFV4';
    const FIREBASE_SENDER_ID = '455290227600';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }

     /*new function for send FCM & APN notification*/
    public function send_method_in_apn_service($userlist, $title, $message,$sound)
    {
        $push = new PushNotification('apn');
        $is_sound = ($sound == true) ? 'default' :'false';
        $message = [
            'aps' => [
                'alert' => [
                    'title' => $title,
                    'body'  => $message
                ],
                'sound' => $is_sound
            ]
        ];
        $push->setMessage($message)
            ->setDevicesToken($userlist);
        $push = $push->send(); 
       
        return $push->getFeedback()->tokenFailList;
    }

    public function send_method_in_fcm_service($userlist, $title, $message,$sound)
    {
        $push = new PushNotification('fcm');
        $is_sound = ($sound == true) ? 'default' :'false';
        $push->setMessage([
            'notification' => [
                'title' => $title,
                'body'  => $message,
                'sound' => $is_sound
                ]
            ])
            ->setApiKey(self::FIREBASE_API_KEY)
            ->setDevicesToken($userlist)
            ->send();
           
        return $push->getFeedback()->results;
    }


}
