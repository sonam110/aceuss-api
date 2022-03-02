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

        Permission::create(['name' => 'requests-browse', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-browse','belongs_to'=>'1']);

         Permission::create(['name' => 'requests-add', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-add','belongs_to'=>'1']);
         Permission::create(['name' => 'requests-read', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-read','belongs_to'=>'1']);

        Permission::create(['name' => 'requests-edit', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'requests-delete', 'guard_name' => 'api','group_name'=>'requests','se_name'=>'requests-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'users-browse', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'users-add', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-add','belongs_to'=>'3']);

        Permission::create(['name' => 'users-read', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-read','belongs_to'=>'3']);

        Permission::create(['name' => 'users-edit', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'users-delete', 'guard_name' => 'api','group_name'=>'users','se_name'=>'users-delete','belongs_to'=>'3']);

        

        Permission::create(['name' => 'activitiesCls-browse', 'guard_name' => 'api','group_name'=>'activitiesCls','se_name'=>'activitiesCls-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'activitiesCls-add', 'guard_name' => 'api','group_name'=>'activitiesCls','se_name'=>'activitiesCls-add','belongs_to'=>'1']);
        Permission::create(['name' => 'activitiesCls-read', 'guard_name' => 'api','group_name'=>'activitiesCls','se_name'=>'activitiesCls-read','belongs_to'=>'1']);

        Permission::create(['name' => 'activitiesCls-edit', 'guard_name' => 'api','group_name'=>'activitiesCls','se_name'=>'activitiesCls-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'activitiesCls-delete', 'guard_name' => 'api','group_name'=>'activitiesCls','se_name'=>'activitiesCls-delete','belongs_to'=>'1']);

        Permission::create(['name' => 'categories-browse', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'categories-add', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-add','belongs_to'=>'3']);
        Permission::create(['name' => 'categories-read', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-read','belongs_to'=>'3']);

        Permission::create(['name' => 'categories-edit', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'categories-delete', 'guard_name' => 'api','group_name'=>'categories','se_name'=>'categories-delete','belongs_to'=>'3']);

        Permission::create(['name' => 'licenses-browse', 'guard_name' => 'api','group_name'=>'licenses','se_name'=>'licenses-browse','belongs_to'=>'1']);

        Permission::create(['name' => 'licenses-add', 'guard_name' => 'api','group_name'=>'licenses','se_name'=>'licenses-add','belongs_to'=>'1']);
        Permission::create(['name' => 'licenses-read', 'guard_name' => 'api','group_name'=>'licenses','se_name'=>'licenses-read','belongs_to'=>'1']);

        Permission::create(['name' => 'licenses-edit', 'guard_name' => 'api','group_name'=>'licenses','se_name'=>'licenses-edit','belongs_to'=>'1']);
        
        Permission::create(['name' => 'licenses-delete', 'guard_name' => 'api','group_name'=>'licenses','se_name'=>'licenses-delete','belongs_to'=>'1']);

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

        Permission::create(['name' => 'nurses-browse', 'guard_name' => 'api','group_name'=>'nurses','se_name'=>'nurses-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'nurses-add', 'guard_name' => 'api','group_name'=>'nurses','se_name'=>'nurses-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'nurses-read', 'guard_name' => 'api','group_name'=>'nurses','se_name'=>'nurses-read','belongs_to'=>'2']);

        Permission::create(['name' => 'nurses-edit', 'guard_name' => 'api','group_name'=>'nurses','se_name'=>'nurses-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'nurses-delete', 'guard_name' => 'api','group_name'=>'nurses','se_name'=>'nurses-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'departments-browse', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'departments-add', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'departments-read', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-read','belongs_to'=>'2']);

        Permission::create(['name' => 'departments-edit', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'departments-delete', 'guard_name' => 'api','group_name'=>'departments','se_name'=>'departments-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'activitySelf-browse', 'guard_name' => 'api','group_name'=>'activitySelf','se_name'=>'activitySelf-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'activitySelf-add', 'guard_name' => 'api','group_name'=>'activitySelf','se_name'=>'activitySelf-add','belongs_to'=>'3']);
        
        Permission::create(['name' => 'activitySelf-read', 'guard_name' => 'api','group_name'=>'activitySelf','se_name'=>'activitySelf-read','belongs_to'=>'3']);

        Permission::create(['name' => 'activitySelf-edit', 'guard_name' => 'api','group_name'=>'activitySelf','se_name'=>'activitySelf-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'activitySelf-delete', 'guard_name' => 'api','group_name'=>'activitySelf','se_name'=>'activitySelf-delete','belongs_to'=>'3']);

        Permission::create(['name' => 'activityPatients-browse', 'guard_name' => 'api','group_name'=>'activityPatients','se_name'=>'activityPatients-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'activityPatients-add', 'guard_name' => 'api','group_name'=>'activityPatients','se_name'=>'activityPatients-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'activityPatients-read', 'guard_name' => 'api','group_name'=>'activityPatients','se_name'=>'activityPatients-read','belongs_to'=>'2']);

        Permission::create(['name' => 'activityPatients-edit', 'guard_name' => 'api','group_name'=>'activityPatients','se_name'=>'activityPatients-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'activityPatients-delete', 'guard_name' => 'api','group_name'=>'activityPatients','se_name'=>'activityPatients-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'activityEmployees-browse', 'guard_name' => 'api','group_name'=>'activityEmployees','se_name'=>'activityEmployees-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'activityEmployees-add', 'guard_name' => 'api','group_name'=>'activityEmployees','se_name'=>'activityEmployees-add','belongs_to'=>'3']);
        
        Permission::create(['name' => 'activityEmployees-read', 'guard_name' => 'api','group_name'=>'activityEmployees','se_name'=>'activityEmployees-read','belongs_to'=>'3']);

        Permission::create(['name' => 'activityEmployees-edit', 'guard_name' => 'api','group_name'=>'activityEmployees','se_name'=>'activityEmployees-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'activityEmployees-delete', 'guard_name' => 'api','group_name'=>'activityEmployees','se_name'=>'activityEmployees-delete','belongs_to'=>'3']);

        Permission::create(['name' => 'journalSelf-browse', 'guard_name' => 'api','group_name'=>'journalSelf','se_name'=>'journalSelf-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'journalSelf-add', 'guard_name' => 'api','group_name'=>'journalSelf','se_name'=>'journalSelf-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journalSelf-read', 'guard_name' => 'api','group_name'=>'journalSelf','se_name'=>'journalSelf-read','belongs_to'=>'2']);

        Permission::create(['name' => 'journalSelf-edit', 'guard_name' => 'api','group_name'=>'journalSelf','se_name'=>'journalSelf-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journalSelf-delete', 'guard_name' => 'api','group_name'=>'journalSelf','se_name'=>'journalSelf-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'journalEmployees-browse', 'guard_name' => 'api','group_name'=>'journalEmployees','se_name'=>'journalEmployees-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'journalEmployees-add', 'guard_name' => 'api','group_name'=>'journalEmployees','se_name'=>'journalEmployees-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journalEmployees-read', 'guard_name' => 'api','group_name'=>'journalEmployees','se_name'=>'journalEmployees-read','belongs_to'=>'2']);

        Permission::create(['name' => 'journalEmployees-edit', 'guard_name' => 'api','group_name'=>'journalEmployees','se_name'=>'journalEmployees-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journalEmployees-delete', 'guard_name' => 'api','group_name'=>'journalEmployees','se_name'=>'journalEmployees-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'journalPatients-browse', 'guard_name' => 'api','group_name'=>'journalPatients','se_name'=>'journalPatients-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'journalPatients-add', 'guard_name' => 'api','group_name'=>'journalPatients','se_name'=>'journalPatients-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journalPatients-read', 'guard_name' => 'api','group_name'=>'journalPatients','se_name'=>'journalPatients-read','belongs_to'=>'2']);

        Permission::create(['name' => 'journalPatients-edit', 'guard_name' => 'api','group_name'=>'journalPatients','se_name'=>'journalPatients-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'journalPatients-delete', 'guard_name' => 'api','group_name'=>'journalPatients','se_name'=>'journalPatients-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationSelf-browse', 'guard_name' => 'api','group_name'=>'deviationSelf','se_name'=>'deviationSelf-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationSelf-add', 'guard_name' => 'api','group_name'=>'deviationSelf','se_name'=>'deviationSelf-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviationSelf-read', 'guard_name' => 'api','group_name'=>'deviationSelf','se_name'=>'deviationSelf-read','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationSelf-edit', 'guard_name' => 'api','group_name'=>'deviationSelf','se_name'=>'deviationSelf-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviationSelf-delete', 'guard_name' => 'api','group_name'=>'deviationSelf','se_name'=>'deviationSelf-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationPatients-browse', 'guard_name' => 'api','group_name'=>'deviationPatients','se_name'=>'deviationPatients-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationPatients-add', 'guard_name' => 'api','group_name'=>'deviationPatients','se_name'=>'deviationPatients-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviationPatients-read', 'guard_name' => 'api','group_name'=>'deviationPatients','se_name'=>'deviationPatients-read','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationPatients-edit', 'guard_name' => 'api','group_name'=>'deviationPatients','se_name'=>'deviationPatients-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviationPatients-delete', 'guard_name' => 'api','group_name'=>'deviationPatients','se_name'=>'deviationPatients-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationEmployees-browse', 'guard_name' => 'api','group_name'=>'deviationEmployees','se_name'=>'deviationEmployees-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationEmployees-add', 'guard_name' => 'api','group_name'=>'deviationEmployees','se_name'=>'deviationEmployees-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviationEmployees-read', 'guard_name' => 'api','group_name'=>'deviationEmployees','se_name'=>'deviationEmployees-read','belongs_to'=>'2']);

        Permission::create(['name' => 'deviationEmployees-edit', 'guard_name' => 'api','group_name'=>'deviationEmployees','se_name'=>'deviationEmployees-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'deviationEmployees-delete', 'guard_name' => 'api','group_name'=>'deviationEmployees','se_name'=>'deviationEmployees-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'scheduleSelf-browse', 'guard_name' => 'api','group_name'=>'scheduleSelf','se_name'=>'scheduleSelf-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'scheduleSelf-add', 'guard_name' => 'api','group_name'=>'scheduleSelf','se_name'=>'scheduleSelf-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'scheduleSelf-read', 'guard_name' => 'api','group_name'=>'scheduleSelf','se_name'=>'scheduleSelf-read','belongs_to'=>'2']);

        Permission::create(['name' => 'scheduleSelf-edit', 'guard_name' => 'api','group_name'=>'scheduleSelf','se_name'=>'scheduleSelf-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'scheduleSelf-delete', 'guard_name' => 'api','group_name'=>'scheduleSelf','se_name'=>'scheduleSelf-delete','belongs_to'=>'2']);


        Permission::create(['name' => 'scheduleEmployees-browse', 'guard_name' => 'api','group_name'=>'scheduleEmployees','se_name'=>'scheduleEmployees-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'scheduleEmployees-add', 'guard_name' => 'api','group_name'=>'scheduleEmployees','se_name'=>'scheduleEmployees-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'scheduleEmployees-read', 'guard_name' => 'api','group_name'=>'scheduleEmployees','se_name'=>'scheduleEmployees-read','belongs_to'=>'2']);

        Permission::create(['name' => 'scheduleEmployees-edit', 'guard_name' => 'api','group_name'=>'scheduleEmployees','se_name'=>'scheduleEmployees-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'scheduleEmployees-delete', 'guard_name' => 'api','group_name'=>'scheduleEmployees','se_name'=>'scheduleEmployees-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'schedulePatients-browse', 'guard_name' => 'api','group_name'=>'schedulePatients','se_name'=>'schedulePatients-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'schedulePatients-add', 'guard_name' => 'api','group_name'=>'schedulePatients','se_name'=>'schedulePatients-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'schedulePatients-read', 'guard_name' => 'api','group_name'=>'schedulePatients','se_name'=>'schedulePatients-read','belongs_to'=>'2']);

        Permission::create(['name' => 'schedulePatients-edit', 'guard_name' => 'api','group_name'=>'schedulePatients','se_name'=>'schedulePatients-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'schedulePatients-delete', 'guard_name' => 'api','group_name'=>'schedulePatients','se_name'=>'schedulePatients-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'patientFamily-browse', 'guard_name' => 'api','group_name'=>'patientFamily','se_name'=>'patientFamily-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'patientFamily-add', 'guard_name' => 'api','group_name'=>'patientFamily','se_name'=>'patientFamily-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'patientFamily-read', 'guard_name' => 'api','group_name'=>'patientFamily','se_name'=>'patientFamily-read','belongs_to'=>'2']);

        Permission::create(['name' => 'patientFamily-edit', 'guard_name' => 'api','group_name'=>'patientFamily','se_name'=>'patientFamily-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'patientFamily-delete', 'guard_name' => 'api','group_name'=>'patientFamily','se_name'=>'patientFamily-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'patientContactPerson-browse', 'guard_name' => 'api','group_name'=>'patientContactPerson','se_name'=>'patientContactPerson-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'patientContactPerson-add', 'guard_name' => 'api','group_name'=>'patientContactPerson','se_name'=>'patientContactPerson-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'patientContactPerson-read', 'guard_name' => 'api','group_name'=>'patientContactPerson','se_name'=>'patientContactPerson-read','belongs_to'=>'2']);

        Permission::create(['name' => 'patientContactPerson-edit', 'guard_name' => 'api','group_name'=>'patientContactPerson','se_name'=>'patientContactPerson-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'patientContactPerson-delete', 'guard_name' => 'api','group_name'=>'patientContactPerson','se_name'=>'patientContactPerson-delete','belongs_to'=>'2']);


        Permission::create(['name' => 'branch-browse', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'branch-add', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'branch-read', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-read','belongs_to'=>'2']);

        Permission::create(['name' => 'branch-edit', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'branch-delete', 'guard_name' => 'api','group_name'=>'branch','se_name'=>'branch-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'ipSelf-browse', 'guard_name' => 'api','group_name'=>'ipSelf','se_name'=>'ipSelf-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'ipSelf-add', 'guard_name' => 'api','group_name'=>'ipSelf','se_name'=>'ipSelf-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipSelf-read', 'guard_name' => 'api','group_name'=>'ipSelf','se_name'=>'ipSelf-read','belongs_to'=>'2']);

        Permission::create(['name' => 'ipSelf-edit', 'guard_name' => 'api','group_name'=>'ipSelf','se_name'=>'ipSelf-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipSelf-delete', 'guard_name' => 'api','group_name'=>'ipSelf','se_name'=>'ipSelf-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'ipPatients-browse', 'guard_name' => 'api','group_name'=>'ipPatients','se_name'=>'ipPatients-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'ipPatients-add', 'guard_name' => 'api','group_name'=>'ipPatients','se_name'=>'ipPatients-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipPatients-read', 'guard_name' => 'api','group_name'=>'ipPatients','se_name'=>'ipPatients-read','belongs_to'=>'2']);

        Permission::create(['name' => 'ipPatients-edit', 'guard_name' => 'api','group_name'=>'ipPatients','se_name'=>'ipPatients-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipPatients-delete', 'guard_name' => 'api','group_name'=>'ipPatients','se_name'=>'ipPatients-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'ipEmployees-browse', 'guard_name' => 'api','group_name'=>'ipEmployees','se_name'=>'ipEmployees-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'ipEmployees-add', 'guard_name' => 'api','group_name'=>'ipEmployees','se_name'=>'ipEmployees-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipEmployees-read', 'guard_name' => 'api','group_name'=>'ipEmployees','se_name'=>'ipEmployees-read','belongs_to'=>'2']);

        Permission::create(['name' => 'ipEmployees-edit', 'guard_name' => 'api','group_name'=>'ipEmployees','se_name'=>'ipEmployees-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipEmployees-delete', 'guard_name' => 'api','group_name'=>'ipEmployees','se_name'=>'ipEmployees-delete','belongs_to'=>'2']);


        Permission::create(['name' => 'ipFollowUpsSelf-browse', 'guard_name' => 'api','group_name'=>'ipFollowUpsSelf','se_name'=>'ipFollowUpsSelf-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'ipFollowUpsSelf-add', 'guard_name' => 'api','group_name'=>'ipFollowUpsSelf','se_name'=>'ipFollowUpsSelf-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipFollowUpsSelf-read', 'guard_name' => 'api','group_name'=>'ipFollowUpsSelf','se_name'=>'ipFollowUpsSelf-read','belongs_to'=>'2']);

        Permission::create(['name' => 'ipFollowUpsSelf-edit', 'guard_name' => 'api','group_name'=>'ipFollowUpsSelf','se_name'=>'ipFollowUpsSelf-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipFollowUpsSelf-delete', 'guard_name' => 'api','group_name'=>'ipFollowUpsSelf','se_name'=>'ipFollowUpsSelf-delete','belongs_to'=>'2']);

        Permission::create(['name' => 'ipFollowUpsPatients-browse', 'guard_name' => 'api','group_name'=>'ipFollowUpsPatients','se_name'=>'ipFollowUpsPatients-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'ipFollowUpsPatients-add', 'guard_name' => 'api','group_name'=>'ipFollowUpsPatients','se_name'=>'ipFollowUpsPatients-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipFollowUpsPatients-read', 'guard_name' => 'api','group_name'=>'ipFollowUpsPatients','se_name'=>'ipFollowUpsPatients-read','belongs_to'=>'2']);

        Permission::create(['name' => 'ipFollowUpsPatients-edit', 'guard_name' => 'api','group_name'=>'ipFollowUpsPatients','se_name'=>'ipFollowUpsPatients-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipFollowUpsPatients-delete', 'guard_name' => 'api','group_name'=>'ipFollowUpsPatients','se_name'=>'ipFollowUpsPatients-delete','belongs_to'=>'2']);




        Permission::create(['name' => 'ipFollowUpsEmployees-browse', 'guard_name' => 'api','group_name'=>'ipFollowUpsEmployees','se_name'=>'ipFollowUpsEmployees-browse','belongs_to'=>'2']);

        Permission::create(['name' => 'ipFollowUpsEmployees-add', 'guard_name' => 'api','group_name'=>'ipFollowUpsEmployees','se_name'=>'ipFollowUpsEmployees-add','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipFollowUpsEmployees-read', 'guard_name' => 'api','group_name'=>'ipFollowUpsEmployees','se_name'=>'ipFollowUpsEmployees-read','belongs_to'=>'2']);

        Permission::create(['name' => 'ipFollowUpsEmployees-edit', 'guard_name' => 'api','group_name'=>'ipFollowUpsEmployees','se_name'=>'ipFollowUpsEmployees-edit','belongs_to'=>'2']);
        
        Permission::create(['name' => 'ipFollowUpsEmployees-delete', 'guard_name' => 'api','group_name'=>'ipFollowUpsEmployees','se_name'=>'ipFollowUpsEmployees-delete','belongs_to'=>'2']);


        Permission::create(['name' => 'categoryTypes-browse', 'guard_name' => 'api','group_name'=>'categoryTypes','se_name'=>'categoryTypes-browse','belongs_to'=>'3']);

        Permission::create(['name' => 'categoryTypes-add', 'guard_name' => 'api','group_name'=>'categoryTypes','se_name'=>'categoryTypes-add','belongs_to'=>'3']);
        
        Permission::create(['name' => 'categoryTypes-read', 'guard_name' => 'api','group_name'=>'categoryTypes','se_name'=>'categoryTypes-read','belongs_to'=>'3']);

        Permission::create(['name' => 'categoryTypes-edit', 'guard_name' => 'api','group_name'=>'categoryTypes','se_name'=>'categoryTypes-edit','belongs_to'=>'3']);
        
        Permission::create(['name' => 'categoryTypes-delete', 'guard_name' => 'api','group_name'=>'categoryTypes','se_name'=>'categoryTypes-delete','belongs_to'=>'3']);

          
        Permission::create(['name' => 'reports-delete', 'guard_name' => 'api','group_name'=>'reports','se_name'=>'reports-delete','belongs_to'=>'3']);


    }
}
