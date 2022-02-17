<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
class AssigneModule extends Model
{
    use HasFactory;
    protected $fillable =[
		'user_id',
		'module_id',
		'entry_mode',
	];

	public function Module()
    {
          return $this->belongsTo(Module::class,'parent_id','module_id');
    }
}
