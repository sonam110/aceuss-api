<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserTypeHasPermission;
use DateTimeInterface;

class UserType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'entry_mode',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function allPermissions()
    {
       return $this->belongsToMany(UserTypeHasPermission::class,'user_type_id','id');
    }
}
