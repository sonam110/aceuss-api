<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\UserType;
use App\Models\User;
use App\Models\EmployeeType;
use Str;
use DB;
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
       
         
        /*------------Default Role-----------------------------------*/
        $role1 = Role::create(['id' => '1','name' => 'Admin','se_name' => 'Super Admin', 'guard_name' => 'api','is_default'=>'0', 'user_type_id' => 1]);
        $role2 = Role::create(['id' => '2','name' => 'Company','se_name' => 'Company', 'guard_name' => 'api','is_default'=>'0']);
        $role3 = Role::create(['id' => '3','name' => 'Employee','se_name' => 'Employee', 'guard_name' => 'api','is_default'=>'1']);
        $role4 = Role::create(['id' => '4','name' => 'Hospital','se_name' => 'Hospital', 'guard_name' => 'api','is_default'=>'1']);
        $role5 = Role::create(['id' => '5','name' => 'Nuser','se_name' => 'Nuser', 'guard_name' => 'api','is_default'=>'1']);
        $role6 = Role::create(['id' => '6','name' => 'Patient','se_name' => 'Patient', 'guard_name' => 'api','is_default'=>'1']);
        $role7 = Role::create(['id' => '7','name' => 'careTaker','se_name' => 'careTaker', 'guard_name' => 'api','is_default'=>'1']);
        $role8 = Role::create(['id' => '8','name' => 'FamilyMember','se_name' => 'FamilyMember', 'guard_name' => 'api','is_default'=>'1']);
        $role9 = Role::create(['id' => '9','name' => 'ContactPerson','se_name' => 'ContactPerson', 'guard_name' => 'api','is_default'=>'1']);
        $role10 = Role::create(['id' => '10','name' => 'careTakerFamily','se_name' => 'careTakerFamily', 'guard_name' => 'api','is_default'=>'1']);
        $role11 = Role::create(['id' => '11','name' => 'Branch','se_name' => 'Branch', 'guard_name' => 'api','is_default'=>'1']);
        $role12 = Role::create(['id' => '12','name' => 'Guardian','se_name' => 'Guardian', 'guard_name' => 'api','is_default'=>'1']);
        $role13 = Role::create(['id' => '13','name' => 'Presented','se_name' => 'Presented', 'guard_name' => 'api','is_default'=>'1']);
        $role14 = Role::create(['id' => '14','name' => 'Participated','se_name' => 'Participated', 'guard_name' => 'api','is_default'=>'1']);
        $role15 = Role::create(['id' => '15','name' => 'Other','se_name' => 'Other', 'guard_name' => 'api','is_default'=>'1']);
        $role16 = Role::create(['id' => '16','name' => 'Admin Employee','se_name' => 'Admin Employee', 'guard_name' => 'api','is_default'=>'0']);

    }
}
