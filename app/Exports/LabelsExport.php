<?php

namespace App\Exports;

use App\Models\Label;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class LabelsExport implements FromCollection, WithHeadings
{
	use Exportable;


    public function headings(): array {
    	return [
    		'SNO',
            'group_name',
            'label_name',
            'label_value_in_english',
            'label_value_in_entered_language',
    		// 'Created At'
    	];
    }

    public function collection()
    {
    	$labels = Label::where('language_id',1)->orderBy('group_id', 'ASC')->get();

    	// return $labels;

    	return $labels->map(function ($data, $key) {
            return [
                'SNO' => $key+1,
                'group_name' => ($data->group) ? $data->group->name : null,
                'label_name' => $data->label_name,
                'label_value_in_english' => trim($data->label_value),
                'label_value_in_entered_language' => '',
                // 'Created At'                    => $data->created_at,
            ];
    	});
    }
}
