<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserType;
use App\Models\CompanyType;
use App\Models\CategoryMaster;
use App\Models\User;
use App\Models\Department;
use App\Models\BankDetail;
use App\Models\SalaryDetail;
use App\Models\ShiftAssigne;
use App\Models\Subscription;
use App\Models\Message;
use App\Models\Country;
use App\Models\AssigneModule;
use App\Models\AgencyWeeklyHour;
use App\Models\EmployeeType;
use App\Models\PersonalInfoDuringIp;
use App\Models\CompanySetting;
use App\Models\PatientInformation;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles,SoftDeletes,LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]

     */
    protected $guard_name = 'api';
    protected $dates = ['deleted_at'];
    protected $appends = ['company_types'];
    protected $fillable = [
        'unique_id',
        'custom_unique_id',
        'user_type_id',
        'role_id',
        'company_type_id',
        'category_id',
        'top_most_parent_id',
        'parent_id',
        'dept_id',
        'govt_id',
        'branch_id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'contact_number',
        'gender',
        'personal_number',
        'organization_number',
        'patient_type_id',
        'country_id',
        'city',
        'postal_area',
        'zipcode',
        'full_address',
        'license_key',
        'license_end_date',
        'license_status',
        'is_substitute',
        'is_regular',
        'is_seasonal',
        'joining_date',
        'establishment_year',
        'user_color',
        'disease_description',
        'created_by',
        'password_token',
        'is_file_required',
        'is_secret',
        'status',
        'is_fake',
        'is_password_change',
        'documents',
        'entry_mode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    public function companyinfo()
    {
        return $this->belongsTo(self::class, 'top_most_parent_id', 'id')->withoutGlobalScope('top_most_parent_id');
    }
     public function companySetting()
    {
        return $this->belongsTo(CompanySetting::class,'user_id','id');
    }
    
    public function UserType()
    {
        return $this->belongsTo(UserType::class,'user_type_id','id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class,'role_id','id');
    }
    public function Country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }
    public function CompanyType()
    {
        return $this->belongsTo(CompanyType::class,'company_type_id','id');
    }
    public function CategoryMaster()
    {
        return $this->belongsTo(CategoryMaster::class,'category_id','id');
    }
    public function TopMostParent()
    {
        return $this->belongsTo(self::class,'top_most_parent_id');
    }
    
    public function Parent()
    {
        return $this->belongsTo(self::class,'parent_id','id');
    }
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function grandchildren()
    {
        return $this->children()->with('grandchildren');
    }
    public function branch()
    {
        return $this->belongsTo(self::class,'branch_id','id');
    }
    public function parentUnit() 
    {
        return $this->belongsTo(User::class,'parent_id', 'id');
    }
    public function PatientType()
    {
        return $this->hasMany(EmployeeType::class,'id','patient_type_id');
    }

    public function PatientInformation()
    {
        return $this->belongsTo(PatientInformation::class,'id','patient_id');
    }

    public function getLatestParent()
    {
        if ($this->parentUnit)
            return $this->parentUnit->getLatestParent();

        return $this;
    }

     public function modules()
    {
         return $this->hasMany(AssigneModule::class,'user_id','id');
    }

    public function weeklyHours()
    {
         return $this->hasMany(AgencyWeeklyHour::class,'user_id','id');
    }

     public function persons()
    {
         return $this->hasMany(PersonalInfoDuringIp::class,'patient_id','id');
    }

    public function messages()
    {
    return $this->hasMany(Message::class);
    }
    
    public function Department()
    {
        return $this->belongsTo(Department::class,'dept_id','id');
    }
    public function Subscription()
    {
        return $this->belongsTo(Subscription::class,'id','user_id');
    }
    public function BankDetail()
    {
        return $this->belongsTo(BankDetail::class,'id','user_id');
    }
    public function SalaryDetail()
    {
        return $this->belongsTo(SalaryDetail::class,'id','user_id');
    }
    public function CompanyWorkShift()
    {
        return $this->belongsTo(CompanyWorkShift::class,'id','user_id');
    }
    public function ShiftAssigne()
    {
        return $this->belongsTo(ShiftAssigne::class,'id','user_id');
    }

    public function branchParent()
    {
        return $this->belongsTo(self::class,'branch_id','id');
    }

     public function branchChildren()
    {
        return $this->hasMany(self::class, 'branch_id');
    }

    public function getCompanyTypesAttribute()
    {
        if(is_null($this->company_type_id)== false){
            $companyType = CompanyType::select('id','name')->whereIn('id',json_decode($this->company_type_id))->get();
            return (!empty($companyType)) ? $companyType : null;
        }
        

    }


    
}
