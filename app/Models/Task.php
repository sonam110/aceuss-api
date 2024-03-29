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
use App\Models\Activity;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\Deviation;
use App\Models\Journal;
use DateTimeInterface;

class Task extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
     protected $appends = ['resource_data'];
    protected $fillable = [
    	'top_most_parent_id',
        'group_id',
		'type_id',
		'resource_id',
        'patient_id',
		'parent_id',
		'branch_id',
		'category_id',
		'subcategory_id',
		'title',
		'description',
		'start_date',
		'start_time',
        'how_many_time',
		'is_repeat',
		'every',
		'repetition_type',
		'how_many_time_array',
		'repeat_dates',
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
		'created_by',
		'edited_by',
        'action_by',
        'action_date',
        'comment',
        'status',
		'first_create_date',
        'is_latest_entry'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function assignEmployee()
    {
        return $this->hasMany(AssignTask::class,'task_id','id');
    }
    public function CategoryType()
    {
        return $this->belongsTo(CategoryType::class,'type_id','id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function Category()
    {
        return $this->belongsTo(CategoryMaster::class,'category_id','id')->withoutGlobalScope('top_most_parent_id')->withTrashed();
    }
    public function Subcategory()
    {
        return $this->belongsTo(CategoryMaster::class,'subcategory_id','id')->withoutGlobalScope('top_most_parent_id')->withTrashed();
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


    public function getResourceDataAttribute()
    {
        $result = [];
        if($this->type_id == '1'){
            $result['activity'] = Activity::select('id','title','description','start_date','start_time','end_date','end_time')->where('id',$this->resource_id)->first();
        }
        if($this->type_id == '2'){
            $result['ip'] = PatientImplementationPlan::select('id','title','goal','start_date','sub_goal','end_date')->where('id',$this->resource_id)->first();
        }
        if($this->type_id == '7'){
            $result['patient'] = User::select('id','name','email','contact_number','personal_number')->where('id',$this->resource_id)->first();
        }
        if($this->type_id == '8'){
            $result['employee'] = User::select('id','name','email','contact_number')->where('id',$this->resource_id)->first();
        }
        if($this->type_id == '5'){
            $result['follow_up'] = IpFollowUp::select('id','title','description','start_date','start_time','end_date','end_time')->where('id',$this->resource_id)->first();
        }
        if($this->type_id == '4'){
            $result['deviation'] = Deviation::select('id','activity_id','patient_id','description','activity_note','is_signed','date_time')->with('Patient:id,name')->where('id', $this->resource_id)->first();
        }
        if($this->type_id == '6'){
            $result['journal'] = Journal::select('id','activity_id','patient_id','description','date','time','is_signed','is_secret','is_active')->with('Patient:id,name')->where('id', $this->resource_id)->first();
        }

        return $result ;
        
        

    }

    public function actionBy()
    {
        return $this->belongsTo(User::class,'action_by','id');
    }

    public function branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }


}
