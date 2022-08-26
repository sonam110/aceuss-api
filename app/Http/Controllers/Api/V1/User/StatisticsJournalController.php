<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Exception;

class StatisticsJournalController extends Controller
{
    public function statisticsJournal(Request $request)
    {
        try {
            $user = getUser();
            if(!empty($user->branch_id)) {
                $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
            } else {
                $allChilds = userChildBranches(\App\Models\User::find($user->id));
            }
            
            $query = Journal::select([
                \DB::raw('COUNT(id) as total_journal'),
                \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
            ]);

            if(in_array($user->user_type_id, [2,3,4,5,11]))
            {

            }
            else
            {
                $query->where('is_secret', '!=', 1);
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
            return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats'),$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function getTWMwiseJournalReport(Request $request)
    {
        $request_for = !empty($request->request_for) ? $request->request_for : 7;
        $datalabels = [];
        $dataset_total_journal = [];
        $dataset_total_signed = [];
        $dataset_without_activity = [];
        $dataset_with_activity = [];
        if(!empty($request->start_date) && !empty($request->end_date)) 
        {
            $diffrece = dateDifference($request->start_date, $request->end_date) + 1;
            for($i = $diffrece; $i>=1; $i--)
            {
                $date = date("Y-m-d", strtotime('-'.($i-1).' days', strtotime($request->end_date)));
                $datalabels[] = $date;

                $user = getUser();
                if(!empty($user->branch_id)) {
                    $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                } else {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                }
                
                $query = Journal::select([
                    \DB::raw('COUNT(id) as total_journal'),
                    \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                    \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                    \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
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
                $query->whereDate('date', $date);         
                $result = $query->first();
                
                $dataset_total_journal[] = $result->total_journal;
                $dataset_total_signed[] = $result->total_signed;
                $dataset_without_activity[] = $result->total_without_activity;
                $dataset_with_activity[] = $result->total_with_activity;
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
                    $allChilds = userChildBranches(\App\Models\User::find($user->branch_id));
                } else {
                    $allChilds = userChildBranches(\App\Models\User::find($user->id));
                }
                
                $query = Journal::select([
                    \DB::raw('COUNT(id) as total_journal'),
                    \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                    \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                    \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
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
                $query->whereDate('date', $date);         
                $result = $query->first();
                
                $dataset_total_journal[] = $result->total_journal;
                $dataset_total_signed[] = $result->total_signed;
                $dataset_without_activity[] = $result->total_without_activity;
                $dataset_with_activity[] = $result->total_with_activity;
            }
        }


        $returnObj = [
            'labels' => $datalabels,
            'dataset_total_journal' => $dataset_total_journal,
            'dataset_total_signed' => $dataset_total_signed,
            'dataset_without_activity' => $dataset_without_activity,
            'dataset_with_activity' => $dataset_with_activity
        ];
        return prepareResult(true,getLangByLabelGroups('BcCommon','message_stats'),$returnObj,config('httpcodes.success'));
    }
}
