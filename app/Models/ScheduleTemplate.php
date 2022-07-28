<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use App\Models\CompanyWorkShift;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Schedule;
use App\Models\ScheduleTemplateData;

class ScheduleTemplate extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'top_most_parent_id',
    	'title',
    	'entry_mode',
    	'status',
    	'deactivation_date'
    ];

    public function templateData()
    {
        return $this->hasMany(ScheduleTemplateData::class,'schedule_template_id','id');
    }

    public function templateDataWithShift()
    {
        return $this->hasMany(ScheduleTemplateData::class,'schedule_template_id','id')->with('scheduleShifts');
    }
}
