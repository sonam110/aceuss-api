<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use App\Models\User;
use App\Models\Package;
use App\Models\Module;
use App\Models\Department;
use App\Models\Activity;
use App\Models\IpFollowUp;
use App\Models\PatientImplementationPlan;
use App\Models\Task;
use App\Models\ActivityAssigne;
use App\Models\AssignTask;
use App\Models\IpAssigneToEmployee;

class DashboardController extends Controller
{
    public function dashboard()
    {
        try {
            $user = getUser();
            $data = [];
            if($user->user_type_id == '1')
            {
            	$date['companyCount'] = User::where('user_type_id','2')->count();
            	$date['packageCount'] = Package::count();
            	$date['moduelCount'] = Module::count();
            	$date['userCount'] = User::whereNotIn('user_type_id',['1','2'])->count();
            	$date['licenseCount'] = User::whereNotNull('license_key')->count();
            }

            if($user->user_type_id == '2')
            {
            	$date['employeeCount'] = User::where('top_most_parent_id',$user->id)->where('user_type_id','3')->count();
            	$date['patientCount'] = User::where('top_most_parent_id',$user->id)->where('user_type_id','6')->count();
            	$date['branchCount'] = User::where('top_most_parent_id',$user->id)->where('user_type_id','11')->count();
            	$date['departmentCount'] = Department::where('top_most_parent_id',$user->id)->count();
            	$date['activityCount'] = Activity::where('top_most_parent_id',$user->id)->count();
            	$date['activityCompleteCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','1')->count();
            	$date['activityPendingCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','0')->count();
            	$date['ipCount'] = PatientImplementationPlan::where('top_most_parent_id',$user->id)->count();
            	$date['ipCompleteCount'] = PatientImplementationPlan::where('top_most_parent_id',$user->id)->where('status','1')->count();
            	$date['ipPendingCount'] = PatientImplementationPlan::where('top_most_parent_id',$user->id)->where('status','0')->count();
            	$date['followupCount'] = IpFollowUp::where('top_most_parent_id',$user->id)->count();
            	$date['followupCompleteCount'] = IpFollowUp::where('top_most_parent_id',$user->id)->where('status','2')->count();
            	$date['followupPendingCount'] = IpFollowUp::where('top_most_parent_id',$user->id)->where('status','0')->count();
            	$date['taskCount'] = Task::where('top_most_parent_id',$user->id)->count();
            	$date['taskCompleteCount'] = Task::where('top_most_parent_id',$user->id)->where('status','1')->count();
            	$date['taskPendingCount'] = Task::where('top_most_parent_id',$user->id)->where('status','0')->count();	
            }

            if($user->user_type_id == '3')
            {
                $date['activityCount'] = ActivityAssigne::where('user_id',$user->id)->count();
                $date['activityCompleteCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','1')->count();
                $date['activityPendingCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','0')->count();
                $date['taskCount'] = AssignTask::where('user_id',$user->id)->count();
                $date['AssignTaskCompleteCount'] = AssignTask::where('user_id',$user->id)->where('status','1')->count();
                $date['AssignTaskPendingCount'] = AssignTask::where('user_id',$user->id)->where('status','0')->count();
                $date['ipCount'] = IpAssigneToEmployee::where('user_id',$user->id)->count();
                $date['ipCompleteCount'] = IpAssigneToEmployee::where('user_id',$user->id)->where('status','1')->count();
                $date['ipPendingCount'] = IpAssigneToEmployee::where('user_id',$user->id)->where('status','0')->count();
            }

            if($user->user_type_id == '6')
            {
                $date['activityCount'] = Activity::where('patient_id',$user->id)->count();
                $date['activityCompleteCount'] = Activity::where('patient_id',$user->id)->where('status','1')->count();
                $date['activityPendingCount'] = Activity::where('patient_id',$user->id)->where('status','0')->count();
                $date['ipCount'] = PatientImplementationPlan::where('user_id',$user->id)->count();
                $date['ipCompleteCount'] = PatientImplementationPlan::where('user_id',$user->id)->where('status','1')->count();
                $date['ipPendingCount'] = PatientImplementationPlan::where('user_id',$user->id)->where('status','0')->count();
            }
            return prepareResult(true,'Dashboard' ,$date, config('httpcodes.success'));    
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));   
        }  
    }


    public function activityFilter()
    {
        try {
            $user = getUser();

            if($user->user_type_id == '2')
            {
                $data = Activity::where('top_most_parent_id',$user->id)->count();
                $date['activityCompleteCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','1')->count();
                $date['activityPendingCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','0')->count();
            }

            if($user->user_type_id == '3')
            {
                $data = ActivityAssigne::where('user_id',$user->id)->count();
                $date['activityCompleteCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','1')->count();
                $date['activityPendingCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','0')->count();
                
            }

            if($user->user_type_id == '6')
            {
                $data = Activity::where('patient_id',$user->id)->count();
                $date['activityCompleteCount'] = Activity::where('patient_id',$user->id)->where('status','1')->count();
                $date['activityPendingCount'] = Activity::where('patient_id',$user->id)->where('status','0')->count();
            }
            return prepareResult(true,'Dashboard' ,$date, config('httpcodes.success'));    
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));   
        }  
    }
}
