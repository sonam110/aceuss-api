<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use App\Models\CompanyWorkShift;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ScheduleTemplateData extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;

    protected $fillable = ['top_most_parent_id','schedule_template_id','shift_id','created_by','schedule_type','shift_name','shift_type','shift_date','shift_start_time','shift_end_time','shift_color','is_active','entry_mode'];
}
