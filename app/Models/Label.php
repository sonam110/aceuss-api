<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;

class Label extends Model
{
    use HasFactory;

    protected $fillable = ['label_name','label_value','language_id','group_id','status','entry_mode'];

    public function group()
    {
        return $this->belongsTo(Group::class,'group_id','id');
    }
}
