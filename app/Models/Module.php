<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class Module extends Model
{
    use HasFactory,LogsActivity;
    protected $appends = ['value'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'name',
        'status',
        'entry_mode',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function getValueAttribute()
    {
    	$data = strtolower(str_replace(' ','_',$this->name));
        return $data;
    }
}
