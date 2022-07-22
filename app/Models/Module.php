<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
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
    public function getValueAttribute()
    {
    	$data = strtolower(str_replace(' ','_',$this->name));
        return $data;
    }
}
