<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\OVHour;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Imports\ObeHoursImport;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use DB;

class OVHourController extends Controller
{
    // public function __construct()
    // {

    //     $this->middleware('permission:OVHour-browse',['except' => ['show']]);
    //     $this->middleware('permission:OVHour-add', ['only' => ['store']]);
    //     $this->middleware('permission:OVHour-edit', ['only' => ['update']]);
    //     $this->middleware('permission:OVHour-read', ['only' => ['show']]);
    //     $this->middleware('permission:OVHour-delete', ['only' => ['destroy']]);
        
    // }

    public function OVHours(Request $request)
    {
        try {

            $query = OVHour::orderBy('id', 'DESC');
            
            if(!empty($request->group_by))
            {
                $query->groupBy('group_token');
            }

            if(!empty($request->title))
            {
                $query->where('title', 'LIKE', '%'.$request->title.'%');
            }

            if(!empty($request->group_token))
            {
                $query->where('group_token', $request->group_token);
            }

            if(!empty($request->start_time))
            {
                $query->where('start_time',">=" ,$request->start_time);
            }
            if(!empty($request->end_time))
            {
                $query->where('end_time',"<=" ,$request->end_time);
            }

            if((empty($request->date)) && (empty($request->end_date)))
            {
                $query->whereDate('date', '>=', date('Y-m-d'));
            }

            if(!empty($request->date))
            {
                $query->whereDate('date', '>=',$request->date);
            }

            if(!empty($request->end_date))
            {
                $query->whereDate('date', '<=',$request->end_date);
            }

            if(!empty($request->ob_type))
            {
                $query->where('ob_type', $request->ob_type);
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
            return prepareResult(true,getLangByLabelGroups('OVHour','message_list') ,$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
	        logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
            
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        if(empty($request->dates))
        {
            $validation = \Validator::make($request->all(), [
                'title'      => 'required',
                'start_time'      => 'required',
                'end_time'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        }
        elseif(empty($request->start_time))
        {
            $validation = \Validator::make($request->all(), [
                'title'      => 'required',
                'dates'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        }
        DB::beginTransaction();
        try {
            if($request->is_range == 1)
            {
                $start_date = $request->dates[0];
                $end_date = $request->dates[1];
                $is_repeat = 1;
                $everyWeek = $request->every_week;
                $week_days = $request->week_days;
                   
                $from = \Carbon\Carbon::parse($start_date);
                $to =   (!empty($end_date)) ? \Carbon\Carbon::parse($end_date) : \Carbon\Carbon::parse($start_date);
                $start_from = $from->format('Y-m-d');
                $end_to = $to->format('Y-m-d');

                $dates = [];
                $ovhour_ids = [];

                if($request->is_repeat == 1)
                {
                    for($w = $from; $w->lte($to); $w->addWeeks($everyWeek)) {
                        $date = \Carbon\Carbon::parse($w);
                        $startWeek = $w->startOfWeek()->format('Y-m-d');
                        $weekNumber = $date->weekNumberInMonth;
                        $start = \Carbon\Carbon::createFromFormat("Y-m-d", $startWeek);
                        $end = $start->copy()->endOfWeek()->addDays()->format('Y-m-d');
                        for($p = $start; $p->lte($end); $p->addDays()) {
                            if(strtotime($start_from) <= strtotime($p) && strtotime($end_to) >= strtotime($p) ) {
                                if(in_array($p->dayOfWeek, $week_days)){
                                    array_push($dates, $p->copy()->format('Y-m-d'));
                                }
                            }
                        }
                    }
                }
                else
                {
                    $date1 = strtotime($start_date);
                    $date2 = strtotime($end_date);
                    for ($currentDate=$date1; $currentDate<=$date2; $currentDate += (86400)) 
                    {                                   
                        $dates[] = date('Y-m-d', $currentDate);
                    }
                }         
            }
            else
            {
                $dates = $request->dates;
            }
            $group_token = generateRandomNumber(10);
            foreach ($dates as $key => $date) 
            {
                $ovHour = new OVHour;
                $ovHour->group_token = ($request->is_repeat == 1) ? $group_token : generateRandomNumber(8);
                $ovHour->title = $request->title;
                $ovHour->date = $date;
                if($request->is_range == 1)
                {
                    $ovHour->end_date = $end_to;
                }
                $ovHour->ob_type = $request->ob_type;
                $ovHour->start_time = $request->start_time;
                $ovHour->end_time = $request->end_time;
                $ovHour->entry_mode = $request->entry_mode;
                $ovHour->save();
                $ovhour_ids[] = $ovHour->id;
            } 

            $schedules = Schedule::where('shift_start_time','>=',date('Y-m-d H:i'))->get();
            foreach ($schedules as $key => $schedule) {
               $result =  scheduleWorkCalculation($schedule->shift_date,$schedule->shift_start_time,$schedule->shift_end_time,$schedule->schedule_type,$schedule->shift_type,$schedule->rest_start_time,$schedule->rest_end_time,$schedule->user_id,$schedule->assigneWork_id);

               $schedule->scheduled_work_duration = $result['scheduled_work_duration'];
               $schedule->extra_work_duration = $result['extra_work_duration'];
               $schedule->emergency_work_duration = $result['emergency_work_duration'];
               $schedule->ob_work_duration = $result['ob_work_duration'];
               $schedule->ob_type = $result['ob_type'];
               $schedule->ob_start_time = $result['ob_start_time'];
               $schedule->ob_end_time = $result['ob_end_time'];
               $schedule->ob_red_work_duration = $result['ob_red_work_duration'];
               $schedule->ob_red_start_time = $result['ob_red_start_time'];
               $schedule->ob_red_end_time = $result['ob_red_end_time'];
               $schedule->ob_weekend_work_duration = $result['ob_weekend_work_duration'];
               $schedule->ob_weekend_start_time = $result['ob_weekend_start_time'];
               $schedule->ob_weekend_end_time = $result['ob_weekend_end_time'];
               $schedule->ob_weekday_work_duration = $result['ob_weekday_work_duration'];
               $schedule->ob_weekday_start_time = $result['ob_weekday_start_time'];
               $schedule->ob_weekday_end_time = $result['ob_weekday_end_time'];
               $schedule->save();
            }

            $data = OVHour::whereIn('id',$ovhour_ids)->groupBy('group_token')->get();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('OVHour','message_create') ,$data, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OVHour  $ovHour
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try 
        {
            $checkId= OVHour::find($id);
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('OVHour','message_record_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,getLangByLabelGroups('OVHour','message_show')  ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OVHour  $ovHour
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request,$id)
    {
        if(empty($request->date))
        {
            $validation = \Validator::make($request->all(), [
                'title'      => 'required',
                'start_time'      => 'required',
                'end_time'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
            }
        }
        elseif(empty($request->start_time))
        {
            $validation = \Validator::make($request->all(), [
                'title'      => 'required',
                'date'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
           }
       }
          

        DB::beginTransaction();
        try {
            $ovHour = OVHour::find($id);
            if(!empty($ovHour->end_date))
            {
                $update = OVHour::where('group_token', $ovHour->group_token)
                ->whereDate('date','>', date('Y-m-d'))
                ->update([
                    'title' => $request->title,
                    'ob_type' => $request->ob_type,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'title' => $request->title,
                ]);
                $ovHour = OVHour::find($id);
            }
            else
            {
                $ovHour->title = $request->title;
                $ovHour->date = $request->date;
                $ovHour->ob_type = $request->ob_type;
                $ovHour->start_time = $request->start_time;
                $ovHour->end_time = $request->end_time;
                $ovHour->entry_mode = $request->entry_mode;
                $ovHour->save();
            }
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('OVHour','message_update') ,$ovHour, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            DB::rollback();
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\OVHour $ovHour
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */

    public function destroy($id)
    {
        try 
        {
            $checkId= OVHour::find($id);
            if (!is_object($checkId)) {
                return prepareResult(false,getLangByLabelGroups('OVHour','message_record_not_found'), [],config('httpcodes.not_found'));
            }
            OVHour::where('id',$id)->delete();
            return prepareResult(true,getLangByLabelGroups('OVHour','message_delete') ,[], config('httpcodes.success'));
        } catch (\Throwable $exception) {
            logException($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }

    public function obeHoursImport(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'file'      => 'required',
        ]);

        if ($validation->fails()) {
           return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
        }

        $import = Excel::import(new ObeHoursImport(),request()->file('file'));

        return prepareResult(true,getLangByLabelGroups('BcCommon','message_import') ,[], config('httpcodes.success'));
    }
}
