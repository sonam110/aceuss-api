<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class AssignTask extends Model
{
    use HasFactory;
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
    public function employee()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
