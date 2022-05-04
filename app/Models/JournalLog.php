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

class JournalLog extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes,LogsActivity;
    protected $dates = ['deleted_at'];
    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    protected $fillable =[
    	'journal_id'
        'parent_id',
		'deviation_id',
		'activity_id',
		'top_most_parent_id',
		'branch_id',
        'patient_id',
		'emp_id',
		'category_id',
		'subcategory_id',
		'type'
		'description',
		'reason_for_editing',
		'edited_by',
        'date',
        'time'
    ];
    
   	public function journal()
    {
          return $this->belongsTo(Journal::class,'journal_id','id');
    }
}
