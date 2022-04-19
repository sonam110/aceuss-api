<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;
class Comment extends Model
{
    use HasFactory,LogsActivity;
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $appends = ['comment_by'];
    protected $fillable =[
        'parent_id',
		'source_id',
		'source_name',
		'comment',
		'created_by',
		'replied_to',
		'edited_by',
        'entry_mode',
    ];

    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function commentBy()
    {
          return $this->belongsTo(User::class,'created_by','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
    }

    public function reply()
    {
        return $this->hasMany(self::class,'parent_id','id');
    }

    public function getCommentByAttribute()
    {
        $comment_by = User::select('id','name')->where('id',$this->created_by)->first();
        return $comment_by;
        

    }
}
