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
    }

    public function addPermissions($permission,$user_type_id)
    {
    	foreach ($permission as $key => $per) {
    		$addPermission = new UserTypeHasPermission;
    		$addPermission->user_type_id = $user_type_id;
    		$addPermission->permission_id = $per->id;
    		$addPermission->save();
    	}
    }

    public function addPermissionOtherUser($permissions,$user_type_id){
        if(!empty($permissions))
        {
            foreach ($permissions as $key => $per) 
            {
                if(UserTypeHasPermission::where('user_type_id', $user_type_id)->where('permission_id', $per['permission_id'])->count()<1)
                {
                    $addPermission = new UserTypeHasPermission;
                    $addPermission->user_type_id = $user_type_id;
                    $addPermission->permission_id = $per['permission_id'];
                    $addPermission->save();
                }
            }
        }
    }

}
