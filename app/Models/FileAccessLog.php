<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;
use App\Models\AdminFile;
use DateTimeInterface;

class FileAccessLog extends Model
{
    use HasFactory, TopMostParentId, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected $fillable = [
        'top_most_parent_id','admin_file_id','user_id'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function adminFile()
    {
        return $this->belongsTo(AdminFile::class,'admin_file_id','id');
    }
}
