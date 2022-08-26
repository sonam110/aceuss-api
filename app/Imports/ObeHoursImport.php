<?php

namespace App\Imports;

use App\Models\Label;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Hash;
use Auth;
use App\Models\OvHour;
use Str;

class ObeHoursImport implements ToModel,WithHeadingRow
{

    public function model(array $row)
    {
        $ovHour = new OVHour;
        $ovHour->title = $row['title'];
        $ovHour->date = $row['date'];
        $ovHour->ob_type = $row['ob_type'];
        $ovHour->start_time = $row['start_time'];
        $ovHour->end_time = $row['end_time'];
        $ovHour->save();
        return;
    }
}
