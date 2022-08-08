<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;
    // protected $appends = ['company_logo'];
    protected $fillable = ['extra_hour_rate','ob_hour_rate','company_name','company_logo','company_email','company_contact','company_address','contact_person_name','contact_person_email','contact_person_phone','company_website','follow_up_reminder','before_minute','relaxation_time'];

    // public function getCompanyLogoAttribute()
    // {
    //     return ENV('APP_URL').'/'.'public/'.$this->company_logo;
    // }
}
