<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class CompanyWorkShift extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'top_most_parent_id',
		'shift_name',
		'shift_start_time',
		'shift_end_time',
		'shift_color',
		'status',
        'entry_mode',
        'shift_type'

    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }

    public function getShiftStartTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }

    public function getShiftEndTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }

    public function getRestStartTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }

    public function getRestEndTimeAttribute($value)
    {
        return (!empty($value)) ? date('H:i', strtotime($value)) : NULL;
    }


}
