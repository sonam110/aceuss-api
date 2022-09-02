<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Activity;
use App\Models\Journal;
use App\Models\CategoryMaster;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
class Deviation extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'top_most_parent_id',
		'activity_id',
		'branch_id',
        'patient_id',
		'emp_id',
		'category_id',
		'subcategory_id',
		'date_time',
		'description',
        'activity_note',
		'immediate_action',
		'probable_cause_of_the_incident',
		'suggestion_to_prevent_event_again',
		'critical_range',
        'further_investigation',
        'follow_up',
        'is_secret',
        'is_signed',
        'sessionId',
        'is_completed',
        'completed_date',
        'completed_by',
        'reason_for_editing',
        'edited_by',
        'edited_date',
        'entry_mode',
        'related_factor'

    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }

    public function Activity()
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }
    
    public function Patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
    }
    public function Employee()
    {
        return $this->belongsTo(User::class,'emp_id','id');
    }
    public function Category()
    {
        return $this->belongsTo(CategoryMaster::class,'category_id','id')->withoutGlobalScope('top_most_parent_id');
    }
    public function Subcategory()
    {
        return $this->belongsTo(CategoryMaster::class,'subcategory_id','id')->withoutGlobalScope('top_most_parent_id');
    }
    public function EditedBy()
    {
        return $this->belongsTo(User::class,'edited_by','id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class,'completed_by','id');
    }

    public function branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }
}
