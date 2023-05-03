<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use DateTimeInterface;

class ScheduleStamplingDatewiseReport extends Model
{
    use HasFactory,TopMostParentId;

    protected $fillable  = ['top_most_parent_id','user_id','shift_date','scheduled_duration','extra_duration','ob_duration','stampling_duration','regular_duration','date'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
