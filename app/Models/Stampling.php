<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Stampling extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];

    protected $fillable  = ['top_most_parent_id','user_id','in_time','out_time','in_location','out_location','extra_hours','reason_for_extra_hours','is_extra_hours_approved','is_scheduled_hours_ov_hours','scheduled_hours_rate','is_extra_hours_ov_hours','extra_hours_rate','scheduled_hours_sum','extra_hours_sum','total_sum','status','entry_mode'];
}
