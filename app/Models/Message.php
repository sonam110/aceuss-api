<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
use Carbon\Carbon;
use DateTimeInterface;

class Message extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'sender_id',
		'receiver_id',
		'message',
        'file_path',
        'file_type',
		'read_at',
        'entry_mode',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function sender()
    {
    	return $this->hasOne(User::class,'id','sender_id')->withoutGlobalScope('top_most_parent_id');
    
    }
    public function receiver()
    {
        return $this->hasOne(User::class,'id','receiver_id')->withoutGlobalScope('top_most_parent_id');
    }

    
}
