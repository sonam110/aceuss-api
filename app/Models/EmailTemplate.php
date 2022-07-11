<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_sms_for','mail_subject','mail_body','sms_body','notify_body','custom_attributes','module','type','event'
    ];
}
