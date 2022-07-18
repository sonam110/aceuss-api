<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EmployeeType extends Model
{
    use HasFactory;

    protected $appends = ['value'];
    protected $fillable=['id','designation'];

    public function getValueAttribute()
    {
    	$data = strtolower(str_replace(' ','_',$this->designation));
        return $data;
    }
}
