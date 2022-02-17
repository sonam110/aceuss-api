<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable =[
        'parent_id',
		'source_id',
		'source_name',
		'comment',
		'created_by',
		'replied_to',
		'edited_by',
        'entry_mode',
    ];

    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
    }
}
