<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
class MailSmsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         EmailTemplate::truncate();
         //Mail Template
        $mailTemplate = new EmailTemplate;
        $mailTemplate->mail_sms_for = 'forgot-password';
        $mailTemplate->mail_subject = "Forgot Password";
        $mailTemplate->mail_body = "This email is to confirm a recent password reset request for your account. To confirm this request and reset your password, {{message}}:";
        $mailTemplate->custom_attributes = "{{name}}, {{email}},{{token}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}{{message}}";
        $mailTemplate->save();

        $mailTemplate = new EmailTemplate;
        $mailTemplate->mail_sms_for = 'welcome-mail';
        $mailTemplate->mail_subject = "Welcome to Aceuss System";
        $mailTemplate->mail_body = "Dear {{name}}, welocome to {{company_email}} Please change your password into website/App for your future safety.";
        $mailTemplate->custom_attributes = "{{name}}, {{email}},{{contact_number}},{{city}},{{address}},{{zipcode}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}";
        $mailTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity';
        $smsTemplate->mail_subject = 'New Activity Assigned';
        $smsTemplate->sms_body = "Dear {{name}}, New Activity {{title}} is assigne to you  for patient id
		{{patient_id}} Activity start at
		{{start_date}}
		{{start_time}}.";
        $smsTemplate->notify_body = "Dear {{name}}, New Activity {{title}} is assigned to yout  for patient id
		{{patient_id}} Activity start at
		{{start_date}}
		{{start_time}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{title}},{{patient_id}},{{start_date}},{{start_time}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task';
        $smsTemplate->mail_subject = 'New Task Assigned';
        $smsTemplate->sms_body = "Dear {{name}}, New task {{title}} is assigne task to your and  start at
		{{start_date}}
		{{start_time}}.";
        $smsTemplate->notify_body = "Dear {{name}}, New task {{title}} is assigne to you and  task start at
		{{start_date}}
		{{start_time}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{title}},{{patient_id}},{{start_date}},{{start_time}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}";
        $smsTemplate->save();
    }
}
