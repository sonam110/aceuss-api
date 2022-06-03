<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OVHour extends Model
{
    use HasFactory,SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable  = ['top_most_parent_id','start_time','end_time','entry_mode'];
}
