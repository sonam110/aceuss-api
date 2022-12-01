<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TopMostParentId;
use App\Models\UserType;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;

class AdminFile extends Model
{
    use HasFactory, TopMostParentId, LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $appends = ['assign_to_company_info'];
    
    protected $fillable = [
        'top_most_parent_id','title','file_path','is_public','created_by','user_type_id','company_ids','file_size'
    ];

    public function UserType()
    {
        return $this->belongsTo(UserType::class,'user_type_id','id');
    }

    public function getAssignToCompanyInfoAttribute()
    {
        $companyIds = $this->company_ids;
        $assigned = false;
        if(!empty($companyIds) && sizeof(json_decode($companyIds, true))>0)
        {
            $json_decode = json_decode($companyIds, true);
            if($json_decode[0]=='all')
            {
                $assigned = User::select('id', 'name', 'branch_name')
                    ->where('user_type_id', 2)->get();
            }
            else
            {
                $assigned = User::select('id', 'name', 'branch_name')
                    ->whereIn('id', $json_decode)->get();
            }
        }
        return $assigned;
    }
}
