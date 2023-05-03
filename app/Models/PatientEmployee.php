<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;
use DateTimeInterface;

class PatientEmployee extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable =[
        'patient_id', 'employee_id'     
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class,'employee_id','id');
    }
}
