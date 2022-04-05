<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\UserType;
use App\Models\User;
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
        $role1 = Role::create(['id' => '1','name' => 'Admin','se_name' => 'Super Admin', 'guard_name' => 'api','is_default'=>'0']);
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

        \DB::table('user_types')->truncate();
        $UserType1 = UserType::create(['id' => '1','name' => 'Super Admin']);
        $UserType2 = UserType::create(['id' => '2','name' => 'Company']);
        $UserType3 = UserType::create(['id' => '3','name' => 'Employee']);
        $UserType4 = UserType::create(['id' => '4','name' => 'Hospital']);
        $UserType5 = UserType::create(['id' => '5','name' => 'Nurse']);
        $UserType6 = UserType::create(['id' => '6','name' => 'Patient']);
        $UserType7 = UserType::create(['id' => '7','name' => 'careTaker']);
        $UserType8 = UserType::create(['id' => '8','name' => 'FamilyMember']);
        $UserType9 = UserType::create(['id' => '9','name' => 'ContactPerson']);
        $UserType10 = UserType::create(['id' => '10','name' => 'careTakerFamily']);
        $UserType11 = UserType::create(['id' => '11','name' => 'Branch']);

        \DB::table('employee_types')->truncate();
        $patientType1 = DB::table('employee_types')::create(['id' => '1','type'=>'patient','designation' => 'Minor Child']);
        $patientType2 = DB::table('employee_types')::create(['id' => '2','type'=>'patient','designation' => 'Student']);
        $patientType3 = DB::table('employee_types')::create(['id' => '3','type'=>'patient','designation' => 'Working']);
        $patientType4 = DB::table('employee_types')::create(['id' => '4','type'=>'patient','designation' => 'Old age']);
    }
}
