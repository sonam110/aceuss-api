<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bookmark;

class BookmarkMaster extends Model
{
    use HasFactory;

    protected $fillable = ['target','title','icon','link','user_types'];

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class,'bookmark_master_id','id');
    }
}
