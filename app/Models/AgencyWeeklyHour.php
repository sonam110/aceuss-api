<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class AgencyWeeklyHour extends Model
{
    use HasFactory,SoftDeletes;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected $fillable = [
        'user_id','name','assigned_hours','start_date','end_date','assigned_hours_per_day','assigned_hours_per_week','assigned_hours_per_month','scheduled_hours','completed_hours','remaining_hours','approved_by_patient'
    ];

    public function setStartDateAndTimeAttribute($value) {
      $this->attributes['start_date'] = (!empty($value)) ? date("Y-m-d", strtotime($value)) :null;
    }
    public function setEndDateAndTimeAttribute($value) {
      $this->attributes['end_date'] =  (!empty($value)) ? date("Y-m-d", strtotime($value)) : null;
    }
}
