<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        	\DB::table('users')->delete();
            $adminUser = new User();
            $adminUser->user_type_id      	= '1';
            $adminUser->company_type_id      = '1';
            $adminUser->name      			= 'NRT';
            $adminUser->email      			= 'admin@gmail.com';
            $adminUser->password      		= \Hash::make(12345678);
            $adminUser->contact_number      	= '8103099592';
            $adminUser->status      			= '1';
            $adminUser->save();
            $adminUser->assignRole('admin');

    }
}
