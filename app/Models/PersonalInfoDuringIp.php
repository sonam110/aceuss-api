<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientImplementationPlan;
use App\Models\IpFollowUp;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class PersonalInfoDuringIp extends Model
{
    use HasFactory,LogsActivity,SoftDeletes;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
	    'patient_id',
	    'ip_id',
        'user_id',
		'follow_up_id',
        'is_presented',
        'is_participated',
        'how_helped',
        'is_approval_requested',
		'entry_mode',
	];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
	public function PatientImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }
    
    public function IpFollowUp()
    {
        return $this->belongsTo(IpFollowUp::class,'follow_up_id','id');
    }
    
    public function patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
