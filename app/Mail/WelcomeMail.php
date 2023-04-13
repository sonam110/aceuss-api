<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $userInfo;

    public function __construct($userInfo)
    {
        $this->userInfo = $userInfo;
    }

   
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $userInfo = $this->userInfo ;
        $companyObj = companySetting($userInfo['company_id']);
        $template = 'welcome-mail';
        $getTemplate = getTemplate($template,$companyObj,$userInfo, null);
     
        $mailObj = [
            'company'       => $companyObj,
            'name'          => aceussDecrypt($userInfo['name']),
            'email'         => aceussDecrypt($userInfo['email']),
            'password'      => $userInfo['password'],
            'id'            => $userInfo['id'],
            'content'       => $getTemplate,
            'template_for'  => $template,
       ];
        return $this->markdown('email.welcome-mail')
            ->subject($getTemplate['subject'])
            ->with('userInfo', $this->userInfo)
            ->with('data', $mailObj);
    }
}
