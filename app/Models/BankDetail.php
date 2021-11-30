<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
class BankDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'clearance_number',
        'is_default',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
