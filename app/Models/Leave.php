<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
    	'top_most_parent_id','user_id','schedule_id','date','reason','status','entry_mode','is_approved','approved_by',"approved_date",'approved_time','group_id','assigned_to','assign_status','notified_to','notified_group'
    ];

    public function topMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function leaves()
    {
        return $this->hasMany(self::class,'group_id','group_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class,'approved_by','id');
    }
}
