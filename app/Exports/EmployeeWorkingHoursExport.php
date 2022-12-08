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

    	$schedules  = Schedule::whereBetween('shift_date',$this->dates)
            ->where('user_id', $this->user_id)
            ->where('is_active',1)
            ->orderBy('shift_date', 'ASC')
            ->get();
    	return $schedules->map(function ($data, $key) {

            $scheduleData = Schedule::select(\DB::raw("SUM(scheduled_work_duration) as scheduled_work_duration"),
                \DB::raw("SUM(extra_work_duration) as extra_work_duration"),
                \DB::raw("SUM(emergency_work_duration) as emergency_work_duration"),
                \DB::raw("SUM(ob_work_duration) as ob_work_duration"),
                \DB::raw("SUM(vacation_duration) as vacation_duration")
                )
                ->where('shift_date',$data->shift_date)
                ->where('user_id',$data->user_id)
                ->where('is_active',1)
                ->where('leave_applied',0)
                ->first();
    		$total_hours = $scheduleData->scheduled_work_duration + $scheduleData->extra_work_duration + $scheduleData->emergency_work_duration + $scheduleData->ob_work_duration + $scheduleData->vacation_duration;
            return [
            	'Date' => $data->shift_date,
                'Employee Name' => aceussDecrypt($data->user->name),
	            'scheduled work duration' => minToHours($scheduleData->scheduled_work_duration),
	            'extra work duration' => minToHours($scheduleData->extra_work_duration),
	            'ob work duration' => minToHours($scheduleData->ob_work_duration),
	            'emergency work duration' => minToHours($scheduleData->emergency_work_duration),
	            'vacation duration' => minToHours($scheduleData->vacation_duration),
	            'Total Hour' => minToHours($total_hours)
            ];
    	});
    }
}
