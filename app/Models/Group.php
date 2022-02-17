<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    public function labels()
    {
        return $this->hasMany(Label::class, 'group_id', 'id');
    }

    public function LabelGroups()
    {
        return $this->hasOne(Label::class, 'group_id', 'id');
    }
}
