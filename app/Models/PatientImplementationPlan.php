<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\CategoryMaster;
use App\Traits\TopMostParentId;
use App\Models\PersonalInfoDuringIp;
use App\Models\IpAssigneToEmployee;
use App\Models\RequestForApproval;
use App\Models\IpFollowUp;
use App\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class PatientImplementationPlan extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    protected $appends = ['with_activity','with_followup','can_approve_this'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'top_most_parent_id',
        'user_id',
        'branch_id',
        'parent_id',
        'category_id',
        'subcategory_id',
        'title',
        'save_as_template',
        'goal',
        'limitations',
        'limitation_details',
        'how_support_should_be_given',
        'week_days',
        'how_many_time',
        'when_during_the_day',
        'who_give_support',
        'sub_goal',
        'sub_goal_details',
        'sub_goal_selected',
        'overall_goal',
        'overall_goal_details',
        'body_functions',
        'personal_factors',
        'health_conditions',
        'other_factors',
        'treatment',
        'working_method',
        'start_date',
        'end_date',
        'documents',
        'reason_for_editing',
        'created_by',
        'edited_by',
        'approved_by',
        'approved_date',
        'approval_comment',
        'action_by',
        'action_date',
        'comment',
        'status',
        'step_one',
        'step_two',
        'step_three',
        'step_four',
        'step_five',
        'step_six',
        'step_seven',
        'entry_mode',
        'is_latest_entry'
		
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }

    public function branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }
    
    public function patient()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function requestForApprovals()
    {
         return $this->hasMany(RequestForApproval::class, 'request_type_id', 'id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
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
    public function persons()
    {
        return $this->hasMany(PersonalInfoDuringIp::class,'ip_id','id');
    }
    public function patientPlan()
    {
        return $this->hasMany(self::class,'user_id','user_id');
    }
    public function patientActivity()
    {
        return $this->hasMany(Activity::class,'patient_id','user_id');
    }
    public function assignEmployee()
    {
        return $this->belongsTo(IpAssigneToEmployee::class,'id','ip_id');
    }

    public function ipFollowUps()
    {
        $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
        return $this->hasMany(IpFollowUp::class,'ip_id','id');
    }

    public function activities()
    {
        $date = date('Y-m-d',strtotime('-'.ENV('CALCULATE_FOR_DAYS').' days'));
        return $this->hasMany(Activity::class,'ip_id','id');
    }

    public function setStartDateAndTimeAttribute($value) {
      $this->attributes['start_date'] = (!empty($value)) ? date("Y-m-d", strtotime($value)) :null;
    }
    public function setEndDateAndTimeAttribute($value) {
      $this->attributes['end_date'] =  (!empty($value)) ? date("Y-m-d", strtotime($value)) : null;
    }

    public function getWithActivityAttribute()
    {
        $withActivity = 0;
        $activity = Activity::where('ip_id',$this->id)->count();
        if($activity > 0)
        {
            $withActivity = 1;
        } 
        return $withActivity;       
    }

    public function getWithFollowUpAttribute()
    {
        $withFollowUp = 0;
        $followUp = IpFollowUp::where('ip_id',$this->id)->count();
        if($followUp > 0)
        {
            $withFollowUp = 1;
        }
        return $withFollowUp;
    }

    public function getCanApproveThisAttribute()
    {
        $data = $this;
        $can_approve_this = 0;
        $getUserId = PersonalInfoDuringIp::select('id','user_id')
            ->where('user_id', auth()->id())
            ->first();
        if($getUserId)
        {
            $isApprovedRequest = RequestForApproval::where('requested_to', $getUserId->id)
            ->where(function ($q) use ($data) {
                $q->where('request_type_id', $data->parent_id)
                    ->orWhere('request_type_id', $data->id);
            })
            ->count();
            if($isApprovedRequest > 0)
            {
                $can_approve_this = 1;
            }
        }
        return $can_approve_this;
    }

    
}
