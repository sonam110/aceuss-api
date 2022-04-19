<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserTypeHasPermission;
use App\Models\UserType;
use DB;
use Spatie\Permission\Models\Permission;
class UserTypePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserTypeHasPermission::truncate();
        $allUserTypes = UserType::get();
        foreach ($allUserTypes as $key => $userType) {
        	$adminPermissions = Permission::select('id','name')->whereIn('belongs_to',['1','3'])->get();
        	if($userType->id  == '1'){
        		$this->addPermissions($adminPermissions,$userType->id);
        	}
        	$companyPermissions = Permission::select('id','name')->whereIn('belongs_to',['2','3'])->get();
        	if($userType->id  == '2'){
        		$this->addPermissions($companyPermissions,$userType->id);
        	}

        	$branchPermissions = Permission::select('id','name')->whereIn('belongs_to',['2','3'])->get();
        	if($userType->id  == '11'){
        		$this->addPermissions($branchPermissions,$userType->id);
        	}

        	$employeePermission = Permission::select('id','name')->whereIn('belongs_to',['2','3'])->get();
        	if($userType->id  == '3'){
        		$this->addPermissions($employeePermission,$userType->id);
        	}
        	$patientPermission = Permission::select('id','name')->whereIn('id',['176','177','178'])->get(); 
        	if($userType->id  == '6'){
        		$this->addPermissions($patientPermission,$userType->id);
        	}
        	
        }


    }

    public function addPermissions($permission,$user_type_id){
    	foreach ($permission as $key => $per) {
    		$addPermission = new UserTypeHasPermission;
    		$addPermission->user_type_id = $user_type_id;
    		$addPermission->permission_id = $per->id;
    		$addPermission->save();
    	}
    }
}
