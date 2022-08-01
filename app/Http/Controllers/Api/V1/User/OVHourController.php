<?php

namespace App\Http\Controllers\API\V1\User;

use App\Http\Controllers\Controller;
use App\Models\OVHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            if(!empty($request->date))
            {
                $query->where('date',$request->date);
            }

            if(!empty($request->title))
            {
                $query->where('title','like','%'.$request->title.'%');
            }

            if(!empty($request->start_time))
            {
                $query->where('start_time',">=" ,$request->start_time);
            }
            if(!empty($request->end_time))
            {
                $query->where('end_time',"<=" ,$request->end_time);
            }

            if(!empty($request->date))
            {
                $query->where('date',$request->date);
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
                return prepareResult(true,"OVHour list",$pagination,config('httpcodes.success'));
            }
            else
            {
                $query = $query->get();
            }
            return prepareResult(true,"OVHour list",$query,config('httpcodes.success'));
        }
        catch(Exception $exception) {
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
                'date'      => 'required',
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
                        $end = $start->copy()->endOfWeek()->format('Y-m-d');
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

            foreach ($dates as $key => $date) 
            {
                $ovHour = new OVHour;
                $ovHour->title = $request->title;
                $ovHour->date = $date;
                $ovHour->ob_type = $request->ob_type;
                $ovHour->start_time = $request->start_time;
                $ovHour->end_time = $request->end_time;
                $ovHour->entry_mode = $request->entry_mode;
                $ovHour->save();
                $ovhour_ids[] = $ovHour->id;
            } 

            $data = OVHour::whereIn('id',$ovhour_ids)->get();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('OVHour','message_create') ,$data, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
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
                return prepareResult(false,getLangByLabelGroups('OVHour','message_id_not_found'), [],config('httpcodes.not_found'));
            }
             return prepareResult(true,'View OVHour' ,$checkId, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
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
                'date'      => 'required',
            ]);

            if ($validation->fails()) {
               return prepareResult(false,$validation->errors()->first(),[], config('httpcodes.bad_request')); 
           }
       }
          

        DB::beginTransaction();
        try {
            $ovHour = OVHour::find($id);
            $ovHour->title = $request->title;
            $ovHour->date = $request->date;
            $ovHour->ob_type = $request->ob_type;
            $ovHour->start_time = $request->start_time;
            $ovHour->end_time = $request->end_time;
            $ovHour->entry_mode = $request->entry_mode;
            $ovHour->save();
            DB::commit();
            return prepareResult(true,getLangByLabelGroups('OVHour','message_create') ,$ovHour, config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
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
                return prepareResult(false,getLangByLabelGroups('OVHour','message_id_not_found'), [],config('httpcodes.not_found'));
            }
            OVHour::where('id',$id)->delete();
            return prepareResult(true,getLangByLabelGroups('OVHour','message_delete') ,[], config('httpcodes.success'));
        } catch (\Throwable $exception) {
            \Log::error($exception);
            return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
        }
    }
}
