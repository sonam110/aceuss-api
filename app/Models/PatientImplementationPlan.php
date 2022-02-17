<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\CategoryMaster;
use App\Traits\TopMostParentId;
use App\Traits\PersonalInfoDuringIp;
class PatientImplementationPlan extends Model
{
    use HasFactory,SoftDeletes,TopMostParentId;
    protected $dates = ['deleted_at'];
    protected $fillable =[
        'top_most_parent_id',
		'user_id',
		'branch_id',
        'parent_id',
		'category_id',
		'subcategory_id',
		'what_happened',
		'how_it_happened',
		'when_it_started',
		'what_to_do',
		'goal',
		'sub_goal',
		'plan_start_date',
		'plan_start_time',
		'remark',
		'activity_message',
		'save_as_template',
		'reason_for_editing',
		'created_by',
		'edited_by',
		'approved_by',
		'approved_date',
        'status',
        'entry_mode',
		
    ];

    public function TopMostParent()
    {
        return $this->belongsTo(User::class,'top_most_parent_id','id');
    }
    
    public function patient()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function Parent()
    {
          return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
         return $this->hasMany(self::class, 'parent_id');
    }
    public function Category()
    {
        return $this->belongsTo(CategoryMaster::class,'category_id','id');
    }
    public function Subcategory()
    {
        return $this->belongsTo(CategoryMaster::class,'subcategory_id','id');
    }
    public function CreatedBy()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function EditedBy()
    {
        return $this->belongsTo(User::class,'edited_by','id');
    }
    public function ApprovedBy()
    {
        return $this->belongsTo(User::class,'approved_by','id');
    }
    public function persons()
    {
         return $this->hasMany(PersonalInfoDuringIp::class,'ip_id','id');
    }
}
