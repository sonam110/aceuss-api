<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
class EmergencyContact extends Model
{
    use HasFactory,TopMostParentId;
}
