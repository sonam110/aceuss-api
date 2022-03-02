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
    public function PatientImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }
     public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
