<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use App\Models\UserType;
use DateTimeInterface;

class UserTypeHasPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type_id',
        'permission_id'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
	public function allUserTypes()
    {
        return $this->belongsToMany(UserType::class, 'user_types')->withTimestamps();
    }
    public function permission()
    {
        return $this->belongsTo(Permission::class,'permission_id','id');
    }
}
