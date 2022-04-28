<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFile extends Model
{
    use HasFactory;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected $fillable = [
        'title','file_path','is_public','created_by'
    ];
}
