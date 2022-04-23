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
       
        
        //$this->call(PermissionSeeder::class);
        $this->call(AgencySeeder::class);
        $this->call(MailSmsTemplateSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UserTypePermissionSeeder::class);
        $this->call(LabelSeeder::class);
        $this->call(DefaultEntrySeeder::class);
    }
}
