<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TopMostParentId;

class OVHour extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable  = ['top_most_parent_id','group_token', 'title','start_time','end_time', 'date', 'end_date','entry_mode','ob_type'];

    public function getStartTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }

    public function getEndTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }
}
