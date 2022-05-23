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
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // if(Language::where('title',$this->data['language_title'])->count() > 0)
        // {
        //     $language = Language::where('title',$this->data['language_title'])->first();
        // }
        // else
        // {
        //     $language = new Language;
        //     $language->title                = $this->data['language_title'];
        //     $language->value                = $this->data['language_value'];
        //     $language->status               = 1;
        //     $language->save();
        // }

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
        else
        {
            $group_id = null;
        }

        $label = new Label;
            $label->group_id               = $group_id;
            $label->language_id            = $this->data['language_id'];
            $label->label_name             = trim($row['label_name']);
            $label->label_value            = trim($row['label_value_in_entered_language']);
            $label->status                 = 1;
            $label->save();

        
        
        // if(!empty($row['label_value_in_entered_language']))
        // {
        //     $label = new Label;
        //     $label->group_id               = $group_id;
        //     $label->language_id            = $this->data['language_id'];
        //     $label->label_name             = trim($row['label_name']);
        //     $label->label_value            = trim($row['label_value_in_entered_language']);
        //     $label->status                 = 1;
        //     $label->save();
        // }
        return;
    }
}
