<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\FollowupComplete;
use App\Models\PersonalInfoDuringIp;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;

class IpFollowUp extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $appends = ['witness_List'];
    protected $fillable =[
        'ip_id',
        'patient_id',
    	'top_most_parent_id',
        'branch_id',
    	'parent_id',
    	'title',
    	'description',
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
        'documents',
        'action_by',
        'action_date',
    	'status',
        'witness',
        'comment',
        'entry_mode',
        'is_latest_entry',
        'more_witness',
        'emp_id'
	];
	public function PatientImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
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
     public function persons()
    {
        return $this->hasMany(PersonalInfoDuringIp::class,'follow_up_id','id');
    }
     public function questions()
    {
        return $this->hasMany(FollowupComplete::class,'follow_up_id','id');
    }
    public function ActionByUser()
    {
        return $this->belongsTo(User::class,'action_by','id');
    }

    public function getWitnessListAttribute()
    {
        if(is_null($this->witness)== false && is_array(json_decode($this->witness)) && sizeof(json_decode($this->witness)) >0){
            $witnessList = PersonalInfoDuringIp::with('user:id,name,email,personal_number,avatar')->whereIn('user_id', json_decode($this->witness));
            if(!empty($this->ip_id))
            {
                $witnessList->where('ip_id', $this->ip_id);
            }
            /*if(!empty($this->follow_up_id))
            {
                $witnessList->where('follow_up_id', $this->follow_up_id);
            }*/
            $witnessList->where('follow_up_id', $this->follow_up_id);
            $witnessList = $witnessList->get();
            return (!empty($witnessList)) ? $witnessList : null;
        }
    }

}
