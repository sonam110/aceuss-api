<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'admin']);
       
        $role2 = Role::create(['name' => 'Company']);
       
        $role3 = Role::create(['name' => 'Employee']);
        $role4 = Role::create(['name' => 'Patient']);
        $role5 = Role::create(['name' => 'Caretaker']);
        $role6 = Role::create(['name' => 'FamilyMember']);
        $role7 = Role::create(['name' => 'ContactPerson']);
        $role8 = Role::create(['name' => 'Nurse']);
        $role9 = Role::create(['name' => 'Hospital']);
    }
}
