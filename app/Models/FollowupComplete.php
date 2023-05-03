<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\IpFollowUp;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class FollowupComplete extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
	    'follow_up_id',
	    'question_id',
		'question',
		'answer',
		'entry_mode',
	];

	protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
