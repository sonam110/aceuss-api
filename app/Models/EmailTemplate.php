<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
class EmailTemplate extends Model
{
    use HasFactory,TopMostParentId;

    protected $fillable = [
        'top_most_parent_id', 'mail_sms_for','mail_subject','mail_body','sms_body','notify_body','custom_attributes'
    ];
}
