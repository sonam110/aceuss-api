<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class ActivityAssigne extends Model
{
    use HasFactory,LogsActivity,softDeletes;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'activity_id',
		'user_id',
		'assignment_date',
		'assignment_day',
		'assigned_by',
		'reason',
        'status',
        'is_notify',
        'entry_mode',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function Activity()
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }
    
    public function employee()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class,'assigned_by','id');
    }
}
