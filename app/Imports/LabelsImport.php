<?php

namespace App\Imports;

use App\Models\Label;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Hash;
use Auth;
use App\Models\Group;
use App\Models\Language;
use Str;

class LabelsImport implements ToModel,WithHeadingRow
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function model(array $row)
    {
        $group_id = null;
        if(!empty($row['group_name']))
        {
            if(Group::where('name',$row['group_name'])->count() > 0)
            {
                $group = Group::where('name',$row['group_name'])->first();
            }
            else
            {
                $group = new Group;
                $group->name                = trim($row['group_name']);
                $group->status              = 1;
                $group->save();
            }
            $group_id = $group->id;
        }

        $label = new Label;
        $label->group_id               = $group_id;
        $label->language_id            = $this->data['language_id'];
        $label->label_name             = trim($row['label_name']);
        $label->label_value            = trim($row['label_value_in_entered_language']);
        $label->status                 = 1;
        $label->save();
        return;
    }
}
