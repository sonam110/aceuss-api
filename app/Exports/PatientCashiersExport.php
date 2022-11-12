<?php

namespace App\Exports;

use App\Models\PatientCashier;
use App\Models\User;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class PatientCashiersExport implements FromCollection, WithHeadings
{
	use Exportable;

    public $patient_id;

    public function __construct($patient_id)
    {
        $this->patient_id = $patient_id;
    }
    public function headings(): array {
        return  [
            'Top Most Parent Name',
            'Branch Name',
            'Patient Name',
            'Date',
            'Amount',
            'Receipt No.',
            'type',
            'file',
            'comment'
        ];
    }

    public function collection()
    {

    	$patientCashiers  = PatientCashier::where('patient_id',$this->patient_id)->get();
    	return $patientCashiers->map(function ($data, $key) {
            return [
                'Top Most Parent Id' => aceussDecrypt(User::find($data->top_most_parent_id)->name),
                'Branch Id' => User::find($data->branch_id)->branch_name,
                'Patient Id' => aceussDecrypt(User::find($data->patient_id)->name),
                'Date' => $data->date,
	            'Amount' =>$data->amount,
	            'Receipt No.' =>$data->receipt_no,
	            'type' =>($data->type==1) ? 'IN' : 'OUT',
	            'file' =>$data->file,
	            'comment' =>$data->comment
            ];
    	});
    }
}
