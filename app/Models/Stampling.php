<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;
use App\Models\Schedule;

class Stampling extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];

    protected $fillable  = [
        'top_most_parent_id',
        'user_id', 
        'stampling_type',
        'date',
        'in_time',
        'in_location',
        'reason_for_early_in',
        'reason_for_late_in',
        'out_time',
        'out_location',
        'reason_for_early_out',
        'reason_for_late_out',
        'is_extra_hours_approved',
        'scheduled_hours_rate',
        'extra_hours_rate',
        'ob_hours_rate',
        'total_schedule_hours',
        'total_extra_hours',
        'total_ob_hours',
        'entry_mode',
        'working_percent',
        'reason_for_rejection'
    ];


    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class,'schedule_id','id');
    }
}
