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
            if($user->user_type_id == '1' || $user->user_type_id == '16')
            {
                $data['companyCount'] = User::where('user_type_id','2')->count();
                $data['packageCount'] = Package::count();
                $data['moduelCount'] = Module::count();
                $data['userCount'] = User::whereNotIn('user_type_id',['1','2'])->count();
                $data['employeeCount'] = User::where('user_type_id', '16')->count();
                $data['taskCount'] = Task::whereIn('top_most_parent_id',[null,$user->id])->where('is_latest_entry', 1)->count();
                $data['licenceCount'] = User::whereNotNull('licence_key')->count();
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
                if(!empty($user->branch_id)) {
                    $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                    $allChilds[] = $user->id;
                } else {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                }
                
                $assignedActivity =  ActivityAssigne::select(
                        \DB::raw('COUNT(activity_assignes.id) as activityCount'),
                        \DB::raw('COUNT(IF(activity_assignes.status = 0, 0, NULL)) as activityPendingCount'),
                        \DB::raw('COUNT(IF(activity_assignes.status = 1, 0, NULL)) as activityCompleteCount'),
                        \DB::raw('COUNT(IF(activity_assignes.status = 2, 0, NULL)) as activityNotDoneCount'),
                        \DB::raw('COUNT(IF(activity_assignes.status = 3, 0, NULL)) as activityNotApplicableCount')
                    )
                    ->join('activities', 'activities.id', '=', 'activity_assignes.activity_id')
                    ->where('activity_assignes.user_id',$user->id)
                    ->where('activities.is_latest_entry', 1)
                    ->whereNull('activities.deleted_at')
                    ->first();
                $data['activityCount'] = $assignedActivity->activityCount;
                $data['activityPendingCount'] = $assignedActivity->activityPendingCount;
                $data['activityCompleteCount'] = $assignedActivity->activityCompleteCount;
                $data['activityNotDoneCount'] = $assignedActivity->activityNotDoneCount;
                $data['activityNotApplicableCount'] = $assignedActivity->activityNotApplicableCount;

                $assignedTask =  AssignTask::select(
                        \DB::raw('COUNT(assign_tasks.id) as taskCount'),
                        \DB::raw('COUNT(IF(assign_tasks.status = 0, 0, NULL)) as AssignTaskPendingCount'),
                        \DB::raw('COUNT(IF(assign_tasks.status = 1, 0, NULL)) as AssignTaskCompleteCount')
                    )
                    ->join('tasks', 'tasks.id', '=', 'assign_tasks.task_id')
                    ->where('assign_tasks.user_id',$user->id)
                    ->where('tasks.is_latest_entry', 1)
                    ->whereNull('tasks.deleted_at')
                    ->first();
                $data['taskCount'] = $assignedTask->taskCount;
                $data['AssignTaskCompleteCount'] = $assignedTask->AssignTaskCompleteCount;
                $data['AssignTaskPendingCount'] = $assignedTask->AssignTaskPendingCount;


                $ipInfo =  PatientImplementationPlan::select(
                        \DB::raw('COUNT(id) as ipCount'),
                        \DB::raw('COUNT(IF(status = 0, 0, NULL)) as ipPendingCount'),
                        \DB::raw('COUNT(IF(status = 1, 0, NULL)) as ipCompleteCount')
                    )
                    ->whereIn('branch_id',$allChilds)
                    ->where('is_latest_entry', 1)
                    ->first();
                $data['ipCount'] = $ipInfo->ipCount;
                $data['ipCompleteCount'] = $ipInfo->ipCompleteCount;
                $data['ipPendingCount'] = $ipInfo->ipPendingCount;
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
                if(!empty($user->branch_id)) {
                    if($user->user_type_id==11)
                    {
                        $allChilds = userChildBranches(\App\Models\User::find($user->id));
                        $allChilds[] = $user->id;
                    }
                    else
                    {
                        $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                    }
                } else {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                }
                

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
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats') ,$data, config('httpcodes.success'));    
        } catch(Exception $exception) {
                logException($exception);
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
            
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats') ,$data, config('httpcodes.success'));    
        } catch(Exception $exception) {
                logException($exception);
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));
        }  
    }
}
