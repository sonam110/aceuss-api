<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\User;

class Word extends Model
{
    use HasFactory, TopMostParentId;

    protected $fillable =[
        'top_most_parent_id',
        'name',
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
}
