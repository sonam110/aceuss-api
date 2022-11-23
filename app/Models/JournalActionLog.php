<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Activity;
use App\Models\Deviation;
use App\Traits\TopMostParentId;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\Journal;
use App\Models\JournalAction;

class JournalActionLog extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'journal_action_id',
        'top_most_parent_id',
		'comment_action',
		'comment_result',
		'reason_for_editing',
		'edited_by',
        'comment_created_at'

    ];

    public function journalAction()
    {
        return $this->belongsTo(JournalAction::class,'journal_action_id','id');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class,'edited_by','id');
    }
   	
}
