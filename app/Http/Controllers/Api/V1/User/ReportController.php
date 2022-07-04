<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\ScheduleTemplate;
use Validator;
use Auth;
use DB;
use App\Models\User;
use App\Models\CompanyWorkShift;
use Exception;
use App\Models\OVHour;
use PDF;
use App\Models\EmailTemplate;
use Str;

class ReportController extends Controller
{
	public function __construct()
	{
		// $this->middleware('permission:schedule-browse',['except' => ['show']]);
		// $this->middleware('permission:schedule-add', ['only' => ['store']]);
		// $this->middleware('permission:schedule-edit', ['only' => ['update']]);
		// $this->middleware('permission:schedule-read', ['only' => ['show']]);
		// $this->middleware('permission:schedule-delete', ['only' => ['destroy']]);
	}
	

	public function scheduleReport(Request $request)
	{
		try 
		{
			$query = Schedule::orderBy('created_at', 'DESC')->where('is_active',1)->groupBy('user_id');

			if(!empty($request->user_id))
            {
                $query->whereIn('user_id', $request->user_id);
            }
            $query = $query->get();
            $data = [];

			foreach ($query as $key => $value) {
				$schduled = Schedule::where('user_id',$value->user_id)->->where('is_active',1)->sum('scheduled_work_duration');
				$extra = Schedule::where('user_id',$value->user_id)->->where('is_active',1)->sum('extra_work_duration')
				$obe = Schedule::where('user_id',$value->user_id)->->where('is_active',1)->sum('ob_work_duration');
				$emergency = 0;
				$data['labels'][] = $value->user->name;
				$data['total_hours'][] = $schduled + $extra + $obe;
				$data['regular_hours'][] = $schduled;
				$data['extra_hours'][] = $extra;
				$data['obe_hours'][] = $obe;
				$data['emergency_hours'][] = $emergency;
			}

			return prepareResult(true,"Schedule Report",$data,config('httpcodes.success'));
		}
		catch(Exception $exception) {
			return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));

		}

	}
}
