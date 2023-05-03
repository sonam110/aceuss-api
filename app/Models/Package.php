<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class Package extends Model
{
    use HasFactory,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'name',
		'price',
		'is_on_offer',
		'discount_type',
		'discount_value',
		'discounted_price',
		'validity_in_days',
		'number_of_patients',
		'number_of_employees',
		'bankid_charges',
		'sms_charges',
		'is_sms_enable',
		'is_enable_bankid_charges',
		'status',
		'entry_mode',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
