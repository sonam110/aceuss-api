<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       \Artisan::call('create:directory');        
        //$this->call(PermissionSeeder::class);
        $this->call(DefaultEntrySeeder::class);
        $this->call(AgencySeeder::class);
        $this->call(MailSmsTemplateSeeder::class);
        //$this->call(RoleSeeder::class);
        //$this->call(PermissionSeeder::class);

        \DB::unprepared(file_get_contents(storage_path('db-backups/permissions.sql')));
        \DB::unprepared(file_get_contents(storage_path('db-backups/roles.sql')));
        \DB::unprepared(file_get_contents(storage_path('db-backups/user_type_has_permissions.sql')));
        \DB::unprepared(file_get_contents(storage_path('db-backups/role_has_permissions.sql')));
        \DB::unprepared(file_get_contents(storage_path('db-backups/bookmark_masters.sql')));

        $this->call(UserSeeder::class);
        $this->call(UserTypePermissionSeeder::class);
        $this->call(CategorySubCat::class);
        $this->call(LabelSeeder::class);
    }
}
