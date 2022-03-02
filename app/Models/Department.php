<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
class Department extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId,LogsActivity;

    

    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'user_id',
        'top_most_parent_id',
        'parent_id',
        'name',
        'status',
        'entry_mode',
    ];

  

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    
    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

   

    public function grandchildren()
    {
        return $this->children()->with('grandchildren');
    }

    public function parentUnit() 
    {
        return $this->belongsTo(Department::class,'parent_id', 'id');
    }

    public function getLatestParent()
    {
        if ($this->parentUnit)
            return $this->parentUnit->getLatestParent();

        return $this;
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

  
}
