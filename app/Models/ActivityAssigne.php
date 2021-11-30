<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Activity;
class ActivityAssigne extends Model
{
    use HasFactory;
    protected $fillable =[
    	'activity_id',
		'user_id',
		'assignment_date',
		'assignment_day',
		'assigned_by',
		'status',
    ];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function Activity()
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }
}
