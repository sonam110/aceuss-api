<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class SalaryDetail extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'user_id',
        'salary_per_month',
        'salary_package_start_date',
        'salary_package_end_date',
        'entry_mode',
    ];
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
