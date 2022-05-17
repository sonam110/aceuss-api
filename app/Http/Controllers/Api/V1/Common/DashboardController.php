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
            if($user->user_type_id == 1)
            {
                $data['companyCount'] = User::where('user_type_id','2')->count();
                $data['packageCount'] = Package::count();
                $data['moduelCount'] = Module::count();
                $data['userCount'] = User::whereNotIn('user_type_id',['1','2'])->count();
                $data['licenseCount'] = User::whereNotNull('license_key')->count();
            }
            elseif($user->user_type_id == 2)
            {
                $data['employeeCount'] = User::where('top_most_parent_id',$user->id)->where('user_type_id','3')->count();
                $data['patientCount'] = User::where('top_most_parent_id',$user->id)->where('user_type_id','6')->count();
                $data['branchCount'] = User::where('top_most_parent_id',$user->id)->where('user_type_id','11')->count();
                $data['departmentCount'] = Department::where('top_most_parent_id',$user->id)->count();
                $data['activityCount'] = Activity::where('top_most_parent_id',$user->id)->where('is_latest_entry', 1)->count();
                $data['activityPendingCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();
                $data['activityCompleteCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['activityNotDoneCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','2')->where('is_latest_entry', 1)->count();
                $data['activityNotApplicableCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','3')->where('is_latest_entry', 1)->count();
                
                $data['ipCount'] = PatientImplementationPlan::where('top_most_parent_id',$user->id)->where('is_latest_entry', 1)->count();
                $data['ipCompleteCount'] = PatientImplementationPlan::where('top_most_parent_id',$user->id)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['ipPendingCount'] = PatientImplementationPlan::where('top_most_parent_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();
                
                $data['followupCount'] = IpFollowUp::where('top_most_parent_id',$user->id)->where('is_latest_entry', 1)->count();
                $data['followupCompleteCount'] = IpFollowUp::where('top_most_parent_id',$user->id)->where('status','2')->where('is_latest_entry', 1)->count();
                $data['followupPendingCount'] = IpFollowUp::where('top_most_parent_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();
                
                $data['taskCount'] = Task::where('top_most_parent_id',$user->id)->where('is_latest_entry', 1)->count();
                $data['taskCompleteCount'] = Task::where('top_most_parent_id',$user->id)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['taskPendingCount'] = Task::where('top_most_parent_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();  
            }
            elseif($user->user_type_id == 3)
            {
                $user = getUser();
                $branch_id = (!empty($user->branch_id)) ? $user->branch_id : $user->id;
                $branchids = branchChilds($branch_id);
                $allChilds = array_merge($branchids,[$branch_id]);

                $data['activityCount'] = Activity::where('user_id',$user->id)->where('is_latest_entry', 1)->count();
                $data['activityPendingCount'] = Activity::where('user_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();
                $data['activityCompleteCount'] = Activity::where('user_id',$user->id)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['activityNotDoneCount'] = Activity::where('user_id',$user->id)->where('status','2')->where('is_latest_entry', 1)->count();
                $data['activityNotApplicableCount'] = Activity::where('user_id',$user->id)->where('status','3')->where('is_latest_entry', 1)->count();
                $data['taskCount'] = AssignTask::where('user_id',$user->id)->count();
                $data['AssignTaskCompleteCount'] = AssignTask::where('user_id',$user->id)->where('status','1')->count();
                $data['AssignTaskPendingCount'] = AssignTask::where('user_id',$user->id)->where('status','0')->count();
                
                $data['ipCount'] = PatientImplementationPlan::whereIn('branch_id',$allChilds)->where('is_latest_entry', 1)->count();
                $data['ipCompleteCount'] = PatientImplementationPlan::whereIn('branch_id',$allChilds)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['ipPendingCount'] = PatientImplementationPlan::whereIn('branch_id',$allChilds)->where('status','0')->where('is_latest_entry', 1)->count();

            }

            elseif(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
            {
                $data['activityCount'] = Activity::where(function ($q) use ($user) {
                    $q->where('patient_id', $user->id)
                        ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('is_latest_entry', 1)
                    ->count();
                $data['activityPendingCount'] = Activity::where(function ($q) use ($user) {
                    $q->where('patient_id', $user->id)
                        ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('status','0')
                    ->where('is_latest_entry', 1)
                    ->count();
                $data['activityCompleteCount'] = Activity::where(function ($q) use ($user) {
                    $q->where('patient_id', $user->id)
                        ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('status','1')
                    ->where('is_latest_entry', 1)
                    ->count();
                $data['activityNotDoneCount'] = Activity::where(function ($q) use ($user) {
                    $q->where('patient_id', $user->id)
                        ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('status','2')
                    ->where('is_latest_entry', 1)
                    ->count();
                $data['activityNotApplicableCount'] = Activity::where(function ($q) use ($user) {
                    $q->where('patient_id', $user->id)
                        ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('status','3')
                    ->where('is_latest_entry', 1)
                    ->count();

                $data['ipCount'] = PatientImplementationPlan::where('user_id',$user->id)->where('is_latest_entry', 1)->count();
                $data['ipCompleteCount'] = PatientImplementationPlan::where('user_id',$user->id)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['ipPendingCount'] = PatientImplementationPlan::where('user_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();
            }
            else
            {
                $user = getUser();
                $branch_id = (!empty($user->branch_id)) ? $user->branch_id : $user->id;
                $branchids = branchChilds($branch_id);
                $allChilds = array_merge($branchids,[$branch_id]);

                $data['activityCount'] = Activity::whereIn('branch_id',$allChilds)->where('is_latest_entry', 1)->count();
                $data['activityPendingCount'] = Activity::whereIn('branch_id',$allChilds)->where('status','0')->where('is_latest_entry', 1)->count();
                $data['activityCompleteCount'] = Activity::whereIn('branch_id',$allChilds)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['activityNotDoneCount'] = Activity::whereIn('branch_id',$allChilds)->where('status','2')->where('is_latest_entry', 1)->count();
                $data['activityNotApplicableCount'] = Activity::whereIn('branch_id',$allChilds)->where('status','3')->where('is_latest_entry', 1)->count();
                
                $data['taskCount'] = Task::whereIn('branch_id',$allChilds)->where('is_latest_entry', 1)->count();
                $data['taskCompleteCount'] = Task::whereIn('branch_id',$allChilds)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['taskPendingCount'] = Task::whereIn('branch_id',$allChilds)->where('status','0')->where('is_latest_entry', 1)->count();
                
                $data['ipCount'] = PatientImplementationPlan::whereIn('branch_id',$allChilds)->where('is_latest_entry', 1)->count();
                $data['ipCompleteCount'] = PatientImplementationPlan::whereIn('branch_id',$allChilds)->where('status','1')->where('is_latest_entry', 1)->count();
                $data['ipPendingCount'] = PatientImplementationPlan::whereIn('branch_id',$allChilds)->where('status','0')->where('is_latest_entry', 1)->count();

                $data['employeeCount'] = User::whereIn('branch_id',$allChilds)->where('user_type_id','3')->count();
                $data['patientCount'] = User::whereIn('branch_id',$allChilds)->where('user_type_id','6')->count();
                $data['branchCount'] = User::whereIn('branch_id',$allChilds)->where('user_type_id','11')->count();
                $data['departmentCount'] = Department::whereIn('branch_id',$allChilds)->count();

                $data['followupCount'] = IpFollowUp::whereIn('branch_id',$allChilds)->where('is_latest_entry', 1)->count();
                $data['followupCompleteCount'] = IpFollowUp::whereIn('branch_id',$allChilds)->where('status','2')->where('is_latest_entry', 1)->count();
                $data['followupPendingCount'] = IpFollowUp::whereIn('branch_id',$allChilds)->where('status','0')->where('is_latest_entry', 1)->count();
            }
            return prepareResult(true,'Dashboard' ,$data, config('httpcodes.success'));    
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
                    $data['activityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('is_latest_entry', 1)->count();
                    $data['pendingActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','0')->where('is_latest_entry', 1)->count();
                    $data['doneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','1')->where('is_latest_entry', 1)->count();
                    $data['notDoneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','2')->where('is_latest_entry', 1)->count();
                    $data['notApplicableActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','3')->where('is_latest_entry', 1)->count();
                }

                if($user->user_type_id == '3')
                {
                    $data['activityCount'] = ActivityAssigne::where('user_id',$user->id)->where('assignment_date','>=',$request->start_date)->where('is_latest_entry', 1)->count();
                    $data['pendingActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('assignment_date','>=',$request->start_date)->where('status','0')->where('is_latest_entry', 1)->count();
                    $data['doneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('assignment_date','>=',$request->start_date)->where('status','1')->where('is_latest_entry', 1)->count();
                    $data['notDoneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','2')->where('is_latest_entry', 1)->count();
                    $data['notApplicableActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('start_date','>=',$request->start_date)->where('status','3')->where('is_latest_entry', 1)->count();
                
                }

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $data['activityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('start_date','>=',$request->start_date)
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['pendingActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('start_date','>=',$request->start_date)
                    ->where('status','0')
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['doneActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('start_date','>=',$request->start_date)
                    ->where('status','1')
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['notDoneActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })  
                    ->where('start_date','>=',$request->start_date)
                    ->where('status','2')
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['notApplicableActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })  
                    ->where('start_date','>=',$request->start_date)
                    ->where('status','3')
                    ->where('is_latest_entry', 1)
                    ->count();
                }
            }
            else
            {
                if($user->user_type_id == '2')
                {
                    $data['activityCount'] = Activity::where('top_most_parent_id',$user->id)->where('is_latest_entry', 1)->count();
                    $data['doneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','1')->where('is_latest_entry', 1)->count();
                    $data['pendingActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();
                    $data['notDoneActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','2')->where('is_latest_entry', 1)->count();
                    $data['notApplicableActivityCount'] = Activity::where('top_most_parent_id',$user->id)->where('status','3')->where('is_latest_entry', 1)->count();
                
                }

                if($user->user_type_id == '3')
                {
                    $data['activityCount'] = ActivityAssigne::where('user_id',$user->id)->where('is_latest_entry', 1)->count();
                    $data['doneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','1')->where('is_latest_entry', 1)->count();
                    $data['pendingActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','0')->where('is_latest_entry', 1)->count();
                    $data['notDoneActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','2')->where('is_latest_entry', 1)->count();
                    $data['notApplicableActivityCount'] = ActivityAssigne::where('user_id',$user->id)->where('status','3')->where('is_latest_entry', 1)->count();
                
                }

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $data['activityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['pendingActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('status','0')
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['doneActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })
                    ->where('status','1')
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['notDoneActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })  
                    ->where('status','2')
                    ->where('is_latest_entry', 1)
                    ->count();

                    $data['notApplicableActivityCount'] = Activity::where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    })  
                    ->where('status','3')
                    ->where('is_latest_entry', 1)
                    ->count();
                }
            }
            
            return prepareResult(true,'ActivityCount' ,$data, config('httpcodes.success'));    
        } catch(Exception $exception) {
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));   
        }  
    }
}
