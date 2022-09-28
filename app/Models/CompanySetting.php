<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySetting extends Model
{
    use HasFactory, SoftDeletes;
    
    // protected $appends = ['company_logo'];
    protected $fillable = ['user_id','company_name','company_logo','company_email','company_contact','company_address','contact_person_name','contact_person_email','contact_person_phone','company_website','follow_up_reminder','before_minute','relaxation_time','extra_hour_rate','ob_hour_rate'];

    public function companyInfo()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    // public function getCompanyLogoAttribute()
    // {
    //     return ENV('APP_URL').'/'.'public/'.$this->company_logo;
    // }
}
