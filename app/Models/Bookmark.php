<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\BookmarkMaster;
use DateTimeInterface;

class Bookmark extends Model
{
	use HasFactory,SoftDeletes;

    protected $fillable = ['bookmark_master_id', 'user_id', 'user_types'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function bookmarkMaster()
    {
        return $this->belongsTo(BookmarkMaster::class,'bookmark_master_id','id');
    }

}
