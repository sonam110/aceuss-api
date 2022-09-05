<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class AssigneModule extends Model
{
    use HasFactory,LogsActivity,SoftDeletes;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
		'user_id',
		'module_id',
		'entry_mode',
	];

	public function module()
  {
      return $this->belongsTo(Module::class, 'module_id','id');
  }
}
