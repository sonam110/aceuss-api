<?php

namespace App\Exports;

use App\Models\Schedule;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class EmployeeWorkingHoursExport implements FromCollection, WithHeadings
{
	use Exportable;

    public $dates;
    public $user_id;

    public function __construct($dates,$user_id)
    {
        $this->dates = $dates;
        $this->user_id = $user_id;
    }
    public function headings(): array {
    	return  [
    		'Date',
            'Employee Name',
            'scheduled work duration',
            'extra work duration',
            'ob work duration',
            'emergency work duration',
            'vacation duration',
            'Total Hour'
        ];
    }

    public function collection()
    {

    	$schedules  = Schedule::whereBetween('shift_date',$this->dates)->where('user_id',$this->user_id)->get();
    	return $schedules->map(function ($data, $key) {
    		$scheduled_work_duration = Schedule::where('shift_date',$data->shift_date)->where('user_id',$data->user_id)->sum('scheduled_work_duration');
    		$extra_work_duration = Schedule::where('shift_date',$data->shift_date)->where('user_id',$data->user_id)->sum('extra_work_duration');
    		$emergency_work_duration = Schedule::where('shift_date',$data->shift_date)->where('user_id',$data->user_id)->sum('emergency_work_duration');
    		$ob_work_duration = Schedule::where('shift_date',$data->shift_date)->where('user_id',$data->user_id)->sum('ob_work_duration');
    		$vacation_duration = Schedule::where('shift_date',$data->shift_date)->where('user_id',$data->user_id)->sum('vacation_duration');
    		$total_hours = $scheduled_work_duration + $extra_work_duration + $emergency_work_duration + $ob_work_duration + $vacation_duration;
            return [
            	'Date' => $data->shift_date,
                'Employee Name' =>$data->user->name,
	            'scheduled work duration' =>$scheduled_work_duration,
	            'extra work duration' =>$extra_work_duration,
	            'ob work duration' =>$ob_work_duration,
	            'emergency work duration' =>$emergency_work_duration,
	            'vacation duration' =>$vacation_duration,
	            'Total Hour' => $total_hours
            ];
    	});
    }
}
