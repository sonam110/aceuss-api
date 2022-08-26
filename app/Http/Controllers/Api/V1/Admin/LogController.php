<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileBankIdLoginLog;
use App\Models\SmsLog;
use Spatie\Activitylog\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SmsLogExport;
use App\Exports\BankIdLogExport;
use App\Exports\ActivityLogExport;
use DB;
use Log;

class LogController extends Controller
{
    public function smsLog(Request $request)
    {
        try {
            $query = SmsLog::select('*')->with('company:id,name')->orderBy('created_at', 'DESC');
            if(!empty($request->top_most_parent_id))
            {
                $query->where('top_most_parent_id', $request->top_most_parent_id);
            }

            if(!empty($request->mobile))
            {
                $query->where('mobile', 'LIKE', '%'.$request->mobile.'%');
            }

            if($request->status==0 && $request->status!="")
            {
                $query->where('status', 0);
            }
            if(!empty($request->status))
            {
                $query->where('status', $request->status);
            }

            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }

            if($request->export == true)
            {
            	$rand = rand(0,1000);
            	$excel = Excel::store(new SmsLogExport($query), 'export/smslog/'.$rand.'.xlsx' , 'export_path');

            	return prepareResult(true,getLangByLabelGroups('BcCommon','message_export') ,['url' => env('APP_URL').'public/export/smslog/'.$rand.'.xlsx'], config('httpcodes.success'));
            }
            else
            {
            	return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
            }
        } catch (\Throwable $e) {
            Log::error($e);
           return prepareResult(false, $e->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function mobileBankIdLog(Request $request)
    {
        try {
            $query = MobileBankIdLoginLog::select('*')->with('company:id,name')->orderBy('created_at', 'DESC');
            if(!empty($request->top_most_parent_id))
            {
                $query->where('top_most_parent_id', $request->top_most_parent_id);
            }

            if(!empty($request->personnel_number))
            {
                $query->where('personnel_number', $request->personnel_number);
            }

            if(!empty($request->name))
            {
                $query->where('name', 'LIKE', '%'.$request->name.'%');
            }

            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            if($request->export == true)
            {
            	$rand = rand(0,1000);
            	$excel = Excel::store(new BankIdLogExport($query), 'export/bankidlog/'.$rand.'.xlsx' , 'export_path');

            	return prepareResult(true,getLangByLabelGroups('BcCommon','message_export') ,['url' => env('APP_URL').'public/export/bankidlog/'.$rand.'.xlsx'], config('httpcodes.success'));
            }
            else
            {
            	return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
            }
        } catch (\Throwable $e) {
            Log::error($e);
           return prepareResult(false, $e->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function activitiesLog(Request $request)
    {
        try {
            $query = Activity::select('*')->orderBy('created_at', 'DESC');
            if(!empty($request->properties))
            {
                $query->where('properties', 'LIKE', '%'.$request->properties.'%');
            }

            if(!empty($request->perPage))
            {
                $perPage = $request->perPage;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }
            if($request->export == true)
            {
                $rand = rand(0,1000);
                $excel = Excel::store(new ActivityLogExport($query), 'export/activitylog/'.$rand.'.xlsx' , 'export_path');

                return prepareResult(true,getLangByLabelGroups('BcCommon','message_export') ,['url' => env('APP_URL').'public/export/activitylog/'.$rand.'.xlsx'], config('httpcodes.success'));
            }
            else
            {
                return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
            }
        } catch (\Throwable $e) {
            Log::error($e);
           return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function activityLogInfo($activity_id)
    {
        try {
            $query = Activity::find($activity_id);
            if($query)
            {
                //$query = $query->changes();
                return prepareResult(true,getLangByLabelGroups('BcCommon','message_list'),$query,config('httpcodes.success'));
            }
            return response(prepareResult(true, [], getLangByLabelGroups('BcCommon','message_record_not_found')), config('httpcodes.not_found'));
        } catch (\Throwable $e) {
            Log::error($e);
           return prepareResult(false, $e->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
    
}
