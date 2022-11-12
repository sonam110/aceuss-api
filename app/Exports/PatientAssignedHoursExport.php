<?php

namespace App\Exports;

use App\Models\Schedule;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class PatientAssignedHoursExport implements FromCollection, WithHeadings
{
	use Exportable;

    public $dates;
    public $patient_id;

    public function __construct($dates,$patient_id)
    {
        $this->dates = $dates;
        $this->patient_id = $patient_id;
    }
    public function headings(): array {
    	return  [
    		'Date',
            'Patient Name',
            'Total Hour'
        ];
    }

    public function collection()
    {

    	$schedules  = Schedule::whereBetween('shift_date',$this->dates)->where('patient_id',$this->patient_id)->get();
    	return $schedules->map(function ($data, $key) {
    		$scheduled_work_duration = Schedule::where('shift_date',$data->shift_date)->where('patient_id',$data->patient_id)->sum('scheduled_work_duration');
    		$extra_work_duration = Schedule::where('shift_date',$data->shift_date)->where('patient_id',$data->patient_id)->sum('extra_work_duration');
    		$emergency_work_duration = Schedule::where('shift_date',$data->shift_date)->where('patient_id',$data->patient_id)->sum('emergency_work_duration');
    		$ob_work_duration = Schedule::where('shift_date',$data->shift_date)->where('patient_id',$data->patient_id)->sum('ob_work_duration');
    		$vacation_duration = Schedule::where('shift_date',$data->shift_date)->where('patient_id',$data->patient_id)->sum('vacation_duration');
    		$total_hours = $scheduled_work_duration + $extra_work_duration + $emergency_work_duration + $ob_work_duration + $vacation_duration;
            return [
            	'Date' => $data->shift_date,
                'Patient Name' => aceussDecrypt($data->patient->name),
	            'Total Hour' => $total_hours
            ];
    	});
    }
}
