<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\AssignTask;
use App\Models\CategoryType;
use App\Models\CategoryMaster;
class Task extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
    	'top_most_parent_id',
		'type_id',
		'resource_id',
		'parent_id',
		'branch_id',
		'category_id',
		'subcategory_id',
		'title',
		'description',
		'start_date',
		'start_time',
		'is_repeat',
		'every',
		'repetition_type',
		'week_days',
		'month_day',
		'end_date',
		'end_time',
        'address_url',
        'video_url',
        'information_url',
        'file',
		'remind_before_start',
        'before_minutes',
        'before_is_text_notify',
        'before_is_push_notify',
        'remind_after_end',
        'after_minutes',
        'after_is_text_notify',
        'after_is_push_notify',
        'is_emergency',
        'emergency_minutes',
        'emergency_is_text_notify',
        'emergency_is_push_notify',
        'in_time',
        'in_time_is_text_notify',
        'in_time_is_push_notify',
		'created_by',
		'edited_by',
		'status',
    ];

     public function assignEmployee()
    {
        return $this->hasMany(AssignTask::class,'task_id','id');
    }
    public function CategoryType()
    {
        return $this->belongsTo(CategoryType::class,'type_id','id');
    }
    public function Category()
    {
        return $this->belongsTo(CategoryMaster::class,'category_id','id');
    }
    public function Subcategory()
    {
        return $this->belongsTo(CategoryMaster::class,'subcategory_id','id');
    }
    public function setStartDateAttribute($value) {
      $this->attributes['start_date'] = (!empty($value)) ? date("Y-m-d", strtotime($value)) :null;
    }
    public function setStartTimeAttribute($value) {
      $this->attributes['start_time'] =  (!empty($value)) ? date("H:i:s", strtotime($value)) : null;
    }
    public function setEndDateAttribute($value) {
      $this->attributes['end_date'] =  (!empty($value)) ? date("Y-m-d", strtotime($value)):null;
    }
    public function setEndTimeAttribute($value) {
      $this->attributes['end_time'] =  (!empty($value)) ? date("H:i:s", strtotime($value)):null;
    }


}
