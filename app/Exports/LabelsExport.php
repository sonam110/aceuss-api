<?php

namespace App\Exports;

use App\Models\Label;
use App\Models\Language;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Auth;

class LabelsExport implements FromCollection, WithHeadings
{
	use Exportable;


    public function headings(): array {
        $languages = Language::all();

        $file_head = ['SNO', 'group_name', 'label_name'];

        foreach ($languages as $key => $value) 
        {
            $file_head[] = 'label_value_in_'.$value->title;
                
        }
        $file_head[] = 'label_value_in_entered_language';
    	return  $file_head;
    }

    public function collection()
    {
        $language_id = Language::first()->id;
    	$labels = Label::where('language_id',$language_id)->orderBy('group_id', 'ASC')->get();
    	return $labels->map(function ($data, $key) {
            $file_value = [
                'SNO' => $key+1,
                'group_name' => ($data->group) ? $data->group->name : null,
                'label_name' => $data->label_name
            ];
            $languages = Language::all();
            foreach ($languages as $key => $value) {
                $label_value = Label::where('language_id',$value->id)->where('label_name',$data->label_name)->first();
                $file_value[] = $label_value ? $label_value->label_value : '';
            }
            $file_value[] = '';
            return $file_value;
    	});
    }
}
