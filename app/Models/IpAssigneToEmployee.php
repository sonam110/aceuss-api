<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientImplementationPlan;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
class IpAssigneToEmployee extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'ip_id',
    	'user_id',
    	'status',
        'entry_mode',

    ];

    protected $appends = ['employee'];
    public function PatientImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }
     public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

     public function getEmployeeAttribute()
    {
        if(is_null($this->user_id)== false){
            $employee = User::select('id','name','email','contact_number')->where('id',$this->user_id)->first();
            return (!empty($employee)) ? $employee : null;
        }
        

    }
}
