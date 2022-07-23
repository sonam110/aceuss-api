<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;
use App\Models\Schedule;
use Spatie\Activitylog\Traits\LogsActivity;

class UserScheduledDate extends Model
{
    use HasFactory,LogsActivity,TopMostParentId;
    protected $fillable = ['top_most_parent_id','working_percent','emp_id','start_date','end_date','entry_mode'];


    public function schedules($start_date,$end_date)
    {
        return $this->hasMany(Schedule::class,'emp_id','user_id')->whereBetween('shift_date',[$start_date,$end_date]);
    }
}
