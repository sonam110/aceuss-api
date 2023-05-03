<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Module;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

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

  protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

	public function module()
  {
      return $this->belongsTo(Module::class, 'module_id','id');
  }
}
