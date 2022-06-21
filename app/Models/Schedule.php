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
    
    protected $fillable  = ['top_most_parent_id','user_id','patient_id','shift_id','parent_id','created_by','slot_assigned_to','employee_assigned_working_hour_id','schedule_template_id','schedule_type','group_id','shift_name','shift_date','shift_start_time','shift_end_time','shift_color','leave_applied','leave_group_id','leave_type','leave_reason','leave_approved','leave_approved_date_time','leave_notified_to','is_active','scheduled_work_duration','extra_work_duration','status','entry_mode','notified_group','leave_approved_by'];

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

    public function leaves()
    {
        return $this->hasMany(self::class,'leave_group_id','leave_group_id');
    }

    public function leaveApprovedBy()
    {
        return $this->belongsTo(User::class,'leave_approved_by','id');
    }
}
