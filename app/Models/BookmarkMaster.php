<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bookmark;
use DateTimeInterface;

class BookmarkMaster extends Model
{
    use HasFactory;

    protected $fillable = ['target','title','icon','link','user_types','icon_type'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class,'bookmark_master_id','id');
    }
}
