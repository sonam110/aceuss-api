<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
class BankDetail extends Model
{
    use HasFactory,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'clearance_number',
        'is_default',
        'entry_mode',
    ];
    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
