<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
class Message extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'top_most_parent_id',
        'sender_id',
		'receiver_id',
		'message',
        'file_path',
		'read_at',
        'entry_mode',
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }

    public function sender()
    {
    	return $this->hasOne(User::class,'id','sender_id');
    
    }
    public function receiver()
    {
        return $this->hasOne(User::class,'id','receiver_id');
    }

    public function unreadMessages()
    {
        return $this->hasMany(self::class, 'receiver_id', 'id')
            ->whereNull('read_at')
            ->withoutGlobalScope('top_most_parent_id');
    }
}
