<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ActivityClassification;
use App\Models\CompanyWorkShift;
use App\Models\ActivityAssigne;
use App\Models\CategoryMaster;
use App\Models\PatientImplementationPlan;
use App\Models\Comment;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Journal;
use App\Models\Deviation;
use App\Models\Task;
use DateTimeInterface;

class Activity extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $appends = ['with_journal','with_deviation'];
    // protected $withJournal = ['journal'];
    // protected $withDeviation = ['deviation'];
    protected $fillable =[
            'top_most_parent_id', 'group_id', 'parent_id', 'ip_id', 'branch_id', 'patient_id', 'emp_id', 'shift_id', 'category_id', 'subcategory_id', 'title', 'description', 'start_date', 'start_time', 'how_many_time', 'is_repeat', 'every', 'repetition_type', 'how_many_time_array', 'repeat_dates', 'end_date', 'end_time', 'address_url', 'video_url', 'information_url', 'file', 'reason_for_editing', 'created_by', 'edited_by', 'edit_date', 'approved_by', 'approved_date', 'selected_option', 'comment', 'internal_comment', 'external_comment', 'remind_before_start', 'before_minutes', 'before_is_text_notify', 'before_is_push_notify', 'remind_after_end', 'after_minutes', 'after_is_text_notify', 'after_is_push_notify', 'is_emergency', 'emergency_minutes', 'emergency_is_text_notify', 'emergency_is_push_notify', 'in_time', 'in_time_is_text_notify', 'in_time_is_push_notify', 'is_risk', 'message', 'is_compulsory', 'status', 'action_by', 'action_date', 'entry_mode', 'activity_tag', 'repetition_comment', 'action_comment', 'is_latest_entry','notify_before_time','notify_after_time','notify_emergency_time','notify_in_time'
	];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function ImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }

    public function branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }
    
	public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    public function ActivityClassification()
    {
        return $this->belongsTo(ActivityClassification::class,'activity_class_id','id');
    }
    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
    }
    public function Patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
    }
    public function Employee()
    {
        return $this->belongsTo(User::class,'emp_id','id');
    }
    public function CompanyWorkShift()
    {
        return $this->belongsTo(CompanyWorkShift::class,'shift_id','id');
    }
    public function Category()
    {
        return $this->belongsTo(CategoryMaster::class,'category_id','id')->withoutGlobalScope('top_most_parent_id')->withTrashed();
    }
    public function Subcategory()
    {
        return $this->belongsTo(CategoryMaster::class,'subcategory_id','id')->withoutGlobalScope('top_most_parent_id')->withTrashed();
    }
    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function EditedBy()
    {
        return $this->belongsTo(User::class,'edited_by','id');
    }
    public function ApprovedBy()
    {
        return $this->belongsTo(User::class,'approved_by','id');
    }

    public function ActionByUser()
    {
        return $this->belongsTo(User::class,'action_by','id');
    }

    public function assignEmployee()
    {
        return $this->hasMany(ActivityAssigne::class,'activity_id','id');
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

    // public function getCommentsAttribute()
    // {
    //     $comments = Comment::where('source_name','Activity')->where('source_id',$this->id)->with('commentBy:id,name')->get();
    //     return $comments;
        

    // }

    public function getWithJournalAttribute()
    {
        $activity_journal = Journal::where('activity_id',$this->id)->whereNull('deleted_at')->count();
        if($activity_journal >= 1)
        {
            $withJournal = 1;
        }
        else
        {
            $withJournal = 0;
        }
        return $withJournal;
        

    }

    public function getWithDeviationAttribute()
    {
        $activity_deviation = Deviation::where('activity_id',$this->id)->whereNull('deleted_at')->count();
        if($activity_deviation >= 1)
        {
            $withDeviation = 1;
        }
        else
        {
            $withDeviation = 0;
        }
        return $withDeviation;
        

    }

    public function comments()
    {
        return $this->hasMany(Comment::class,'source_id','id')->where('source_name','Activity');
    }


    public function tasks()
    {
         return $this->hasMany(Task::class,'resource_id','id')->where('type_id','1');
    }

}
