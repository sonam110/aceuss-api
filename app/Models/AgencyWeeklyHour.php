<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class AgencyWeeklyHour extends Model
{
    use HasFactory;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected $fillable = [
        'user_id','name','weekly_hours_allocated','start_date','end_date'
    ];

    public function setStartDateAndTimeAttribute($value) {
      $this->attributes['start_date'] = (!empty($value)) ? date("Y-m-d", strtotime($value)) :null;
    }
    public function setEndDateAndTimeAttribute($value) {
      $this->attributes['end_date'] =  (!empty($value)) ? date("Y-m-d", strtotime($value)) : null;
    }
}
