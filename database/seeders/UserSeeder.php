<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Language;
use App\Models\Package;
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
        	
          /*  $adminUser = new User();
            $adminUser->id      	            = '1';
            $adminUser->unique_id               = generateRandomNumber();
            $adminUser->user_type_id            = '1';
            $adminUser->role_id                 = '1';
            $adminUser->company_type_id         = null;
            $adminUser->top_most_parent_id      = '1';
            $adminUser->name      			    = 'admin';
            $adminUser->email      			    = 'admin@gmail.com';
            $adminUser->password      		    = \Hash::make(12345678);
            $adminUser->contact_number      	= '8103099592';
            $adminUser->status      			= '1';
            $adminUser->save();*/

            $adminRole = Role::where('id','1')->first();
            $companyRole = Role::where('id','2')->first();
            $branchRole = Role::where('id','11')->first();

           /* $adminUser->assignRole($adminRole);*/
            

            $adminPermissions = Permission::select('id','name')->whereIn('belongs_to',['1','3'])->get();
            foreach ($adminPermissions as $key => $permission) {
                $addedPermission = $permission->name;
                $adminRole->givePermissionTo($addedPermission);
            }


            $companyPermissions = Permission::select('id','name')->whereIn('belongs_to',['2','3'])->get();
            foreach ($companyPermissions as $key => $permission) {
                $addedPermission = $permission->name;
                $companyRole->givePermissionTo($addedPermission);
                $branchRole->givePermissionTo($addedPermission);
            }


         


    }
}
