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
use App\Models\Journal;
use App\Models\Deviation;
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
            if($user->user_type_id == 1 || $user->user_type_id == 16)
            {
                $userData = User::select(
                        \DB::raw('COUNT(IF(users.user_type_id = 2, 0, NULL)) as companyCount'),
                        \DB::raw('COUNT(IF(users.user_type_id NOT IN (1,2), 0, NULL)) as userCount'),
                        \DB::raw('COUNT(IF(users.user_type_id = 16, 0, NULL)) as employeeCount'),
                        \DB::raw('COUNT(IF(users.licence_key IS NOT NULL, 0, NULL)) as licenceCount')
                    )
                ->first();
                $data['companyCount'] = $userData->companyCount;
                $data['userCount'] = $userData->userCount;
                $data['employeeCount'] = $userData->employeeCount;
                $data['licenceCount'] = $userData->licenceCount;

                $data['packageCount'] = Package::count();
                $data['moduelCount'] = Module::count();
                $data['taskCount'] = Task::where(function($q) use ($user) {
                    $q->where('top_most_parent_id', $user->id)
                        ->orWhereNull('top_most_parent_id');

                })->where('is_latest_entry', 1)->count();
            }
            elseif($user->user_type_id == 2)
            {
                $userData = User::select(
                        \DB::raw('COUNT(IF(users.top_most_parent_id = "'.$user->id.'" AND users.user_type_id = 3, 0, NULL)) as employeeCount'),
                        \DB::raw('COUNT(IF(users.top_most_parent_id = "'.$user->id.'" AND users.user_type_id = 6, 0, NULL)) as patientCount'),
                        \DB::raw('COUNT(IF(users.top_most_parent_id = "'.$user->id.'" AND users.user_type_id = 11, 0, NULL)) as branchCount')
                    )
                ->first();

                $data['employeeCount'] = $userData->employeeCount;
                $data['patientCount'] = $userData->patientCount;
                $data['branchCount'] = $userData->branchCount;
                $data['departmentCount'] = Department::where('top_most_parent_id',$user->id)->count();

                $data['deviationCount'] = Deviation::where('top_most_parent_id',$user->id)->count();

                $data['journalCount'] = Journal::where('top_most_parent_id',$user->id)->count();

                $activityData = Activity::select(
                        \DB::raw('COUNT(IF(activities.top_most_parent_id = "'.$user->id.'" AND activities.is_latest_entry = "1", 0, NULL)) as activityCount'),
                        \DB::raw('COUNT(IF(activities.top_most_parent_id = "'.$user->id.'" AND activities.status = "0" AND activities.is_latest_entry = "1", 0, NULL)) as activityPendingCount'),
                        \DB::raw('COUNT(IF(activities.top_most_parent_id = "'.$user->id.'" AND activities.status = "1" AND activities.is_latest_entry = "1", 0, NULL)) as activityCompleteCount'),
                        \DB::raw('COUNT(IF(activities.top_most_parent_id = "'.$user->id.'" AND activities.status = "2" AND activities.is_latest_entry = "1", 0, NULL)) as activityNotDoneCount'),
                        \DB::raw('COUNT(IF(activities.top_most_parent_id = "'.$user->id.'" AND activities.status = "3" AND activities.is_latest_entry = "1", 0, NULL)) as activityNotApplicableCount')
                    )
                ->first();
                $data['activityCount'] = $activityData->activityCount;
                $data['activityPendingCount'] = $activityData->activityPendingCount;
                $data['activityCompleteCount'] = $activityData->activityCompleteCount;
                $data['activityNotDoneCount'] = $activityData->activityNotDoneCount;
                $data['activityNotApplicableCount'] =$activityData->activityNotApplicableCount;

                $ipData = PatientImplementationPlan::select(
                        \DB::raw('COUNT(IF(patient_implementation_plans.top_most_parent_id = "'.$user->id.'" AND patient_implementation_plans.is_latest_entry = "1", 0, NULL)) as ipCount'),
                        \DB::raw('COUNT(IF(patient_implementation_plans.top_most_parent_id = "'.$user->id.'" AND patient_implementation_plans.status = "2" AND patient_implementation_plans.is_latest_entry = "1", 0, NULL)) as ipCompleteCount'),
                        \DB::raw('COUNT(IF(patient_implementation_plans.top_most_parent_id = "'.$user->id.'" AND patient_implementation_plans.status = "0" AND patient_implementation_plans.is_latest_entry = "1", 0, NULL)) as ipPendingCount'),
                        \DB::raw('COUNT(IF(patient_implementation_plans.top_most_parent_id = "'.$user->id.'" AND patient_implementation_plans.status = "1" AND patient_implementation_plans.is_latest_entry = "1", 0, NULL)) as ipInCompleteCount')
                    )
                ->first();
                $data['ipCount'] = $ipData->ipCount;
                $data['ipCompleteCount'] = $ipData->ipCompleteCount;
                $data['ipPendingCount'] = $ipData->ipPendingCount;
                $data['ipInCompleteCount'] = $ipData->ipInCompleteCount;

                $ipFollowUpData = IpFollowUp::select(
                        \DB::raw('COUNT(IF(ip_follow_ups.top_most_parent_id = "'.$user->id.'" AND ip_follow_ups.is_latest_entry = "1", 0, NULL)) as followupCount'),
                        \DB::raw('COUNT(IF(ip_follow_ups.top_most_parent_id = "'.$user->id.'" AND ip_follow_ups.status = "2" AND ip_follow_ups.is_latest_entry = "1", 0, NULL)) as followupCompleteCount'),
                        \DB::raw('COUNT(IF(ip_follow_ups.top_most_parent_id = "'.$user->id.'" AND ip_follow_ups.status = "0" AND ip_follow_ups.is_latest_entry = "1", 0, NULL)) as followupPendingCount')
                    )
                ->first();
                $data['followupCount'] = $ipFollowUpData->followupCount;
                $data['followupCompleteCount'] = $ipFollowUpData->followupCompleteCount;
                $data['followupPendingCount'] = $ipFollowUpData->followupPendingCount;

                $taskData = Task::select(
                        \DB::raw('COUNT(IF(tasks.top_most_parent_id = "'.$user->id.'" AND tasks.is_latest_entry = "1", 0, NULL)) as taskCount'),
                        \DB::raw('COUNT(IF(tasks.top_most_parent_id = "'.$user->id.'" AND tasks.status = "1" AND tasks.is_latest_entry = "1", 0, NULL)) as taskCompleteCount'),
                        \DB::raw('COUNT(IF(tasks.top_most_parent_id = "'.$user->id.'" AND tasks.status = "0" AND tasks.is_latest_entry = "1", 0, NULL)) as taskPendingCount')
                    )
                ->first();
                $data['taskCount'] = $taskData->taskCount;
                $data['taskCompleteCount'] = $taskData->taskCompleteCount;
                $data['taskPendingCount'] = $taskData->taskPendingCount;  
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
                        \DB::raw('COUNT(IF(assign_tasks.status = 0, 0, NULL)) as taskPendingCount'),
                        \DB::raw('COUNT(IF(assign_tasks.status = 1, 0, NULL)) as taskCompleteCount')
                    )
                    ->join('tasks', 'tasks.id', '=', 'assign_tasks.task_id')
                    ->where('assign_tasks.user_id',$user->id)
                    ->where('tasks.is_latest_entry', 1)
                    ->whereNull('tasks.deleted_at')
                    ->first();
                $data['taskCount'] = $assignedTask->taskCount;
                $data['taskCompleteCount'] = $assignedTask->taskCompleteCount;
                $data['taskPendingCount'] = $assignedTask->taskPendingCount;

                $ipInfo =  PatientImplementationPlan::select(
                        \DB::raw('COUNT(id) as ipCount'),
                        \DB::raw('COUNT(IF(status = 0, 0, NULL)) as ipPendingCount'),
                        \DB::raw('COUNT(IF(status = 2, 0, NULL)) as ipCompleteCount'),
                        \DB::raw('COUNT(IF(status = 1, 0, NULL)) as ipInCompleteCount')
                    )
                    ->whereIn('branch_id',$allChilds)
                    ->where('is_latest_entry', 1)
                    ->first();
                $data['ipCount'] = $ipInfo->ipCount;
                $data['ipCompleteCount'] = $ipInfo->ipCompleteCount;
                $data['ipPendingCount'] = $ipInfo->ipPendingCount;
                $data['ipInCompleteCount'] = $ipInfo->ipInCompleteCount;
            }
            elseif(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
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
                $activityData =  Activity::select(
                        \DB::raw('COUNT(IF((activities.patient_id = "'.$user->id.'" OR activities.patient_id = "'.$user->parent_id.'"), 0, NULL)) as activityCount'),
                        \DB::raw('COUNT(IF((activities.patient_id = "'.$user->id.'" OR activities.patient_id = "'.$user->parent_id.'") AND activities.status = "0", 0, NULL)) as activityPendingCount'),
                        \DB::raw('COUNT(IF((activities.patient_id = "'.$user->id.'" OR activities.patient_id = "'.$user->parent_id.'") AND activities.status = "1", 0, NULL)) as activityCompleteCount'),
                        \DB::raw('COUNT(IF((activities.patient_id = "'.$user->id.'" OR activities.patient_id = "'.$user->parent_id.'") AND activities.status = "2", 0, NULL)) as activityNotDoneCount'),
                        \DB::raw('COUNT(IF((activities.patient_id = "'.$user->id.'" OR activities.patient_id = "'.$user->parent_id.'") AND activities.status = "3", 0, NULL)) as activityNotApplicableCount')
                    )
                    ->whereIn('branch_id',$allChilds)
                    ->where('is_latest_entry', 1)
                    ->first();
                $data['activityCount'] = $activityData->activityCount;
                $data['activityPendingCount'] = $activityData->activityPendingCount;
                $data['activityCompleteCount'] = $activityData->activityCompleteCount;
                $data['activityNotDoneCount'] = $activityData->activityNotDoneCount;
                $data['activityNotApplicableCount'] = $activityData->activityNotApplicableCount;

                $ipInfo =  PatientImplementationPlan::select(
                        \DB::raw('COUNT(id) as ipCount'),
                        \DB::raw('COUNT(IF(status = 0, 0, NULL)) as ipPendingCount'),
                        \DB::raw('COUNT(IF(status = 2, 0, NULL)) as ipCompleteCount'),
                        \DB::raw('COUNT(IF(status = 1, 0, NULL)) as ipInCompleteCount')
                    )
                    ->where(function($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->orWhere('user_id', $user->parent_id);
                    })
                    ->where('is_latest_entry', 1)
                    ->first();
                $data['ipCount'] = $ipInfo->ipCount;
                $data['ipCompleteCount'] = $ipInfo->ipCompleteCount;
                $data['ipPendingCount'] = $ipInfo->ipPendingCount;
                $data['ipInCompleteCount'] = $ipInfo->ipInCompleteCount;

                $taskData = Task::select(
                        \DB::raw('COUNT(id) as taskCount'),
                        \DB::raw('COUNT(IF(tasks.status = "1", 0, NULL)) as taskCompleteCount'),
                        \DB::raw('COUNT(IF(tasks.status = "0", 0, NULL)) as taskPendingCount')
                    )
                ->where(function($q) use ($user) {
                    $q->where('resource_id', $user->id)
                        ->orWhere('resource_id', $user->parent_id)
                        ->orWhere('patient_id', $user->parent_id);
                })
                ->where('is_latest_entry', 1)
                ->first();
                $data['taskCount'] = $taskData->taskCount;
                $data['taskCompleteCount'] = $taskData->taskCompleteCount;
                $data['taskPendingCount'] = $taskData->taskPendingCount;
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

                $activityData = Activity::select(
                        \DB::raw('COUNT(id) as activityCount'),
                        \DB::raw('COUNT(IF(activities.branch_id = "'.$user->id.'" AND activities.status = "0", 0, NULL)) as activityPendingCount'),
                        \DB::raw('COUNT(IF(activities.branch_id = "'.$user->id.'" AND activities.status = "1", 0, NULL)) as activityCompleteCount'),
                        \DB::raw('COUNT(IF(activities.branch_id = "'.$user->id.'" AND activities.status = "2", 0, NULL)) as activityNotDoneCount'),
                        \DB::raw('COUNT(IF(activities.branch_id = "'.$user->id.'" AND activities.status = "3", 0, NULL)) as activityNotApplicableCount')
                    )
                ->whereIn('branch_id',$allChilds)
                ->where('is_latest_entry', 1)
                ->first();
                $data['activityCount'] = $activityData->activityCount;
                $data['activityPendingCount'] = $activityData->activityPendingCount;
                $data['activityCompleteCount'] = $activityData->activityCompleteCount;
                $data['activityNotDoneCount'] = $activityData->activityNotDoneCount;
                $data['activityNotApplicableCount'] =$activityData->activityNotApplicableCount;
                
                $taskData = Task::select(
                        \DB::raw('COUNT(id) as taskCount'),
                        \DB::raw('COUNT(IF(tasks.status = "1", 0, NULL)) as taskCompleteCount'),
                        \DB::raw('COUNT(IF(tasks.status = "0", 0, NULL)) as taskPendingCount')
                    )
                ->whereIn('branch_id',$allChilds)
                ->where('is_latest_entry', 1)
                ->first();
                $data['taskCount'] = $taskData->taskCount;
                $data['taskCompleteCount'] = $taskData->taskCompleteCount;
                $data['taskPendingCount'] = $taskData->taskPendingCount; 

                $ipData = PatientImplementationPlan::select(
                        \DB::raw('COUNT(id) as ipCount'),
                        \DB::raw('COUNT(IF(patient_implementation_plans.status = "2", 0, NULL)) as ipCompleteCount'),
                        \DB::raw('COUNT(IF(patient_implementation_plans.status = "0", 0, NULL)) as ipPendingCount'),
                        \DB::raw('COUNT(IF(patient_implementation_plans.status = "1", 0, NULL)) as ipInCompleteCount')
                    )
                ->whereIn('branch_id',$allChilds)
                ->where('is_latest_entry', 1)
                ->first();

                $data['ipCount'] = $ipData->ipCount;
                $data['ipCompleteCount'] = $ipData->ipCompleteCount;
                $data['ipPendingCount'] = $ipData->ipPendingCount;
                $data['ipInCompleteCount'] = $ipData->ipInCompleteCount;
                
                $userData = User::select(
                        \DB::raw('COUNT(IF(users.user_type_id = 3, 0, NULL)) as employeeCount'),
                        \DB::raw('COUNT(IF(users.user_type_id = 6, 0, NULL)) as patientCount'),
                        \DB::raw('COUNT(IF(users.user_type_id = 11, 0, NULL)) as branchCount')
                    )
                ->whereIn('branch_id',$allChilds)
                ->first();

                $data['employeeCount'] = $userData->employeeCount;
                $data['patientCount'] = $userData->patientCount;
                $data['branchCount'] = $userData->branchCount;
                $data['departmentCount'] = Department::whereIn('branch_id',$allChilds)->count();

                $ipFollowUpData = IpFollowUp::select(
                        \DB::raw('COUNT(id) as followupCount'),
                        \DB::raw('COUNT(IF(ip_follow_ups.status = "2", 0, NULL)) as followupCompleteCount'),
                        \DB::raw('COUNT(IF(ip_follow_ups.status = "0", 0, NULL)) as followupPendingCount')
                    )
                ->whereIn('branch_id',$allChilds)
                ->where('is_latest_entry', 1)
                ->first();

                $data['followupCount'] = $ipFollowUpData->followupCount;
                $data['followupCompleteCount'] = $ipFollowUpData->followupCompleteCount;
                $data['followupPendingCount'] = $ipFollowUpData->followupPendingCount;

            }
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats') ,$data, config('httpcodes.success'));    
        } catch(Exception $exception) {
                logException($exception);
                return prepareResult(false, $exception->getMessage(),$exception->getMessage(), config('httpcodes.internal_server_error'));   
        }  
    }
}
