<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use DateTimeInterface;

class AssignTask extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable =[
        'task_id',
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
    
    public function employee()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
