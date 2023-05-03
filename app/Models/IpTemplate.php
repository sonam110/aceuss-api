<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientImplementationPlan;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\TopMostParentId;
use DateTimeInterface;

class IpTemplate extends Model
{
    use HasFactory,LogsActivity,TopMostParentId;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
     protected $fillable =[
        'top_most_parent_id',
        'created_by',
        'ip_id',
    	'template_title',
    	'status',
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
    
}
