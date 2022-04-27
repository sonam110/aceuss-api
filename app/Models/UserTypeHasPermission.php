<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use App\Models\UserType;
class UserTypeHasPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type_id',
        'permission_id'
    ];

	public function allUserTypes()
    {
        return $this->belongsToMany(UserType::class, 'user_types')->withTimestamps();
    }
    public function permission()
    {
        return $this->belongsTo(Permission::class,'permission_id','id');
    }
}
