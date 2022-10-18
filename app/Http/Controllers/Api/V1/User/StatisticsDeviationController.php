<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Deviation;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Auth;
use DB;
use Exception;

class StatisticsDeviationController extends Controller
{
    public function statisticsDeviation(Request $request)
    {
        try {
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
            
            $query = Deviation::select([
                \DB::raw('COUNT(id) as total_deviation'),
                \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
                \DB::raw('COUNT(IF(is_completed = 1, 0, NULL)) as total_completed'),
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
            
            if($user->user_type_id !='2') 
            {
                if($user->user_type_id =='3') 
                {
                    $user_records = getAllowUserList('visible-all-patients-deviation');
                    $deviationCounts->whereIn('deviations.patient_id', $user_records);
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

            if(!empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereDate('date_time', '>=', $request->from_date)->whereDate('date_time', '<=', $request->end_date);
            }
            elseif(!empty($request->from_date) && empty($request->end_date))
            {
                $query->whereDate('date_time', $request->from_date);
            }
            elseif(empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereDate('date_time', '<=', $request->end_date);
            }            
            $query = $query->first();
            return prepareResult(true,getLangByLabelGroups('CompanyType','message_stats'),$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function getTWMwiseReport(Request $request)
    {
        $request_for = !empty($request->request_for) ? $request->request_for : 7;
        $datalabels = [];
        $dataset_total_deviation = [];
        $dataset_total_signed = [];
        $dataset_without_activity = [];
        $dataset_with_activity = [];
        $dataset_total_completed = [];
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
                
                $query = Deviation::select([
                    \DB::raw('COUNT(id) as total_deviation'),
                    \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                    \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                    \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
                    \DB::raw('COUNT(IF(is_completed = 1, 0, NULL)) as total_completed'),
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

                if($user->user_type_id !='2') 
                {
                    if($user->user_type_id =='3') 
                    {
                        $user_records = getAllowUserList('visible-all-patients-deviation');
                        $deviationCounts->whereIn('deviations.patient_id', $user_records);
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
                $query->whereDate('date_time', $date);         
                $result = $query->first();
                $dataset_total_deviation[] = $result->total_deviation;
                $dataset_total_signed[] = $result->total_signed;
                $dataset_without_activity[] = $result->total_without_activity;
                $dataset_with_activity[] = $result->total_with_activity;
                $dataset_total_completed[] = $result->total_completed;

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
                
                
                $query = Deviation::select([
                    \DB::raw('COUNT(id) as total_deviation'),
                    \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                    \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                    \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
                    \DB::raw('COUNT(IF(is_completed = 1, 0, NULL)) as total_completed'),
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
                
                if($user->user_type_id !='2') 
                {
                    if($user->user_type_id =='3') 
                    {
                        $user_records = getAllowUserList('visible-all-patients-deviation');
                        $deviationCounts->whereIn('deviations.patient_id', $user_records);
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
                $query->whereDate('date_time', $date);         
                $result = $query->first();
                $dataset_total_deviation[] = $result->total_deviation;
                $dataset_total_signed[] = $result->total_signed;
                $dataset_without_activity[] = $result->total_without_activity;
                $dataset_with_activity[] = $result->total_with_activity;
                $dataset_total_completed[] = $result->total_completed;
            }
        }

        $returnObj = [
            'labels' => $datalabels,
            'dataset_total_deviation' => $dataset_total_deviation,
            'dataset_total_signed' => $dataset_total_signed,
            'dataset_without_activity' => $dataset_without_activity,
            'dataset_with_activity' => $dataset_with_activity,
            'dataset_total_completed' => $dataset_total_completed
        ];
        return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats'),$returnObj,config('httpcodes.success'));
    }

    public function getMonthWiseReport(Request $request)
    {
        $period = now()->startOfMonth()->subMonths(6)->monthsUntil(now());
        $data = [];
        $datalabels = [];
        foreach ($period as $date)
        {
            $datalabels[] = $date->shortMonthName.' '.Carbon::parse('01-01-'.$date->year)->format('y');
            $month = $date->month;
            $year = $date->year;
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


            $query = Deviation::select([
                \DB::raw('COUNT(id) as total_deviation'),
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

            if($user->user_type_id !='2') 
            {
                if($user->user_type_id =='3') 
                {
                    $user_records = getAllowUserList('visible-all-patients-deviation');
                    $deviationCounts->whereIn('deviations.patient_id', $user_records);
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
            $query->whereRaw('MONTH(date_time) = '.$month);         
            $query->whereRaw('YEAR(date_time) = '.$year);         
            $result = $query->first();
            $data[] = $result->total_deviation;
        }

        $returnObj = [
            'datalabels' => $datalabels,
            'data' => $data
        ];
        return prepareResult(true,getLangByLabelGroups('CompanyType','message_stats'),$returnObj,config('httpcodes.success'));
    }
}
