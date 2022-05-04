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

class JournalAction extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
        'description',
		'result',
		'reason_for_editing',
		'edited_by',
        'is_signed',
        'journal_id'
    ];
   	
}
