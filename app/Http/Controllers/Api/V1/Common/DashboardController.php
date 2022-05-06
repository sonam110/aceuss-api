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


    public function activityCount(Request $request)
    {
        try {
            $user = getUser();
            $data = [];
            if(!empty($request->start_date)){
                if($user->user_type_id == '2')
                {
                    $data['activityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->count();
                    $data['doneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','1')->count();
                    $data['pendingActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','0')->count();
                    $data['notDoneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','2')->count();
                    $data['notApplicableActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','3')->count();
                }

                if($user->user_type_id == '3')
                {
                    $data['activityCount'] = ActivityAssigne::where('user_id',$user->id)->where('assignment_date','>=',$request->start_date)->count();
                    $data['doneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('assignment_date','>=',$request->start_date)->where('status','1')->count();
                    $data['pendingActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('assignment_date','>=',$request->start_date)->where('status','0')->count();
                    $data['notDoneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','2')->count();
                    $data['notApplicableActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','3')->count();
                
                }

                if($user->user_type_id == '6')
                {
                    $data['activityCount'] = Activity::where('patient_id',$user->id)->where('start_date','>=',$request->start_date)->count();
                    $data['doneActivityCount'] = Activity::where('patient_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','1')->count();
                    $data['pendingActivityCount'] = Activity::where('patient_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','0')->count();
                    $data['notDoneActivityCount'] = Activity::where('patient_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','2')->count();
                    $data['notApplicableActivityCount'] = Activity::where('patient_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','3')->count();
                
                }
            }
            else{
                if($user->user_type_id == '2')
                {
                    $data['activityCount'] = Activity::where('top_most_parent_id',$user->id)->count();
                    $data['doneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','1')->count();
                    $data['pendingActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','0')->count();
                    $data['notDoneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','2')->count();
                    $data['notApplicableActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','3')->count();
                
                }

                if($user->user_type_id == '3')
                {
                    $data['activityCount'] = ActivityAssigne::where('user_id',$user->id)->count();
                    $data['doneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','1')->count();
                    $data['pendingActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','0')->count();
                    $data['notDoneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','2')->count();
                    $data['notApplicableActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','3')->count();
                
                }

                if($user->user_type_id == '6')
                {
                    $data['activityCount'] = Activity::where('patient_id',$user->id)->count();
                    $data['doneActivityCount'] = Activity::where('patient_id',$user->id)->where('status','1')->count();
                    $data['pendingActivityCount'] = Activity::where('patient_id',$user->id)->where('status','0')->count();
                    $data['notDoneActivityCount'] = Activity::where('patient_id',$user->id)->where('status','2')->count();
                    $data['notApplicableActivityCount'] = Activity::where('patient_id',$user->id)->where('status','3')->count();
                
                }
            }
            
            return prepareResult(true,'ActivityCount' ,$data, config('httpcodes.success'));    
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));   
        }  
    }
}
