<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\TopMostParentId;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientCashier extends Model
{
    use HasFactory,TopMostParentId,LogsActivity,SoftDeletes;

    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'top_most_parent_id',
        'branch_id',
        'patient_id',
        'receipt_no',
        'date',
        'type',
        'amount',
        'file',
        'comment',
        'created_by',
        'entry_mode',
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }

    public function Branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }
    
    public function Patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }

}
