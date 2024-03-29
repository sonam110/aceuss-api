<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\TopMostParentId;
use App\Models\Activity;
use App\Models\PatientImplementationPlan;
use App\Models\PersonalInfoDuringIp;
use App\Models\User;
use App\Models\CategoryType;
use DateTimeInterface;

class RequestForApproval extends Model
{
    use HasFactory,LogsActivity,TopMostParentId;
    protected static $logAttributes = ['*'];
    protected $appends = ['type_id_Data'];
    protected static $logOnlyDirty = true;
    protected $fillable = [
        'top_most_parent_id',
		'requested_by',
		'requested_to',
		'request_type',
        'group_token',
		'request_type_id',
		'reason_for_requesting',
		'reason_for_rejection',
		'rejected_by',
		'approved_by',
		'approved_date',
        'approval_type',
        'other_info',
		'status',
        'sessionId',
        'entry_mode',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    public function RequestedBy()
    {
        return $this->belongsTo(User::class,'requested_by','id');
    }
    public function RequestedTo()
    {
        return $this->belongsTo(PersonalInfoDuringIp::class,'requested_to','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'requested_to','id');
    }
   
    public function RejectedBy()
    {
        return $this->belongsTo(User::class,'rejected_by','id');
    }
    public function ApprovedBy()
    {
        return $this->belongsTo(User::class,'approved_by','id');
    }

    public function getTypeIdDataAttribute()
    {
        $result = [];
        if($this->request_type == '1'){
            $result['activity'] = Activity::select('id','title','description','start_date','start_time','end_date','end_time')->where('id',$this->request_type_id)->first();
        }
        if($this->request_type == '2'){
            $result['ip'] = PatientImplementationPlan::select('*')->where('id',$this->request_type_id)->first();
        }
        if($this->request_type == '7'){
            $result['patient'] = User::select('id','name','email','contact_number','personal_number')->where('request_type','1')->first();
        }
        if($this->request_type == '8'){
            $result['employee'] = User::select('id','name','email','contact_number')->where('id',$this->request_type_id)->first();
        }
        if($this->request_type == '9'){
            $result['edit_ip_permission'] = PatientImplementationPlan::select('*')->where('id',$this->request_type_id)->first();
        }
        return $result ;
        
        

    }
}
