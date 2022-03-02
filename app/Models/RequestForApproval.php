<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
class RequestForApproval extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'top_most_parent_id',
		'requested_by',
		'requested_to',
		'request_type',
		'request_type_id',
		'reason_for_requesting',
		'reason_for_rejection',
		'rejected_by',
		'approved_by',
		'approved_date',
		'status',
        'entry_mode',
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    public function RequestedBy()
    {
        return $this->belongsTo(User::class,'requested_by','id');
    }
    public function RequestedTo()
    {
        return $this->belongsTo(User::class,'requested_to','id');
    }
    public function RejectedBy()
    {
        return $this->belongsTo(User::class,'rejected_by','id');
    }
    public function ApprovedBy()
    {
        return $this->belongsTo(User::class,'approved_by','id');
    }
}
