<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Package;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
class Subscription extends Model
{
    use HasFactory,LogsActivity;
    protected $casts = [
    'package_details' => 'json',
    ];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable=[
        'user_id',
		'package_id',
		'package_details',
		'licence_key',
		'start_date',
		'end_date',
		'status',
        'entry_mode',
    ];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
     public function Package()
    {
        return $this->belongsTo(Package::class,'package_id','id');
    }
}
