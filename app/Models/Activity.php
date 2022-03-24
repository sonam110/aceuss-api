<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ActivityClassification;
use App\Models\CompanyWorkShift;
use App\Models\CategoryMaster;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
class Activity extends Model
{
    use HasFactory,TopMostParentId,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'top_most_parent_id',
		'parent_id',
		'activity_class_id',
		'ip_id',
		'branch_id',
        'patient_id',
		'emp_id',
		'shift_id',
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
		'external_link',
		'activity_status',
		'done_by',
		'not_done_by',
		'not_done_reason',
		'not_applicable_reason',
		'notity_to_users',
		'reason_for_editing',
		'created_by',
		'edited_by',
		'edit_date',
		'approved_by',
		'approved_date',
        'question',
        'selected_option',
        'comment',
		'status',
		'remind_before_start',
		'remind_after_end',
        'entry_mode',
	];
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
        return $this->belongsTo(CategoryMaster::class,'category_id','id');
    }
    public function Subcategory()
    {
        return $this->belongsTo(CategoryMaster::class,'subcategory_id','id');
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

}
