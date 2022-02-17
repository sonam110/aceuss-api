<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class SalaryDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'salary_per_month',
        'salary_package_start_date',
        'salary_package_end_date',
        'entry_mode',
    ];
    
    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
