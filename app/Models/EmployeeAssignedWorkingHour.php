<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeAssignedWorkingHour extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;
    protected $dates = ['deleted_at'];
    
    protected $fillable  = ['top_most_parent_id','emp_id','created_by','municipal_name','assigned_working_hour_per_week','working_percent','actual_working_hour_per_week','entry_mode'];

    public function topMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    
    public function employee()
    {
        return $this->belongsTo(User::class,'emp_id','id');
    }
}
