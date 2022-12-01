<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityAssigne;
use App\Models\PatientImplementationPlan;
use Illuminate\Http\Request;

class StatisticsActivityController extends Controller
{
    public function getTWMwiseActivityReport(Request $request)
    {
        $request_for = !empty($request->request_for) ? $request->request_for : 7;
        $datalabels = [];
        $dataset_total_activity = [];
        $dataset_total_compulsory = [];
        $dataset_total_risk = [];
        $dataset_total_pending = [];
        $dataset_total_done = [];
        $dataset_total_not_done = [];
        $dataset_total_not_applicable = [];
        $dataset_total_completed_by_staff_on_time = [];
        $dataset_total_completed_by_staff_not_on_time = [];
        $dataset_total_completed_by_patient_itself = [];
        $dataset_total_patient_did_not_want = [];
        $dataset_total_not_done_by_employee = [];
        if(!empty($request->start_date) && !empty($request->end_date)) 
        {
            $diffrece = dateDifference($request->start_date, $request->end_date) + 1;
            for($i = $diffrece; $i>=1; $i--)
            {
                $date = date("Y-m-d", strtotime('-'.($i-1).' days', strtotime($request->end_date)));
                $datalabels[] = $date;

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
                
                $query = Activity::select([
                    \DB::raw('COUNT(id) as total_activity'),
                    \DB::raw('COUNT(IF(is_compulsory = 1, 0, NULL)) as total_compulsory'),
                    \DB::raw('COUNT(IF(is_risk = 1, 0, NULL)) as total_risk'),
                    \DB::raw('COUNT(IF(is_risk = 1, 0, NULL)) as total_risk'),
                    \DB::raw('COUNT(IF(status = 0, 0, NULL)) as total_pending'),
                    \DB::raw('COUNT(IF(status = 1, 0, NULL)) as total_done'),
                    \DB::raw('COUNT(IF(status = 2, 0, NULL)) as total_not_done'),
                    \DB::raw('COUNT(IF(status = 3, 0, NULL)) as total_not_applicable'),
                    \DB::raw('COUNT(IF(selected_option = "completed-by-staff-on-time", 0, NULL)) as total_completed_by_staff_on_time'),
                    \DB::raw('COUNT(IF(selected_option = "completed-by-staff-not-on-time", 0, NULL)) as total_completed_by_staff_not_on_time'),
                    \DB::raw('COUNT(IF(selected_option = "completed-by-patient-itself", 0, NULL)) as total_completed_by_patient_itself'),
                    \DB::raw('COUNT(IF(selected_option = "patient-did-not-want", 0, NULL)) as total_patient_drecord_not_want'),
                    \DB::raw('COUNT(IF(selected_option = "not-done-by-employee", 0, NULL)) as total_not_done_by_employee'),
                ]);

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $query->where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    });
                }
                
                if($user->user_type_id !='2') 
                {
                    if($user->user_type_id =='3') 
                    {
                        if($user->hasPermissionTo('visible-all-patients-activity'))
                        {
                            $user_records = getAllowUserList('visible-all-patients-activity');
                            $query->whereIn('activities.patient_id', $user_records);
                        }
                        else
                        {
                            $agnActivity  = ActivityAssigne::where('activity_assignes.user_id',$user->id)->pluck('activity_id');
                            $query = $query->whereIn('activities.id',$agnActivity);
                        }
                    }
                    else
                    {
                        $query =  $query->whereIn('branch_id',$allChilds);
                    }
                }

                if(!empty($request->patient_id))
                {
                    $query->where('patient_id', $request->patient_id);
                }

                $query->whereDate('start_date', $date);         
                $result = $query->first();
                $dataset_total_activity[] = $result->total_activity;
                $dataset_total_compulsory[] = $result->total_compulsory;
                $dataset_total_risk[] = $result->total_risk;
                $dataset_total_pending[] = $result->total_pending;
                $dataset_total_done[] = $result->total_done;
                $dataset_total_not_done[] = $result->total_not_done;
                $dataset_total_not_applicable[] = $result->total_not_applicable;
                $dataset_total_completed_by_staff_on_time[] = $result->total_completed_by_staff_on_time;
                $dataset_total_completed_by_staff_not_on_time[] = $result->total_completed_by_staff_not_on_time;
                $dataset_total_completed_by_patient_itself[] = $result->total_completed_by_patient_itself;
                $dataset_total_patient_did_not_want[] = $result->total_patient_drecord_not_want;
                $dataset_total_not_done_by_employee[] = $result->total_not_done_by_employee;
            }
        }
        else
        {
            for($i = $request_for; $i>=1; $i--)
            {
                $date = date('Y-m-d',strtotime('-'.($i-1).' days'));
                $datalabels[] = $date;

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
                
                $query = Activity::select([
                    \DB::raw('COUNT(id) as total_activity'),
                    \DB::raw('COUNT(IF(is_compulsory = 1, 0, NULL)) as total_compulsory'),
                    \DB::raw('COUNT(IF(is_risk = 1, 0, NULL)) as total_risk'),
                    \DB::raw('COUNT(IF(is_risk = 1, 0, NULL)) as total_risk'),
                    \DB::raw('COUNT(IF(status = 0, 0, NULL)) as total_pending'),
                    \DB::raw('COUNT(IF(status = 1, 0, NULL)) as total_done'),
                    \DB::raw('COUNT(IF(status = 2, 0, NULL)) as total_not_done'),
                    \DB::raw('COUNT(IF(status = 3, 0, NULL)) as total_not_applicable'),
                    \DB::raw('COUNT(IF(selected_option = "completed-by-staff-on-time", 0, NULL)) as total_completed_by_staff_on_time'),
                    \DB::raw('COUNT(IF(selected_option = "completed-by-staff-not-on-time", 0, NULL)) as total_completed_by_staff_not_on_time'),
                    \DB::raw('COUNT(IF(selected_option = "completed-by-patient-itself", 0, NULL)) as total_completed_by_patient_itself'),
                    \DB::raw('COUNT(IF(selected_option = "patient-did-not-want", 0, NULL)) as total_patient_drecord_not_want'),
                    \DB::raw('COUNT(IF(selected_option = "not-done-by-employee", 0, NULL)) as total_not_done_by_employee'),
                ]);

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $query->where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    });
                }
                
                if($user->user_type_id !='2') 
                {
                    if($user->user_type_id =='3') 
                    {
                        if($user->hasPermissionTo('visible-all-patients-activity'))
                        {
                            $user_records = getAllowUserList('visible-all-patients-activity');
                            $query->whereIn('activities.patient_id', $user_records);
                        }
                        else
                        {
                            $agnActivity  = ActivityAssigne::where('activity_assignes.user_id',$user->id)->pluck('activity_id');
                            $query = $query->whereIn('activities.id',$agnActivity);
                        }
                    }
                    else
                    {
                        $query =  $query->whereIn('branch_id',$allChilds);
                    }
                }

                if(!empty($request->patient_id))
                {
                    $query->where('patient_id', $request->patient_id);
                }
                $query->whereDate('start_date', $date);         
                $result = $query->first();
                $dataset_total_activity[] = $result->total_activity;
                $dataset_total_compulsory[] = $result->total_compulsory;
                $dataset_total_risk[] = $result->total_risk;
                $dataset_total_pending[] = $result->total_pending;
                $dataset_total_done[] = $result->total_done;
                $dataset_total_not_done[] = $result->total_not_done;
                $dataset_total_not_applicable[] = $result->total_not_applicable;
                $dataset_total_completed_by_staff_on_time[] = $result->total_completed_by_staff_on_time;
                $dataset_total_completed_by_staff_not_on_time[] = $result->total_completed_by_staff_not_on_time;
                $dataset_total_completed_by_patient_itself[] = $result->total_completed_by_patient_itself;
                $dataset_total_patient_did_not_want[] = $result->total_patient_drecord_not_want;
                $dataset_total_not_done_by_employee[] = $result->total_not_done_by_employee;
            }
        }

        $returnObj = [
            'labels' => $datalabels,
            'dataset_total_activity' => $dataset_total_activity,
            'dataset_total_compulsory' => $dataset_total_compulsory,
            'dataset_total_risk' => $dataset_total_risk,
            'dataset_total_risk' => $dataset_total_risk,
            'dataset_total_pending' => $dataset_total_pending,
            'dataset_total_done' => $dataset_total_done,
            'dataset_total_not_done' => $dataset_total_not_done,
            'dataset_total_not_applicable' => $dataset_total_not_applicable,
            'dataset_total_completed_by_staff_on_time' => $dataset_total_completed_by_staff_on_time,
            'dataset_total_completed_by_staff_not_on_time' => $dataset_total_completed_by_staff_not_on_time,
            'dataset_total_completed_by_patient_itself' => $dataset_total_completed_by_patient_itself,
            'dataset_total_patient_did_not_want' => $dataset_total_patient_did_not_want,
            'dataset_total_not_done_by_employee' => $dataset_total_not_done_by_employee,
        ];
        return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats'),$returnObj,config('httpcodes.success'));
    }

    public function getIPGoalSubgoalReport(Request $request)
    {
        $request_for = !empty($request->request_for) ? $request->request_for : 7;
        $datalabels = [];
        $dataset_total_goal = [];
        $dataset_total_sub_goal = [];
        if(!empty($request->start_date) && !empty($request->end_date)) 
        {
            $diffrece = dateDifference($request->start_date, $request->end_date) + 1;
            for($i = $diffrece; $i>=1; $i--)
            {
                $date = date("Y-m-d", strtotime('-'.($i-1).' days', strtotime($request->end_date)));
                $datalabels[] = $date;

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
                
                $query = PatientImplementationPlan::select([
                    \DB::raw('COUNT(IF(goal IS NOT NULL, 0, NULL)) as total_goal'),
                    \DB::raw('COUNT(IF(sub_goal IS NOT NULL, 0, NULL)) as total_sub_goal'),
                ]);

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $query->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->orWhere('user_id', $user->parent_id);
                    });
                }
                
                if($user->user_type_id !='2') 
                {
                    if($user->user_type_id =='3') 
                    {
                        if($user->hasPermissionTo('visible-all-patients-activity'))
                        {
                            $user_records = getAllowUserList('visible-all-patients-activity');
                            $query->whereIn('activities.patient_id', $user_records);
                        }
                        else
                        {
                            $query =  $query->whereIn('branch_id',$allChilds);
                        }
                    }
                    else
                    {
                        $query =  $query->whereIn('branch_id',$allChilds);
                    }
                }

                if(!empty($request->patient_id))
                {
                    $query->where('user_id', $request->patient_id);
                }

                $query->whereDate('start_date', $date);         
                $result = $query->first();
                $dataset_total_goal[] = $result->total_goal;
                $dataset_total_sub_goal[] = $result->total_sub_goal;
            }
        }
        else
        {
            for($i = $request_for; $i>=1; $i--)
            {
                $date = date('Y-m-d',strtotime('-'.($i-1).' days'));
                $datalabels[] = $date;

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
                
                
                $query = PatientImplementationPlan::select([
                    \DB::raw('COUNT(IF(goal IS NOT NULL, 0, NULL)) as total_goal'),
                    \DB::raw('COUNT(IF(sub_goal IS NOT NULL, 0, NULL)) as total_sub_goal'),
                ]);

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $query->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->orWhere('user_id', $user->parent_id);
                    });
                }
                
                if($user->user_type_id !='2') 
                {
                    if($user->user_type_id =='3') 
                    {
                        if($user->hasPermissionTo('visible-all-patients-activity'))
                        {
                            $user_records = getAllowUserList('visible-all-patients-activity');
                            $query->whereIn('activities.patient_id', $user_records);
                        }
                        else
                        {
                            $query =  $query->whereIn('branch_id',$allChilds);
                        }
                    }
                    else
                    {
                        $query =  $query->whereIn('branch_id',$allChilds);
                    }
                }

                if(!empty($request->patient_id))
                {
                    $query->where('user_id', $request->patient_id);
                }
                $query->whereDate('start_date', $date);         
                $result = $query->first();
                $dataset_total_goal[] = $result->total_goal;
                $dataset_total_sub_goal[] = $result->total_sub_goal;
            }
        }

        $returnObj = [
            'labels' => $datalabels,
            'dataset_total_goal' => $dataset_total_goal,
            'dataset_total_sub_goal' => $dataset_total_sub_goal,
        ];
        return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats'),$returnObj,config('httpcodes.success'));
    }
}
