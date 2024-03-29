<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Language;
use App\Models\Package;
use App\Models\CompanyType;
use App\Models\CategoryType;
use App\Models\CompanySetting;
use DB;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        	
            $adminUser = new User();
            $adminUser->id      	            = '1';
            $adminUser->unique_id               = generateRandomNumber();
            $adminUser->user_type_id            = '1';
            $adminUser->role_id                 = '1';
            $adminUser->company_type_id         = null;
            $adminUser->top_most_parent_id      = '1';
            $adminUser->name      			    = 'Aceuss admin';
            $adminUser->email      			    = 'admin@aceuss.se';
            $adminUser->password      		    = \Hash::make('@$$euSS@Nrt&2023');
            $adminUser->contact_number      	= '8103099592';
            $adminUser->status      			= '1';
            $adminUser->save();

            $addSettings = new CompanySetting;
            $addSettings->user_id = $adminUser->id;
            $addSettings->company_name = $adminUser->name;
            $addSettings->company_email = $adminUser->email;
            $addSettings->company_contact = $adminUser->contact_number;
            $addSettings->company_address = 'Sweden';
            $addSettings->contact_person_name = $adminUser->name;
            $addSettings->contact_person_email = $adminUser->email;
            $addSettings->contact_person_phone = $adminUser->contact_number;
            $addSettings->company_website = 'https://aceuss.se';
            $addSettings->company_logo = env('APP_URL').'uploads/no-image.png';
            $addSettings->save();

            $adminRole = Role::where('id','1')->first();
            $companyRole = Role::where('id','2')->first();
            $branchRole = Role::where('id','11')->first();
            $employeeRole = Role::where('id','3')->first();

            $adminUser->assignRole($adminRole);
            

            /*$adminPermissions = Permission::select('id','name')->whereIn('belongs_to',['1','3'])->get();
            foreach ($adminPermissions as $key => $permission) {
                $addedPermission = $permission->name;
                $adminRole->givePermissionTo($addedPermission);
            }


            $companyPermissions = Permission::select('id','name')->whereIn('belongs_to',['2','3'])->get();
            foreach ($companyPermissions as $key => $permission) {
                $addedPermission = $permission->name;
                $companyRole->givePermissionTo($addedPermission);
                $branchRole->givePermissionTo($addedPermission);
                $employeeRole->givePermissionTo($addedPermission);
            }

           
            $roles = Role::whereNotIn('id',['1','2','11','16'])->get();
            foreach ($roles as $key => $role) {
                $defaultPermission = Permission::select('id','name')->where('id','11')->first(); 
                $role->givePermissionTo($defaultPermission->name);
            }*/

            $companyType1 = CompanyType::create(['id' => '1','created_by'=>'1','name' => 'Group Living']);
            $companyType2 = CompanyType::create(['id' => '2','created_by'=>'1','name' => 'Home Living']);
            $companyType3 = CompanyType::create(['id' => '3','created_by'=>'1','name' => 'Single Living']);

            $cataegoryType1 = CategoryType::create(['id' => '1','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Activity']);
            $cataegoryType2 = CategoryType::create(['id' => '2','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Other']);
            $cataegoryType3 = CategoryType::create(['id' => '3','created_by'=>'1','top_most_parent_id'=>'1','name' => 'User']);
            $cataegoryType4 = CategoryType::create(['id' => '4','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Deviation']);
            $cataegoryType5 = CategoryType::create(['id' => '5','created_by'=>'1','top_most_parent_id'=>'1','name' => 'FollowUps']);
            $cataegoryType6 = CategoryType::create(['id' => '6','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Journal']);
            $cataegoryType7 = CategoryType::create(['id' => '7','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Patient']);
            $cataegoryType8 = CategoryType::create(['id' => '8','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Employee']);
            $cataegoryType8 = CategoryType::create(['id' => '9','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Other']);

    }
}
