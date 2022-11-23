<?php

namespace App\Imports;

use App\Models\Label;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Hash;
use Auth;
use App\Models\OVHour;
use Str;

class ObeHoursImport implements ToModel,WithHeadingRow
{

    public function model(array $row)
    {
        if (isset($row['title'])) 
        {
            $ovHour = new OVHour;
            $ovHour->title = $row['title'];
            $ovHour->date = (!empty($row['date']) ? date('Y-m-d', strtotime($row['date'])) : null);
            $ovHour->ob_type = $row['ob_type'];
            $ovHour->start_time = (!empty($row['start_time']) ? date('H:i:s', strtotime($row['start_time'])) : null);
            $ovHour->end_time = (!empty($row['end_time']) ? date('H:i:s', strtotime($row['end_time'])) : null);
            $ovHour->save();
        }
        return;
    }
}
