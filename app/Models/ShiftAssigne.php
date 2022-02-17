<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\CompanyWorkShift;
use App\Traits\TopMostParentId;
class ShiftAssigne extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId;
    protected $dates = ['deleted_at'];
    protected $fillable =[
        'top_most_parent_id',
        'user_id',
        'shift_id',
        'shift_start_date',
        'shift_end_date',
        'created_by',
        'status',
        'entry_mode',

    ];
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    
    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function CompanyWorkShift()
    {
        return $this->belongsTo(CompanyWorkShift::class,'shift_id','id');
    }


}
