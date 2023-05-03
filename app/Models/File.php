<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Folder;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
use DateTimeInterface;

class File extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable = [
        'top_most_parent_id',
		'folder_id',
		'source_id',
		'source_name',
		'file_url',
		'file_type',
		'file_extension',
		'is_compulsory',
		'approval_required',
		'created_by',
		'approved_by',
		'approved_date',
		'visible_to_users',
        'entry_mode',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    public function Folder()
    {
        return $this->belongsTo(Folder::class,'folder_id','id');
    }
}


