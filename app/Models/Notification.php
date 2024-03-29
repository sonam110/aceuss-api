<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use DateTimeInterface;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type','user_id','sender_id', 'status_code','device_id','device_platform','user_type','module','title','sub_title','message','image_url','screen', 'data_id','read_status','read_at','event','extra_param'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
