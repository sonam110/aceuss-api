<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserTypeHasPermission;
class UserType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'entry_mode',
    ];

    public function allPermissions()
    {
       return $this->belongsToMany(UserTypeHasPermission::class,'user_type_id','id');
    }
}
