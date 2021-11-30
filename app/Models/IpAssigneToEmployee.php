<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientImplementationPlan;
use App\Models\User;
class IpAssigneToEmployee extends Model
{
    use HasFactory;
    protected $fillable =[
    	'ip_id',
    	'user_id',
    	'status',

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
