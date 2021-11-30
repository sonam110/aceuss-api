<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
class Folder extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $fillable =[
		'parent_id',
		'top_most_parent_id',
		'name',
		'visible_to_users',
		'status',
	];
	public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
    }
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
}
