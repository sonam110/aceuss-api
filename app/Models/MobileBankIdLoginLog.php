<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyId;;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;
use App\Traits\TopMostParentId;
use Str;
class MobileBankIdLoginLog extends Model
{
    use HasFactory, CompanyId, LogsActivity,TopMostParentId;

    protected $fillable = [
        'uuid', 'top_most_parent_id', 'sessionId', 'personnel_number', 'name'
    ];

    protected static $logAttributes = ['*'];
    
    protected static $logOnlyDirty = false;

    protected static $logName = 'mobile_bank_id_login_logs';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
    public function company()
    {
        return $this->belongsTo(User::class, 'top_most_parent_id', 'id')->withoutGlobalScope('top_most_parent_id')->withTrashed();
    }
}
