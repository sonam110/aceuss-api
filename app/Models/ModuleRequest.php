<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use App\Models\User;
use DateTimeInterface;

class ModuleRequest extends Model
{
    use HasFactory;
    protected $appends = ['module_names'];
    protected $fillable = ['user_id','modules','request_comment','reply_comment','reply_date','status','entry_mode'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function getModuleNamesAttribute()
    {
    	$data = json_decode($this->modules);
    	$names = [];
    	foreach ($data as $key => $value) {
    		$names[] = Module::find($value)->name;
    	}
    	$module_names = implode(', ', $names);
    	return $module_names;
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
