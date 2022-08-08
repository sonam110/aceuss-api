<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\BookmarkMaster;

class Bookmark extends Model
{
	use HasFactory;

    protected $fillable = ['bookmark_master_id', 'user_id', 'user_types'];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function bookmarkMaster()
    {
        return $this->belongsTo(BookmarkMaster::class,'bookmark_master_id','id');
    }

}
