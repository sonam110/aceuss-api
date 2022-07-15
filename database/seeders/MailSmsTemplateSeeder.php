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
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'activity';
        $smsTemplate->event = 'assigned';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "Dear {{name}}, New Activity {{title}} is assigne to you  for patient id {{patient_id}} Activity start at {{start_date}}
        {{start_time}}.";
        $smsTemplate->notify_body = "Dear {{name}}, New Activity {{title}} is assigned to yout  for patient id {{patient_id}} Activity start at {{start_date}} {{start_time}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{title}},{{patient_id}},{{start_date}},{{start_time}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->module = 'task';
        $smsTemplate->mail_sms_for = 'task';
        $smsTemplate->mail_subject = 'New Task Assigned';
        $smsTemplate->type = 'task';
        $smsTemplate->event = 'assigned';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "Dear {{name}}, New task {{title}} is assigned to you. start at {{start_date}} {{start_time}}.";
        $smsTemplate->notify_body = "Dear {{name}}, New task {{title}} is assigned to you. start at {{start_date}} {{start_time}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{title}},{{patient_id}},{{start_date}},{{start_time}},{{company_name}},{{company_logo}},{{company_email}},{{company_contact}},{{company_address}}";
        $smsTemplate->save();





        $smsTemplate = new EmailTemplate;//Template for notification
        $smsTemplate->mail_sms_for = 'activity';
        $smsTemplate->mail_subject = 'Activity Notification';
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'activity';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
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
        $smsTemplate->module = 'request-approval';
        $smsTemplate->type = 'request-approval';
        $smsTemplate->event = 'request-approval';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Request for approval Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s";
        $smsTemplate->custom_attributes = "";
        $smsTemplate->save();


        /*-------------------------Added by khushboo------------------------*/
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-assignment';
        $smsTemplate->mail_subject = 'New Activity Assigned';
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'activity';
        $smsTemplate->event = 'assigned';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Activity {{activity_title}} starts at {{start_date}}   {{start_time}}  is assigned to you  by {{assigned_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{activity_title}},{{start_date}},{{start_time}},{{assigned_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-done';
        $smsTemplate->mail_subject = 'Activity Marked As Done';
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'activity';
        $smsTemplate->event = 'activity-marked-done';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Activity {{activity_title}} is Marked as done  starts at {{start_date}} {{start_time}} by {{action_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{start_date}}, {{start_time}}, {{action_by}}, {{activity_title}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-not-done';
        $smsTemplate->mail_subject = 'Activity Marked As Not Done';
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'activity';
        $smsTemplate->event = 'activity-marked-not-done';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'danger';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Activity {{activity_title}} is Marked as not done  starts at {{start_date}} {{start_time}} by {{action_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{start_date}}, {{start_time}}, {{action_by}}, {{activity_title}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-not-applicable';
        $smsTemplate->mail_subject = 'Activity Marked As Not Applicable';
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'activity';
        $smsTemplate->event = 'activity-marked-not-applicable';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'warning';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Activity {{activity_title}} is Marked as Not Applicable  starts at {{start_date}} {{start_time}} by {{action_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{start_date}}, {{start_time}}, {{action_by}}, {{activity_title}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'activity-comment';
        $smsTemplate->mail_subject = 'Activity comment posted';
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'comment';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, comment is posted on Activity {{activity_title}} by {{comment_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{activity_title}}},{{comment_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'journal';
        $smsTemplate->mail_subject = 'Journal Created';
        $smsTemplate->module = 'journal';
        $smsTemplate->type = 'journal';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Journal is created by {{created_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{created_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'deviation';
        $smsTemplate->mail_subject = 'Deviation Created';
        $smsTemplate->module = 'deviation';
        $smsTemplate->type = 'deviation';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Deviation is created by {{created_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{created_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task-created-assigned';
        $smsTemplate->mail_subject = 'Task Created And Assigned';
        $smsTemplate->module = 'task';
        $smsTemplate->type = 'task';
        $smsTemplate->event = 'created-assigned';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Tasks {{task_title}} is  created and assigned successfully.";
        $smsTemplate->custom_attributes = "{{name}},{{task_title}}";
        $smsTemplate->save();

        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task-assignment';
        $smsTemplate->mail_subject = 'New Task Assigned';
        $smsTemplate->module = 'task';
        $smsTemplate->type = 'task';
        $smsTemplate->event = 'assigned';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Tasks {{task_title}} is  assigned to by {{assigned_by}}.";
        $smsTemplate->custom_attributes = "{{name}},{{task_title}},{{assigned_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task-done';
        $smsTemplate->mail_subject = 'Task Marked As Done';
        $smsTemplate->module = 'task';
        $smsTemplate->type = 'task';
        $smsTemplate->event = 'marked-done';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'success';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, task {{task_title}} is masrked as done  by {{action_by}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{task_title}},{{action_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'task-not-done';
        $smsTemplate->mail_subject = 'Task Marked As Not Done';
        $smsTemplate->module = 'task';
        $smsTemplate->type = 'task';
        $smsTemplate->event = 'marked-not-done';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'danger';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, task {{task_title}} is masrked as not done  by {{action_by}}.";
        $smsTemplate->custom_attributes = "{{name}}, {{task_title}},{{action_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'schedule-assignment';
        $smsTemplate->mail_subject = 'New scheduled Assigned';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'leave';
        $smsTemplate->event = 'schedule-assigned';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Schedule {{schedule_title}} on {{date}} starts at    {{start_time}} ends at {{end_time}}  is assigned to you  by {{assigned_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{schedule_title}},{{date}},{{start_time}},{{assigned_by}},{{end_time}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'schedule-request';
        $smsTemplate->mail_subject = 'New schedule Request';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'leave';
        $smsTemplate->event = 'requested';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, New Schedule for dates {{dates}}  requested to you  by {{requested_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{requested_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'schedule-slot-selected';
        $smsTemplate->mail_subject = 'Schedule  Slot Selected';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'leave';
        $smsTemplate->event = 'scheduleSlotSelected';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Schedule slot for {{date}} is selected by {{selected_by}} and  dates {{vacant_dates}}  are still available to select.";
        $smsTemplate->custom_attributes = "{{name}},{{vacant_dates}},{{selected_by}},{{date}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'leave-applied';
        $smsTemplate->mail_subject = 'New Leave Request';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'leave';
        $smsTemplate->event = 'leave-applied';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Leave  on {{date}} requested  by {{requested_by}} beacause of {{reason}}";
        $smsTemplate->custom_attributes = "{{date}},{{requested_by}},{{reason}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'leave-approved';
        $smsTemplate->mail_subject = 'Leave Approved';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'leave';
        $smsTemplate->event = 'leave-approved';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, your leave request for {{date}} is approved   by {{approved_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{date}},{{approved_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'leave-approved-multiple';
        $smsTemplate->mail_subject = 'Leave Approved';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'leave';
        $smsTemplate->event = 'leave-approved';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, your leave request for {{dates}} is approved   by {{approved_by}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();
        
        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'leave-applied-approved';
        $smsTemplate->mail_subject = 'Leave Applied And Approved';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'leave';
        $smsTemplate->event = 'leave-applied-approved';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();


        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'schedule-approved';
        $smsTemplate->mail_subject = 'Scheduled Approved';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'schedule';
        $smsTemplate->event = 'schedule-approved';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Your Schedule {{schedule_title}} on {{date}} starts at    {{start_time}} ends at {{end_time}}  is approved by {{approved_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{schedule_title}},{{date}},{{start_time}},{{approved_by}},{{end_time}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'schedule-verified';
        $smsTemplate->mail_subject = 'Scheduled Verified';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'schedule';
        $smsTemplate->event = 'schedule-verified';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Your Schedule {{schedule_title}} on {{date}} starts at    {{start_time}} ends at {{end_time}}  is verified by {{verified_by}}";
        $smsTemplate->custom_attributes = "{{name}}, {{schedule_title}},{{date}},{{start_time}},{{verified_by}},{{end_time}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'birthday-wish';
        $smsTemplate->mail_subject = 'Birthday Wishes';
        $smsTemplate->module = 'user';
        $smsTemplate->type = 'user';
        $smsTemplate->event = 'birthday';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 0;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, Happy And Blessed Birthday. Wishing You A Great Year Ahead.";
        $smsTemplate->custom_attributes = "{{name}}";
        $smsTemplate->save();

        //----------------------vivek-----------------------------------//

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'employee-created';
        $smsTemplate->mail_subject = 'Employee has Created';
        $smsTemplate->module = 'user';
        $smsTemplate->type = 'employee';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'detail';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, new employee {{user_name}} has been added.";
        $smsTemplate->custom_attributes = "{{name}},{{user_name}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'patient-created';
        $smsTemplate->mail_subject = 'Patient has Created';
        $smsTemplate->module = 'user';
        $smsTemplate->type = 'patient';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, new patient {{user_name}} has been added.";
        $smsTemplate->custom_attributes = "{{name}},{{user_name}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'branch-created';
        $smsTemplate->mail_subject = 'Branch has Created';
        $smsTemplate->module = 'user';
        $smsTemplate->type = 'branch';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, new branch {{user_name}} has been added.";
        $smsTemplate->custom_attributes = "{{name}},{{user_name}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'followup-created';
        $smsTemplate->mail_subject = 'Followup Created';
        $smsTemplate->module = 'plan';
        $smsTemplate->type = 'followup';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'ip-created';
        $smsTemplate->mail_subject = 'IP Created';
        $smsTemplate->module = 'plan';
        $smsTemplate->type = 'ip';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'ip-assigned';
        $smsTemplate->mail_subject = 'IP Assigned';
        $smsTemplate->module = 'plan';
        $smsTemplate->type = 'ip';
        $smsTemplate->event = 'assigned';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'ov-created';
        $smsTemplate->mail_subject = 'OV Created';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'ov';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'workshift-created';
        $smsTemplate->mail_subject = 'workshift has Created';
        $smsTemplate->module = 'schedule';
        $smsTemplate->type = 'workshift';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();


        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'trashed-activity-created';
        $smsTemplate->mail_subject = 'Trashed Activity has Created';
        $smsTemplate->module = 'activity';
        $smsTemplate->type = 'trashed-activity';
        $smsTemplate->event = 'created';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();

        

        $smsTemplate = new EmailTemplate;
        $smsTemplate->mail_sms_for = 'file-uploaded';
        $smsTemplate->mail_subject = 'New File has Uploaded';
        $smsTemplate->module = 'setting';
        $smsTemplate->type = 'manage-file';
        $smsTemplate->event = 'uploaded';
        $smsTemplate->screen = 'list';
        $smsTemplate->status_code = 'info';
        $smsTemplate->save_to_database = 1;
        $smsTemplate->sms_body = "";
        $smsTemplate->notify_body = "Dear {{name}}, {{approved_by}} has applied and approved your leave on {{dates}}";
        $smsTemplate->custom_attributes = "{{name}},{{dates}},{{approved_by}}";
        $smsTemplate->save();
    }
}
