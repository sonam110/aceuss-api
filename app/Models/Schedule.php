<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use App\Models\CompanyWorkShift;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Schedule extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    
    protected $fillable  = ['top_most_parent_id','user_id','shift_id','parent_id','shift_name','shift_start_time','shift_end_time','shift_color','shift_date','leave_applied','leave_approved','status','entry_mode','patient_id','group_id','employee_assigned_working_hour_id','emergency','emergency_start_time','emergency_end_time','scheduled_work_hour','emergency_work_hour','schedule_type','schedule_template_id'];

    public function topMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function companyWorkShift()
    {
        return $this->belongsTo(CompanyWorkShift::class,'shift_id','id');
    }

    public function getShiftStartTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }

    public function getShiftEndTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }

    public function scheduleDates()
    {
        return $this->hasMany(self::class,'group_id','group_id');
    }
}
