<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\TopMostParentId;
use App\Models\Activity;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use App\Models\CategoryType;
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
		'request_type_id',
		'reason_for_requesting',
		'reason_for_rejection',
		'rejected_by',
		'approved_by',
		'approved_date',
        'approval_type',
		'status',
        'entry_mode',
    ];

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
            $result['ip'] = PatientImplementationPlan::select('id','title','goal','start_date','sub_goal','end_date')->where('id',$this->request_type_id)->first();
        }
        if($this->request_type == '7'){
            $result['patient'] = User::select('id','name','email','contact_number','personal_number')->where('id',$this->request_type_id)->first();
        }
        if($this->request_type == '8'){
            $result['employee'] = User::select('id','name','email','contact_number')->where('id',$this->request_type_id)->first();
        }
        if($this->request_type == '9'){
            $result['edit_ip_permission'] = PatientImplementationPlan::select('id','title','category_id','goal','start_date','sub_goal','end_date')->where('id',$this->request_type_id)->first();
        }

        return $result ;
        
        

    }
}
