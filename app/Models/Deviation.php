<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Activity;
use App\Models\Journal;
use App\Models\CategoryMaster;
use App\Traits\TopMostParentId;
class Deviation extends Model
{
    use HasFactory,TopMostParentId,SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable =[
        'parent_id',
		'journal_id',
		'activity_id',
		'top_most_parent_id';,
		'branch_id',
        'patient_id',
		'emp_id',
		'category_id',
		'subcategory_id',
		'title',
		'description',
		'status',
		'not_a_deviation',
		'reason_of_not_being_deviation',
		'reason_for_editing',
		'edited_by',
		'approved_by',
		'approved_date',
        'entry_mode',

    ];
    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
    }

    public function Journal()
    {
        return $this->belongsTo(Journal::class,'journal_id','id');
    }
    public function Activity()
    {
        return $this->belongsTo(Activity::class,'activity_id','id');
    }
    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
     public function Patient()
    {
        return $this->belongsTo(User::class,'patient_id','id');
    }
    public function Employee()
    {
        return $this->belongsTo(User::class,'emp_id','id');
    }
    public function Category()
    {
        return $this->belongsTo(CategoryMaster::class,'category_id','id');
    }
    public function Subcategory()
    {
        return $this->belongsTo(CategoryMaster::class,'subcategory_id','id');
    }
     public function EditedBy()
    {
        return $this->belongsTo(User::class,'edited_by','id');
    }
    public function ApprovedBy()
    {
        return $this->belongsTo(User::class,'approved_by','id');
    }
}
