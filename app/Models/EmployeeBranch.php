<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;

class EmployeeBranch extends Model
{
    use HasFactory, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'employee_id', 'branch_id'     
    ];

    public function branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class,'employee_id','id');
    }
}
