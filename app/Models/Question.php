<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
class Question extends Model
{
    use HasFactory,LogsActivity,SoftDeletes;
     protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'top_most_parent_id',
        'created_by',
        'group_name',
        'question',
        'is_visible',
        'status',
        'entry_mode',
    ];
}
