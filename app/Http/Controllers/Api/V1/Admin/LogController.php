<?php
namespace App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileBankIdLoginLog;
use App\Models\SmsLog;
use App\Models\MailLog;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    
    public function smsLog(Request $request)
    {
        try {
            $query = SmsLog::select('*')->with('company:id,name')->orderBy('created_at', 'DESC');
            if(!empty($request->top_most_parent_id))
            
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

            if(!empty($request->per_page_record))
            {
                $perPage = $request->per_page_record;
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

            return prepareResult(true,"Sms Log",$query,$this->success);
        } catch (\Throwable $e) {
            \Log::error($e);
           return prepareResult(false, $exception->getMessage(),[], $this->internal_server_error);
        }
    }

    
}
