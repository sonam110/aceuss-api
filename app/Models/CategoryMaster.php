<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\CategoryType;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
class CategoryMaster extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected $fillable = [
        'top_most_parent_id',
        'created_by',
        'parent_id',
        'category_type_id',
        'name',
        'category_color',
        'is_global',
        'status',
        'entry_mode',
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
    }
    public function CategoryType()
    {
        return $this->belongsTo(CategoryType::class,'category_type_id','id');
    }
}
