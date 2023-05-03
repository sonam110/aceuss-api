<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use App\Models\CompanyWorkShift;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Schedule;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class ScheduleTemplateData extends Model
{
    use HasFactory,TopMostParentId,LogsActivity;

    protected $appends = ['schedule_shifts'];

    protected $fillable = ['top_most_parent_id','schedule_template_id','shift_id','created_by','schedule_type','shift_name','shift_type','shift_date','shift_start_time','shift_end_time','shift_color','is_active','entry_mode'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function scheduleShifts()
    {
        return $this->hasMany(Schedule::class,'shift_id','shift_id')->where('shift_date',$this->shift_date)->where('schedule_template_id',$this->schedule_template_id);
    }

    public function getScheduleShiftsAttribute()
    {
        return Schedule::where('shift_id',$this->shift_id)->where('shift_date',$this->shift_date)->where('schedule_template_id',$this->schedule_template_id)->get();
    }
}
