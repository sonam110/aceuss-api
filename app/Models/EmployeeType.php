<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class EmployeeType extends Model
{
    use HasFactory;

    protected $appends = ['value'];
    protected $fillable=['id','designation'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function getValueAttribute()
    {
    	$data = strtolower(str_replace(' ','_',$this->designation));
        return $data;
    }
}
