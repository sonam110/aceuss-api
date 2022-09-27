<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class LicenceKeyManagement extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $dates = ['deleted_at'];
    
    protected $connection = 'mysql2';
    
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    protected $fillable =[
        'top_most_parent_id',
        'created_by',
        'licence_key',
        'module_attached',
        'package_details',
        'active_from',
        'expire_at',
        'is_used', 'is_expired', 'cancelled_by','reason_for_cancellation'
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
}
