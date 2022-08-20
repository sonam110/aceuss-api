<?php

namespace App\Exports;

use App\Models\Schedule;
use App\Models\Stamling;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class StamplingReportExport implements FromCollection, WithHeadings
{
	use Exportable;
    public $stamplings;

    public function __construct($stamplings)
    {
        $this->stamplings = $stamplings;
    }
    public function headings(): array {
    	return  [
    		'Id',
            'Schedule Id',
            'Date',
            'In Time',
            'Out Time',
            'Stampling Type',
            'Total Ob Hours',
            'Shift Start Time',
            'Shift End Time',
            'Shift Hours',
            'Stampling Hours',
            'Extra Work',
        ];
    }

    public function collection()
    {
    	return $this->stamplings->map(function ($data, $key) {
            return $data;
    	});
    }
}
