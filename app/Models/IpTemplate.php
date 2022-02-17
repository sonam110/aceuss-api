<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientImplementationPlan;
class IpTemplate extends Model
{
    use HasFactory;
     protected $fillable =[
        'ip_id',
    	'template_title',
    	'status',
        'entry_mode',

    ];


    public function PatientImplementationPlan()
    {
        return $this->belongsTo(PatientImplementationPlan::class,'ip_id','id');
    }
    
}
