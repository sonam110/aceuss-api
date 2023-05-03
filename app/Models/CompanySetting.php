<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class CompanySetting extends Model
{
    use HasFactory, SoftDeletes;
    
    // protected $appends = ['company_logo'];
    protected $fillable = ['user_id','company_name','company_logo','company_email','company_contact','company_address','contact_person_name','contact_person_email','contact_person_phone','company_website','follow_up_reminder','before_minute','relaxation_time','extra_hour_rate','ob_hour_rate'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function companyInfo()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    // public function getCompanyLogoAttribute()
    // {
    //     return ENV('APP_URL').'/'.'public/'.$this->company_logo;
    // }
}
