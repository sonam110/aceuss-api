<?php

namespace App\Exports;

use App\Models\Label;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class BankIdLogExport implements FromCollection, WithHeadings
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
            'Top Most Parent Id', 
            'Session Id', 
            'Personnel Number', 
            'Name',
            'Created At',
            'Updated At',
            'Company'
        ];
    }

    public function collection()
    {
    	return $this->query->map(function ($data, $key) {
            return $data;
    	});
    }
}
