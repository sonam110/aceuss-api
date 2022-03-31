<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Traits\AutoInc;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;
use App\Models\Booking;

class SmsLog extends Model
{
    use HasFactory, AutoInc, TopMostParentId, LogsActivity;

    protected $fillable = [
        'id_inc', 'top_most_parent_id', 'type_id','resource_id', 'mobile', 'message', 'status'
    ];

    protected static $logAttributes = ['*'];
    
    protected static $logOnlyDirty = false;

    protected static $logName = 'sms_logs';

    public function getCreatedAtAttribute($value)
    {
        return (!empty($value)) ? date('Y-m-d H:i:s', strtotime($value)) : NULL;
    }

    public function getUpdatedAtAttribute($value)
    {
        return (!empty($value)) ? date('Y-m-d H:i:s', strtotime($value)) : NULL;
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'top_most_parent_id', 'id')->withoutGlobalScope('top_most_parent_id')->withTrashed();
    }

   
}
