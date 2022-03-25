<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\AssignTask;
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
		'created_by',
		'edited_by',
		'status',
    ];

     public function assignEmployee()
    {
        return $this->hasMany(AssignTask::class,'task_id','id');
    }


}
