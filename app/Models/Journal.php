<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Activity;
use App\Models\Deviation;
use App\Models\CategoryMaster;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\JournalLog;
use App\Models\JournalAction;
class Journal extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
		'activity_id',
        'activity_note',
		'top_most_parent_id',
		'branch_id',
        'patient_id',
		'emp_id',
		'category_id',
		'subcategory_id',
		'description',
		'reason_for_editing',
		'edited_by',
        'edit_date',
        'entry_mode',
        'date',
        'time',
        'is_signed',
        'signed_by',
        'signed_date',
        'is_secret',
        'is_active'

    ];
   	

    public function Activity()
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
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
    public function ApprovedBy()
    {
        return $this->belongsTo(User::class,'approved_by','id');
    }

    public function journalLogs()
    {
        return $this->hasMany(JournalLog::class,'journal_id','id')->orderBy('id','desc');
    }

    public function journalActions()
    {
        return $this->hasMany(JournalAction::class,'journal_id','id')->orderBy('id','desc');
    }

    public function branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }
}
