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

            if($userType->id  == '3'){
                $this->addPermissions($companyPermissions,$userType->id);
            }
        	if($userType->id  == '11'){
        		$this->addPermissions($companyPermissions,$userType->id);
        	}
        	$defaultPermission = Permission::select('id','name')->whereIn('id',['11'])->get(); 
            if($userType->id  != '1' || $userType->id != '2' || $userType->id != '3' || $userType->id != '11')
        	{
        		$this->addPermissions($defaultPermission,$userType->id);
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
