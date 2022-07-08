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
        'user_id','name','assigned_hours','start_date','end_date','assigned_hours_per_day','assigned_hours_per_week','assigned_hours_per_month','planning_done_hours','planning_remaining_hours','work_done_hours','work_remaining_hours','approved_by_patient'
    ];

    public function setStartDateAndTimeAttribute($value) {
      $this->attributes['start_date'] = (!empty($value)) ? date("Y-m-d", strtotime($value)) :null;
    }
    public function setEndDateAndTimeAttribute($value) {
      $this->attributes['end_date'] =  (!empty($value)) ? date("Y-m-d", strtotime($value)) : null;
    }
}
