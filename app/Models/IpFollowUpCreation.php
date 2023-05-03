<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientImplementationPlan;
use App\Models\IpFollowUp;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class IpFollowUpCreation extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
	    'ip_id',
		'follow_up_id',
		'name',
		'email',
		'contact_number',
		'full_address',
		'is_family_member',
		'is_caretaker',
		'is_contact_person',
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
}
