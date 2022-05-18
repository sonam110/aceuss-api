<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenceHistory extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $connection = 'mysql2';
    
    protected $dates = ['deleted_at'];
    
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    protected $fillable =[
        'top_most_parent_id',
        'created_by',
        'license_key',
        'module_attached',
        'package_details',
        'active_from',
        'expire_at'
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
