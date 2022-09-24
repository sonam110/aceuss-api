<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;

class PatientEmployee extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable =[
        'patient_id', 'employee_id'     
    ];

    public function patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class,'employee_id','id');
    }
}
