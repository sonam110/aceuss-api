<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Activity;
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
                $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
                $branchids = branchChilds($branch_id);
                $allChilds = array_merge($branchids,[$branch_id]);
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
                    \DB::raw('COUNT(IF(selected_option = "patient-did-not-want", 0, NULL)) as total_patient_did_not_want'),
                    \DB::raw('COUNT(IF(selected_option = "not-done-by-employee", 0, NULL)) as total_not_done_by_employee'),
                ]);

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $query->where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    });
                }
                
                if($user->user_type_id !='2') {
                    $query =  $query->whereIn('branch_id',$allChilds);
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
                $dataset_total_patient_did_not_want[] = $result->total_patient_did_not_want;
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
                $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
                $branchids = branchChilds($branch_id);
                $allChilds = array_merge($branchids,[$branch_id]);
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
                    \DB::raw('COUNT(IF(selected_option = "patient-did-not-want", 0, NULL)) as total_patient_did_not_want'),
                    \DB::raw('COUNT(IF(selected_option = "not-done-by-employee", 0, NULL)) as total_not_done_by_employee'),
                ]);

                if(in_array($user->user_type_id, [2,3,4,5,11]))
                {

                }
                else
                {
                    $query = $query->where('is_secret', '!=', 1);
                }

                if(in_array($user->user_type_id, [6,7,8,9,10,12,13,14,15]))
                {
                    $query->where(function ($q) use ($user) {
                        $q->where('patient_id', $user->id)
                            ->orWhere('patient_id', $user->parent_id);
                    });
                }
                
                if($user->user_type_id !='2') {
                    $query =  $query->whereIn('branch_id',$allChilds);
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
                $dataset_total_patient_did_not_want[] = $result->total_patient_did_not_want;
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
        return prepareResult(true,"Activities",$returnObj,config('httpcodes.success'));
    }
}