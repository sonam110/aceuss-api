<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class SmsTemplate extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'sms_for', 'sms_body', 'custom_attributes'
    ];

    protected static $logAttributes = ['*'];
    
    protected static $logOnlyDirty = false;

    protected static $logName = 'sms_templates';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
