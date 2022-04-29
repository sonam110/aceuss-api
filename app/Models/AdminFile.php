<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;

class AdminFile extends Model
{
    use HasFactory, TopMostParentId, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected $fillable = [
        'top_most_parent_id','title','file_path','is_public','created_by','user_type_id'
    ];
}
