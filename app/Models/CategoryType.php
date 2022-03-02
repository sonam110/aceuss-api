<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
class CategoryType extends Model
{
    use HasFactory,SoftDeletes,LogsActivity;
   // protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'top_most_parent_id',
        'created_by',
        'name',
        'status',
        'entry_mode',
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    public function createdby()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }


}
