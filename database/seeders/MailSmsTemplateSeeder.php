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
        $smsTemplate->sms_body = "Dear {{name}}, New task {{title}} is assigned to you. start at
		{{start_date}}
		{{start_time}}.";
        $smsTemplate->notify_body = "Dear {{name}}, New task {{title}} is assigned to you. start at
		{{start_date}}
		{{start_time}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{title}},{{patient_id}},{{start_date}},{{start_time}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}";
        $smsTemplate->save();




        //Template for notification
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity';
        $smsTemplate->mail_subject = 'Activity Notification';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Activity Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
        $smsTemplate->custom_attributes = "";
        $smsTemplate->save();

        // $smsTemplate = new EmailTemplate;
        // $smsTemplate->mail_sms_for = 'Request approval';
        // $smsTemplate->mail_subject = 'Request for approval Notification';
        // $smsTemplate->sms_body = "";
        // $smsTemplate->notify_body = "Request for approval Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
        // $smsTemplate->custom_attributes = "";
        // $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'request-approval';
        $smsTemplate->mail_subject = 'New Approval Request';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Request for approval Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
        $smsTemplate->custom_attributes = "";
        $smsTemplate->save();


        /*-------------------------Added by khushboo------------------------*/

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-assignment';
        $smsTemplate->mail_subject = 'New Activity Assigned';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Activity {{activity_title}} starts at {{start_date}}   {{start_time}}  is assigned to you  by {{assigned_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{activity_title}},{{start_date}},{{start_time}},{{assigned_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-action';
        $smsTemplate->mail_subject = 'Activity Action Performed';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Action {{action}} is performed on Activity {{activity_title}}   starts at {{start_date}} {{start_time}} by {{action_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{start_date}}, {{start_time}}, {{action_by}}, {{activity_title}}, {{action}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-comment';
        $smsTemplate->mail_subject = 'Activity comment posted';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, comment is posted on Activity {{activity_title}} by {{comment_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{activity_title}}},{{comment_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'journal';
        $smsTemplate->mail_subject = 'Journal Created';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Journal is created by {{created_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{created_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'deviation';
        $smsTemplate->mail_subject = 'Deviation Created';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Deviation is created by {{created_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{created_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task-created-assigned';
        $smsTemplate->mail_subject = 'Task Created And Assigned';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Tasks {{task_title}} is  created and assigned successfully.";
        $smsTemplate->custom_attributes = "{{name}},{{task_title}}";
        $smsTemplate->save();


        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task-assignment';
        $smsTemplate->mail_subject = 'New Task Assigned';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Tasks {{task_title}} is  assigned to by {{assigned_by}}.";
        $smsTemplate->custom_attributes = "{{name}},{{task_title}},{{assigned_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task-action';
        $smsTemplate->mail_subject = 'Task Action Performed';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Action {{action}} is performed on {{task_title}} by {{action_by}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{task_title}},{{action_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'schedule-assignment';
        $smsTemplate->mail_subject = 'New scheduled Assigned';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Schedule {{schedule_title}} on {{date}} starts at    {{start_time}} ends at {{end_time}}  is assigned to you  by {{assigned_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{schedule_title}},{{date}},{{start_time}},{{assigned_by}},{end_time}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'schedule-request';
        $smsTemplate->mail_subject = 'New schedule  Request';
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Schedule for dates {{dates}}  requested to you  by {{requested_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{requested_by}}";
        $smsTemplate->save();

    }
}
