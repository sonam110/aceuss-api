<?php

namespace App\Exports;

use App\Models\Schedule;
use App\Models\Stamling;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class StamplingDatewiseReportExport implements FromCollection, WithHeadings
{
	use Exportable;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function headings(): array {
    	return  [
            'Date',
            'Total Schedule Work Done',
            'Total Extra Work Done',
            'Total Ob Work Done',
            'Stampling Hour',
        ];
    }

    public function collection()
    {
    	return collect($this->data);
    }
}
