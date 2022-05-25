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
            //super admin and super admin employee
        	if($userType->id==1 || $userType->id==16){
                $adminPermissions = Permission::select('id','name')
                    ->whereIn('belongs_to',['1','3'])
                    ->get();
        		$this->addPermissions($adminPermissions,$userType->id);
        	}

            //Company, Branch and company & branch employee
            elseif($userType->id==2 || $userType->id==3 || $userType->id==11){
        		$companyPermissions = Permission::select('id','name')
                    ->whereIn('belongs_to',['2','3'])
                    ->get();
                $this->addPermissions($companyPermissions,$userType->id);
        	}
        	//other then super admin, company, branch and employee
            elseif($userType->id!=1 || $userType->id!=2 || $userType->id!=3 || $userType->id!=11)
        	{
                $defaultPermission = Permission::select('id','name')
                    ->whereIn('id',['11'])
                    ->get(); 
        		$this->addPermissions($defaultPermission,$userType->id);
        	}

            //////////////////////////////////////////
            //////////////Other Permissions///////////
            //////////////////////////////////////////
            //Query
            /*
            select `user_type_has_permissions`.`permission_id`, `permissions`.`name`  from `user_type_has_permissions` inner join `permissions` on `user_type_has_permissions`.`permission_id` = `permissions`.`id` where  `user_type_has_permissions`.`user_type_id` = 7
            */

            if(in_array($userType->id, [4,5,6,7,8,9,10,12,13,14,15]))
            {
                //Doctor
                if($userType->id==4)
                {
                    $jsonObj = null;
                }
                //Nurse
                elseif($userType->id==5)
                {
                    $jsonObj = null;
                }
                //Patient, caretaker, FamilyMember, careTakerFamily & Guardian
                elseif($userType->id==6 || $userType->id==7 || $userType->id==8 || $userType->id==10 || $userType->id==12)
                {
                    $jsonObj = '[
                        {"permission_id":"11","name":"dashboard-browse"},
                        {"permission_id":"21","name":"users-browse"},
                        {"permission_id":"23","name":"users-read"},
                        {"permission_id":"71","name":"patients-browse"},
                        {"permission_id":"72","name":"patients-add"},
                        {"permission_id":"73","name":"patients-read"},
                        {"permission_id":"74","name":"patients-edit"},
                        {"permission_id":"75","name":"patients-delete"},
                        {"permission_id":"81","name":"journal-browse"},
                        {"permission_id":"83","name":"journal-read"},
                        {"permission_id":"86","name":"deviation-browse"},
                        {"permission_id":"88","name":"deviation-read"},
                        {"permission_id":"96","name":"persons-browse"},
                        {"permission_id":"97","name":"persons-add"},
                        {"permission_id":"98","name":"persons-read"},
                        {"permission_id":"99","name":"persons-edit"},
                        {"permission_id":"100","name":"persons-delete"},
                        {"permission_id":"106","name":"branch-browse"},
                        {"permission_id":"108","name":"branch-read"},
                        {"permission_id":"111","name":"ip-browse"},
                        {"permission_id":"121","name":"activity-browse"},
                        {"permission_id":"123","name":"activity-read"},
                        {"permission_id":"137","name":"task-browse"},
                        {"permission_id":"139","name":"task-read"},
                        {"permission_id":"160","name":"patientimport-add"},
                        {"permission_id":"161","name":"files-browse"},
                        {"permission_id":"171","name":"patient_cashiers"},
                        {"permission_id":"172","name":"patient_cashier-add"}
                    ]';
                    
                }
                //Presented
                elseif($userType->id==13)
                {
                    $jsonObj = null;
                }
                //Participated
                elseif($userType->id==14)
                {
                    $jsonObj = null;
                }
                //Other
                elseif($userType->id==15)
                {
                    $jsonObj = null;
                }
            $jsonArr = json_decode($jsonObj, true);
            $this->addPermissionOtherUser($jsonArr, $userType->id);
            }
        }
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
