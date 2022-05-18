<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      
       app()['cache']->forget('spatie.permission.cache');
       // create roles and assign existing permissions
        Permission::create(['name' => 'companies-browse', 'guard_name' => 'api','group_name'=>'companies','se_name'=>'companies-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'companies-read', 'guard_name' => 'api','group_name'=>'companies','se_name'=>'companies-read','belongs_to'=>'1']);

        Permission::create(['name' => 'companies-add', 'guard_name' => 'api','group_name'=>'companies','se_name'=>'companies-create','belongs_to'=>'1']);

        Permission::create(['name' => 'companies-edit', 'guard_name' => 'api','group_name'=>'companies','se_name'=>'companies-edit','belongs_to'=>'1']);

        Permission::create(['name' => 'companies-delete', 'guard_name' => 'api','group_name'=>'companies','se_name'=>'companies-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'role-browse', 'guard_name' => 'api','group_name'=>'role','se_name'=>'role-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'role-read', 'guard_name' => 'api','group_name'=>'role','se_name'=>'role-read','belongs_to'=>'3']);

        Permission::create(['name' => 'role-add', 'guard_name' => 'api','group_name'=>'role','se_name'=>'role-add','belongs_to'=>'3']);

        Permission::create(['name' => 'role-edit', 'guard_name' => 'api','group_name'=>'role','se_name'=>'role-edit','belongs_to'=>'3']);

        Permission::create(['name' => 'role-delete', 'guard_name' => 'api','group_name'=>'role','se_name'=>'role-delete','belongs_to'=>'3']);


         Permission::create(['name' => 'dashboard-browse', 'guard_name' => 'api','group_name'=>'dashboard','se_name'=>'dashboard-browse','belongs_to'=>'3']);

         Permission::create(['name' => 'notifications-browse', 'guard_name' => 'api','group_name'=>'notifications','se_name'=>'notifications-browse','belongs_to'=>'3']);

         Permission::create(['name' => 'notifications-add', 'guard_name' => 'api','group_name'=>'notifications','se_name'=>'notifications-add','belongs_to'=>'3']);

        Permission::create(['name' => 'notifications-edit', 'guard_name' => 'api','group_name'=>'notifications','se_name'=>'notifications-edit','belongs_to'=>'3']);

        Permission::create(['name' => 'notifications-delete', 'guard_name' => 'api','group_name'=>'notifications','se_name'=>'notifications-delete','belongs_to'=>'3']);

        Permission::create(['name' => 'requests-browse', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-browse','belongs_to'=>'3']);

         Permission::create(['name' => 'requests-add', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-add','belongs_to'=>'3']);
         Permission::create(['name' => 'requests-read', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-read','belongs_to'=>'3']);

        Permission::create(['name' => 'requests-edit', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'requests-delete', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-delete','belongs_to'=>'3']);

        Permission::create(['name' => 'users-browse', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'users-add', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-add','belongs_to'=>'3']);

        Permission::create(['name' => 'users-read', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-read','belongs_to'=>'3']);

        Permission::create(['name' => 'users-edit', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'users-delete', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-delete','belongs_to'=>'3']);

      

        Permission::create(['name' => 'categories-browse', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'categories-add', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-add','belongs_to'=>'3']);
        Permission::create(['name' => 'categories-read', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-read','belongs_to'=>'3']);

        Permission::create(['name' => 'categories-edit', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'categories-delete', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-delete','belongs_to'=>'3']);

        Permission::create(['name' => 'licences-browse', 'guard_name' => 'api','group_name'=>'licences','se_name'=>'licences-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'licences-add', 'guard_name' => 'api','group_name'=>'licences','se_name'=>'licences-add','belongs_to'=>'1']);
        Permission::create(['name' => 'licences-read', 'guard_name' => 'api','group_name'=>'licences','se_name'=>'licences-read','belongs_to'=>'1']);

        Permission::create(['name' => 'licences-edit', 'guard_name' => 'api','group_name'=>'licences','se_name'=>'licences-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'licences-delete', 'guard_name' => 'api','group_name'=>'licences','se_name'=>'licences-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'modules-browse', 'guard_name' => 'api','group_name'=>'modules','se_name'=>'modules-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'modules-add', 'guard_name' => 'api','group_name'=>'modules','se_name'=>'modules-add','belongs_to'=>'1']);
        Permission::create(['name' => 'modules-read', 'guard_name' => 'api','group_name'=>'modules','se_name'=>'modules-read','belongs_to'=>'1']);

        Permission::create(['name' => 'modules-edit', 'guard_name' => 'api','group_name'=>'modules','se_name'=>'modules-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'modules-delete', 'guard_name' => 'api','group_name'=>'modules','se_name'=>'modules-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'packages-browse', 'guard_name' => 'api','group_name'=>'packages','se_name'=>'packages-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'packages-add', 'guard_name' => 'api','group_name'=>'packages','se_name'=>'packages-add','belongs_to'=>'1']);
        Permission::create(['name' => 'packages-read', 'guard_name' => 'api','group_name'=>'packages','se_name'=>'packages-read','belongs_to'=>'1']);

        Permission::create(['name' => 'packages-edit', 'guard_name' => 'api','group_name'=>'packages','se_name'=>'packages-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'packages-delete', 'guard_name' => 'api','group_name'=>'packages','se_name'=>'packages-delete','belongs_to'=>'1']);


        Permission::create(['name' => 'userType-browse', 'guard_name' => 'api','group_name'=>'userType','se_name'=>'userType-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'userType-add', 'guard_name' => 'api','group_name'=>'userType','se_name'=>'userType-add','belongs_to'=>'1']);
        Permission::create(['name' => 'userType-read', 'guard_name' => 'api','group_name'=>'userType','se_name'=>'userType-read','belongs_to'=>'1']);

        Permission::create(['name' => 'userType-edit', 'guard_name' => 'api','group_name'=>'userType','se_name'=>'userType-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'userType-delete', 'guard_name' => 'api','group_name'=>'userType','se_name'=>'userType-delete','belongs_to'=>'1']);

         Permission::create(['name' => 'companyType-browse', 'guard_name' => 'api','group_name'=>'companyType','se_name'=>'companyType-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'companyType-add', 'guard_name' => 'api','group_name'=>'companyType','se_name'=>'companyType-add','belongs_to'=>'1']);
        Permission::create(['name' => 'companyType-read', 'guard_name' => 'api','group_name'=>'companyType','se_name'=>'companyType-read','belongs_to'=>'1']);

        Permission::create(['name' => 'companyType-edit', 'guard_name' => 'api','group_name'=>'companyType','se_name'=>'companyType-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'companyType-delete', 'guard_name' => 'api','group_name'=>'companyType','se_name'=>'companyType-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'settings-browse', 'guard_name' => 'api','group_name'=>'settings','se_name'=>'settings-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'settings-add', 'guard_name' => 'api','group_name'=>'settings','se_name'=>'settings-add','belongs_to'=>'3']);
        Permission::create(['name' => 'settings-read', 'guard_name' => 'api','group_name'=>'settings','se_name'=>'settings-read','belongs_to'=>'3']);

        Permission::create(['name' => 'settings-edit', 'guard_name' => 'api','group_name'=>'settings','se_name'=>'settings-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'settings-delete', 'guard_name' => 'api','group_name'=>'settings','se_name'=>'settings-delete','belongs_to'=>'3']);

         Permission::create(['name' => 'adminEmployee-browse', 'guard_name' => 'api','group_name'=>'adminEmployee','se_name'=>'adminEmployee-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'adminEmployee-add', 'guard_name' => 'api','group_name'=>'adminEmployee','se_name'=>'adminEmployee-add','belongs_to'=>'1']);

        Permission::create(['name' => 'adminEmployee-read', 'guard_name' => 'api','group_name'=>'adminEmployee','se_name'=>'adminEmployee-read','belongs_to'=>'1']);

        Permission::create(['name' => 'adminEmployee-edit', 'guard_name' => 'api','group_name'=>'adminEmployee','se_name'=>'adminEmployee-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'adminEmployee-delete', 'guard_name' => 'api','group_name'=>'adminEmployee','se_name'=>'adminEmployee-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'employees-browse', 'guard_name' => 'api','group_name'=>'employees','se_name'=>'employees-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'employees-add', 'guard_name' => 'api','group_name'=>'employees','se_name'=>'employees-add','belongs_to'=>'2']);

        Permission::create(['name' => 'employees-read', 'guard_name' => 'api','group_name'=>'employees','se_name'=>'employees-read','belongs_to'=>'2']);

        Permission::create(['name' => 'employees-edit', 'guard_name' => 'api','group_name'=>'employees','se_name'=>'employees-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'employees-delete', 'guard_name' => 'api','group_name'=>'employees','se_name'=>'employees-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'patients-browse', 'guard_name' => 'api','group_name'=>'patients','se_name'=>'patients-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'patients-add', 'guard_name' => 'api','group_name'=>'patients','se_name'=>'patients-add','belongs_to'=>'2']);

        Permission::create(['name' => 'patients-read', 'guard_name' => 'api','group_name'=>'patients','se_name'=>'patients-read','belongs_to'=>'2']);

        Permission::create(['name' => 'patients-edit', 'guard_name' => 'api','group_name'=>'patients','se_name'=>'patients-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'patients-delete', 'guard_name' => 'api','group_name'=>'patients','se_name'=>'patients-delete','belongs_to'=>'2']);

       

        Permission::create(['name' => 'departments-browse', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'departments-add', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'departments-read', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-read','belongs_to'=>'2']);

        Permission::create(['name' => 'departments-edit', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'departments-delete', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-delete','belongs_to'=>'2']);

       
        Permission::create(['name' => 'journal-browse', 'guard_name' => 'api','group_name'=>'journal','se_name'=>'journal-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'journal-add', 'guard_name' => 'api','group_name'=>'journal','se_name'=>'journal-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journal-read', 'guard_name' => 'api','group_name'=>'journal','se_name'=>'journal-read','belongs_to'=>'2']);

        Permission::create(['name' => 'journal-edit', 'guard_name' => 'api','group_name'=>'journal','se_name'=>'journal-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journal-delete', 'guard_name' => 'api','group_name'=>'journal','se_name'=>'journal-delete','belongs_to'=>'2']);

       
        Permission::create(['name' => 'deviation-browse', 'guard_name' => 'api','group_name'=>'deviation','se_name'=>'deviation-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'deviation-add', 'guard_name' => 'api','group_name'=>'deviation','se_name'=>'deviation-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviation-read', 'guard_name' => 'api','group_name'=>'deviation','se_name'=>'deviation-read','belongs_to'=>'2']);

        Permission::create(['name' => 'deviation-edit', 'guard_name' => 'api','group_name'=>'deviation','se_name'=>'deviation-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviation-delete', 'guard_name' => 'api','group_name'=>'deviation','se_name'=>'deviation-delete','belongs_to'=>'2']);

        
        Permission::create(['name' => 'schedule-browse', 'guard_name' => 'api','group_name'=>'schedule','se_name'=>'schedule-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'schedule-add', 'guard_name' => 'api','group_name'=>'schedule','se_name'=>'schedule-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'schedule-read', 'guard_name' => 'api','group_name'=>'schedule','se_name'=>'schedule-read','belongs_to'=>'2']);

        Permission::create(['name' => 'schedule-edit', 'guard_name' => 'api','group_name'=>'schedule','se_name'=>'schedule-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'schedule-delete', 'guard_name' => 'api','group_name'=>'schedule','se_name'=>'schedule-delete','belongs_to'=>'2']);


        Permission::create(['name' => 'persons-browse', 'guard_name' => 'api','group_name'=>'persons','se_name'=>'persons-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'persons-add', 'guard_name' => 'api','group_name'=>'persons','se_name'=>'persons-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'persons-read', 'guard_name' => 'api','group_name'=>'persons','se_name'=>'persons-read','belongs_to'=>'2']);

        Permission::create(['name' => 'persons-edit', 'guard_name' => 'api','group_name'=>'persons','se_name'=>'persons-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'persons-delete', 'guard_name' => 'api','group_name'=>'persons','se_name'=>'persons-delete','belongs_to'=>'2']);

       
        Permission::create(['name' => 'workShift-browse', 'guard_name' => 'api','group_name'=>'workShift','se_name'=>'workShift-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'workShift-add', 'guard_name' => 'api','group_name'=>'workShift','se_name'=>'workShift-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'workShift-read', 'guard_name' => 'api','group_name'=>'workShift','se_name'=>'workShift-read','belongs_to'=>'2']);

        Permission::create(['name' => 'workShift-edit', 'guard_name' => 'api','group_name'=>'workShift','se_name'=>'workShift-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'workShift-delete', 'guard_name' => 'api','group_name'=>'workShift','se_name'=>'workShift-delete','belongs_to'=>'2']);


        Permission::create(['name' => 'branch-browse', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'branch-add', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'branch-read', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-read','belongs_to'=>'2']);

        Permission::create(['name' => 'branch-edit', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'branch-delete', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'ip-browse', 'guard_name' => 'api','group_name'=>'ip','se_name'=>'ip-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'ip-add', 'guard_name' => 'api','group_name'=>'ip','se_name'=>'ip-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ip-read', 'guard_name' => 'api','group_name'=>'ip','se_name'=>'ip-read','belongs_to'=>'2']);

        Permission::create(['name' => 'ip-edit', 'guard_name' => 'api','group_name'=>'ip','se_name'=>'ip-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ip-delete', 'guard_name' => 'api','group_name'=>'ip','se_name'=>'ip-delete','belongs_to'=>'2']);

     
        Permission::create(['name' => 'followup-browse', 'guard_name' => 'api','group_name'=>'followup','se_name'=>'followup-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'followup-add', 'guard_name' => 'api','group_name'=>'followup','se_name'=>'followup-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'followup-read', 'guard_name' => 'api','group_name'=>'followup','se_name'=>'followup-read','belongs_to'=>'2']);

        Permission::create(['name' => 'followup-edit', 'guard_name' => 'api','group_name'=>'followup','se_name'=>'followup-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'followup-delete', 'guard_name' => 'api','group_name'=>'followup','se_name'=>'followup-delete','belongs_to'=>'2']);

       
        Permission::create(['name' => 'activity-browse', 'guard_name' => 'api','group_name'=>'activity','se_name'=>'activity-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'activity-add', 'guard_name' => 'api','group_name'=>'activity','se_name'=>'activity-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'activity-read', 'guard_name' => 'api','group_name'=>'activity','se_name'=>'activity-read','belongs_to'=>'2']);

        Permission::create(['name' => 'activity-edit', 'guard_name' => 'api','group_name'=>'activity','se_name'=>'activity-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'activity-delete', 'guard_name' => 'api','group_name'=>'activity','se_name'=>'activity-delete','belongs_to'=>'2']);

          
        Permission::create(['name' => 'reports-delete', 'guard_name' => 'api','group_name'=>'reports','se_name'=>'reports-delete','belongs_to'=>'3']);


        Permission::create(['name' => 'words-browse', 'guard_name' => 'api','group_name'=>'words','se_name'=>'words-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'words-add', 'guard_name' => 'api','group_name'=>'words','se_name'=>'words-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'words-read', 'guard_name' => 'api','group_name'=>'words','se_name'=>'words-read','belongs_to'=>'2']);

        Permission::create(['name' => 'words-edit', 'guard_name' => 'api','group_name'=>'words','se_name'=>'words-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'words-delete', 'guard_name' => 'api','group_name'=>'words','se_name'=>'words-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'paragraphs-browse', 'guard_name' => 'api','group_name'=>'paragraphs','se_name'=>'paragraphs-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'paragraphs-add', 'guard_name' => 'api','group_name'=>'paragraphs','se_name'=>'paragraphs-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'paragraphs-read', 'guard_name' => 'api','group_name'=>'paragraphs','se_name'=>'paragraphs-read','belongs_to'=>'2']);

        Permission::create(['name' => 'paragraphs-edit', 'guard_name' => 'api','group_name'=>'paragraphs','se_name'=>'paragraphs-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'paragraphs-delete', 'guard_name' => 'api','group_name'=>'paragraphs','se_name'=>'paragraphs-delete','belongs_to'=>'2']);
        

        Permission::create(['name' => 'task-browse', 'guard_name' => 'api','group_name'=>'task','se_name'=>'task-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'task-add', 'guard_name' => 'api','group_name'=>'task','se_name'=>'task-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'task-read', 'guard_name' => 'api','group_name'=>'task','se_name'=>'task-read','belongs_to'=>'2']);

        Permission::create(['name' => 'task-edit', 'guard_name' => 'api','group_name'=>'task','se_name'=>'task-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'task-delete', 'guard_name' => 'api','group_name'=>'task','se_name'=>'task-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'internalCom-read', 'guard_name' => 'api','group_name'=>'command','se_name'=>'internalCom-read','belongs_to'=>'2']);

         Permission::create(['name' => 'EmailTemplate-browse', 'guard_name' => 'api','group_name'=>'EmailTemplate','se_name'=>'EmailTemplate-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'EmailTemplate-add', 'guard_name' => 'api','group_name'=>'EmailTemplate','se_name'=>'EmailTemplate-add','belongs_to'=>'1']);
        
        Permission::create(['name' => 'EmailTemplate-read', 'guard_name' => 'api','group_name'=>'EmailTemplate','se_name'=>'EmailTemplate-read','belongs_to'=>'1']);

        Permission::create(['name' => 'EmailTemplate-edit', 'guard_name' => 'api','group_name'=>'EmailTemplate','se_name'=>'EmailTemplate-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'EmailTemplate-delete', 'guard_name' => 'api','group_name'=>'EmailTemplate','se_name'=>'EmailTemplate-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'bank-browse', 'guard_name' => 'api','group_name'=>'bank','se_name'=>'bank-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'bank-add', 'guard_name' => 'api','group_name'=>'bank','se_name'=>'bank-add','belongs_to'=>'3']);
        
        Permission::create(['name' => 'bank-read', 'guard_name' => 'api','group_name'=>'bank','se_name'=>'bank-read','belongs_to'=>'3']);

        Permission::create(['name' => 'bank-edit', 'guard_name' => 'api','group_name'=>'bank','se_name'=>'bank-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'bank-delete', 'guard_name' => 'api','group_name'=>'bank','se_name'=>'bank-delete','belongs_to'=>'3']);

        Permission::create(['name' => 'questions-browse', 'guard_name' => 'api','group_name'=>'questions','se_name'=>'questions-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'questions-add', 'guard_name' => 'api','group_name'=>'questions','se_name'=>'questions-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'questions-read', 'guard_name' => 'api','group_name'=>'questions','se_name'=>'questions-read','belongs_to'=>'2']);

        Permission::create(['name' => 'questions-edit', 'guard_name' => 'api','group_name'=>'questions','se_name'=>'questions-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'questions-delete', 'guard_name' => 'api','group_name'=>'questions','se_name'=>'questions-delete','belongs_to'=>'2']);


         Permission::create(['name' => 'isCategoryEditPermission-edit', 'guard_name' => 'api','group_name'=>'isCategoryEditPermission','se_name'=>'isCategoryEditPermission-edit','belongs_to'=>'2']);

         Permission::create(['name' => 'calendar-browse', 'guard_name' => 'api','group_name'=>'calendar','se_name'=>'calendar-browse','belongs_to'=>'2']);

         Permission::create(['name' => 'patientimport-add', 'guard_name' => 'api','group_name'=>'import','se_name'=>'patientimport-add','belongs_to'=>'2']);


         Permission::create(['name' => 'files-browse', 'guard_name' => 'api','group_name'=>'files','se_name'=>'files-browse','belongs_to'=>'3']);

         Permission::create(['name' => 'files-read', 'guard_name' => 'api','group_name'=>'files','se_name'=>'files-read','belongs_to'=>'3']);

         Permission::create(['name' => 'files-edit', 'guard_name' => 'api','group_name'=>'files','se_name'=>'files-edit','belongs_to'=>'3']);

         Permission::create(['name' => 'files-add', 'guard_name' => 'api','group_name'=>'files','se_name'=>'files-add','belongs_to'=>'3']);

         Permission::create(['name' => 'files-delete', 'guard_name' => 'api','group_name'=>'files','se_name'=>'files-delete','belongs_to'=>'3']);

         //Licence
         Permission::create(['name' => 'licences', 'guard_name' => 'api','group_name'=>'licence','se_name'=>'licences','belongs_to'=>'1']);
         Permission::create(['name' => 'licence-add', 'guard_name' => 'api','group_name'=>'licence','se_name'=>'licence-add','belongs_to'=>'1']);
         Permission::create(['name' => 'licence-edit', 'guard_name' => 'api','group_name'=>'licence','se_name'=>'licence-edit','belongs_to'=>'1']);
         Permission::create(['name' => 'licence-read', 'guard_name' => 'api','group_name'=>'licence','se_name'=>'licence-read','belongs_to'=>'1']);
         Permission::create(['name' => 'licence-delete', 'guard_name' => 'api','group_name'=>'licence','se_name'=>'licence-delete','belongs_to'=>'1']);

          

    }
}
