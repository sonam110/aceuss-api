<?php

namespace App\Exports;

use App\Models\Label;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class ActivityLogExport implements FromCollection, WithHeadings
{
	use Exportable;
    public $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function headings(): array {
        return  [
            'Id',
            'Log Name',
            'Description',
            'Subject Type',
            'Subject Id',
            'Causer type',
            'Causer Id',
            'Properties',
            'Created At',
            'Updated At'
        ];
    }

    public function collection()
    {
    	return $this->query->map(function ($data, $key) {
            return $data;
    	});
    }
}
