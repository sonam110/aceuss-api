<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use App\Models\CompanyWorkShift;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ScheduleTemplate extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'top_most_parent_id',
    	'title',
    	// 'from_date',
    	// 'to_date',
    	// 'shifts',
    	'entry_mode',
    	'status',
    	'deactivation_date'
    ];
}
