<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Group extends Model
{
    use HasFactory;

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    
    public function labels()
    {
        return $this->hasMany(Label::class, 'group_id', 'id');
    }

    public function LabelGroups()
    {
        return $this->hasOne(Label::class, 'group_id', 'id');
    }
}
