<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
class IpFollowUp extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
    'ip_id',
	'top_most_parent_id',
	'parent_id',
	'title',
	'description',
	'follow_up_type',
	'repetition_type',
	'repetition_days',
	'start_date',
	'start_time',
	'is_completed',
	'end_date',
	'end_time',
	'remarks',
	'reason_for_editing',
	'created_by',
	'edited_by',
	'approved_by',
	'approved_date',
	'status',
    'entry_mode',


	];
	public function PatientImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
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
