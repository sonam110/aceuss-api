<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Activity;
use App\Models\Deviation;
use App\Models\CategoryMaster;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Journal;
use App\Models\JournalActionLog;


class JournalAction extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'journal_id',
        'top_most_parent_id',
        'comment_action',
		'comment_result',
		'reason_for_editing',
		'edited_by',
        'edit_date',
        'is_signed',
        'signed_by',
        'signed_date',
        'sessionId',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class,'journal_id','id');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class,'edited_by','id');
    }

    public function journalActionLogs()
    {
        return $this->hasMany(JournalActionLog::class,'journal_action_id','id')->orderBy('id','desc');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class,'signed_by','id');
    }

   	
}
