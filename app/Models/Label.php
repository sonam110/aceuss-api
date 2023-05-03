<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;
use DateTimeInterface;

class Label extends Model
{
    use HasFactory;

    protected $fillable = ['label_name','label_value','language_id','group_id','status','entry_mode'];
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function group()
    {
        return $this->belongsTo(Group::class,'group_id','id');
    }
}
