<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientImplementationPlan;
use App\Models\IpFollowUp;
class PersonalInfoDuringIp extends Model
{
    use HasFactory;
    protected $fillable =[
	    'ip_id',
		'follow_up_id',
		'name',
		'email',
		'contact_number',
		'country',
		'city',
		'postal_area',
		'zipcode',
		'full_address',
		'is_family_member',
		'is_caretaker',
		'is_contact_person',
		'entry_mode',
	];

	public function PatientImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }
    
    public function IpFollowUp()
    {
        return $this->belongsTo(IpFollowUp::class,'follow_up_id','id');
    }
}
