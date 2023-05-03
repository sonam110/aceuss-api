<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class DeviceLoginHistory extends Model
{

    protected $table = 'device_login_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'device_id', 'device_model','device_token','login_via','status','user_token','ip_address'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function employeeInfo()
    {
        return $this->belongsTo('App\Models\Employee', 'id', 'user_id');
    }

}
