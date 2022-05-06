<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Deviation;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use Exception;

class StatisticsDeviationController extends Controller
{
    public function statisticsDeviation(Request $request)
    {
        try {
            $user = getUser();
            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = Deviation::select([
                \DB::raw('COUNT(id) as total_deviation'),
                \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
                \DB::raw('COUNT(IF(is_completed = 1, 0, NULL)) as total_completed'),
            ]);

            if($user->user_type_id=='2' || $user->user_type_id=='3' || $user->user_type_id=='4' || $user->user_type_id=='5' || $user->user_type_id=='11')
            {

            }
            else
            {
                $query = $query->where('is_secret', '!=', 1);
            }
            
            if($user->user_type_id !='2') {
                $query =  $query->whereIn('id',$allChilds);
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
            return prepareResult(true,"Deviations",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    public function getTWMwiseReport(Request $request)
    {
        $request_for = !empty($request->request_for) ? $request->request_for : 7;
        for($i = $request_for; $i>=1; $i--)
        {
            $date = date('Y-m-d',strtotime('-'.($i-1).' days'));
            $user = getUser();
            $branch_id = (!empty($user->branch_id)) ?$user->branch_id : $user->id;
            $branchids = branchChilds($branch_id);
            $allChilds = array_merge($branchids,[$branch_id]);
            $query = Deviation::select([
                \DB::raw('COUNT(id) as total_deviation'),
                \DB::raw('COUNT(IF(is_signed = 1, 0, NULL)) as total_signed'),
                \DB::raw('COUNT(IF(activity_id IS NULL, 0, NULL)) as total_without_activity'),
                \DB::raw('COUNT(IF(activity_id IS NOT NULL, 0, NULL)) as total_with_activity'),
                \DB::raw('COUNT(IF(is_completed = 1, 0, NULL)) as total_completed'),
            ]);

            if($user->user_type_id=='2' || $user->user_type_id=='3' || $user->user_type_id=='4' || $user->user_type_id=='5' || $user->user_type_id=='11')
            {

            }
            else
            {
                $query = $query->where('is_secret', '!=', 1);
            }
            
            if($user->user_type_id !='2') {
                $query =  $query->whereIn('id',$allChilds);
            }

            if(!empty($request->patient_id))
            {
                $query->where('patient_id', $request->patient_id);
            }
            $query->whereDate('date_time', $date);         
            $result[$date] = $query->first();
        }
        return prepareResult(true,"Deviations",$result,config('httpcodes.success'));
    }
}
