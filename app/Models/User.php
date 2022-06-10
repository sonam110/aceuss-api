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
use App\Models\PatientImplementationPlan;
use App\Models\Activity;
use App\Models\Task;
use App\Models\IpFollowUp;
use App\Models\Followup;
use App\Models\Employee;
use App\Models\Patient;
use App\Models\Journal;
use App\Models\Deviation;
use App\Models\Bookmark;
use App\Models\ActivityAssigne;
use App\Models\AssignTask;
use App\Models\Language;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\TopMostParentId;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles,SoftDeletes,LogsActivity,TopMostParentId;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]

     */
    protected $guard_name = 'api';
    protected $dates = ['deleted_at'];
    protected $appends = ['company_types','patient_types'];
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
        'employee_type',
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
        'step_one',
        'step_two',
        'step_three',
        'step_four',
        'step_five',
        'entry_mode',
        'contact_person_name',
        'language_id',
        'contract_type',
        'contract_value',
        'avatar'
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
        return $this->belongsTo(UserType::class,'user_type_id','id')->withoutGlobalScope('top_most_parent_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class,'language_id','id');
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
        return $this->belongsTo(CategoryMaster::class,'category_id','id')->withoutGlobalScope('top_most_parent_id');
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

    public function branchs()
    {
        return $this->hasMany(User::class,'top_most_parent_id','id')->where('user_type_id', 11);
    }

    public function parentUnit() 
    {
        return $this->belongsTo(User::class,'parent_id', 'id');
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

    public function assignedModule()
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
    public function patientPlan()
    {
        return $this->hasMany(PatientImplementationPlan::class,'user_id','id');
    }

    public function assignedActivity()
    {
        return $this->hasMany(ActivityAssigne::class,'user_id','id');
    }

    public function assignedTask()
    {
        return $this->hasMany(AssignTask::class,'user_id','id');
    }

    public function patientActivity()
    {
        return $this->hasMany(Activity::class,'patient_id','id');
    }


    public function getCompanyTypesAttribute()
    {

        if(is_null($this->company_type_id)== false && is_array(json_decode($this->company_type_id)) && sizeof(json_decode($this->company_type_id)) >0){
            $companyType = CompanyType::select('id','name')->whereIn('id',json_decode($this->company_type_id))->get();
            return (!empty($companyType)) ? $companyType : null;
        }
        

    }
    public function getPatientTypesAttribute()
    {
        if(is_null($this->patient_type_id)== false && is_array(json_decode($this->patient_type_id)) && sizeof(json_decode($this->patient_type_id)) >0){
            $patientTYpe = EmployeeType::select('id','designation')->whereIn('id',json_decode($this->patient_type_id))->get();
            return (!empty($patientTYpe)) ? $patientTYpe : null;
        }
        

    }


    //--------------------------created by khushboo--------------------------//

    public function tasks()
    {
         return $this->hasMany(Task::class,'top_most_parent_id','id');
    }

    public function patientTasks()
    {
         return $this->hasMany(Task::class,'resource_id','id')->where('type_id', 7);
    }

    public function assignedTasks()
    {
         return $this->hasMany(Task::class,'resource_id','id')->where('type_id', 7);
    }

    public function activities()
    {
         return $this->hasMany(Activity::class,'top_most_parent_id','id');
    }

    public function ips()
    {
         return $this->hasMany(PatientImplementationPlan::class,'top_most_parent_id','id');
    }

    public function followUps()
    {
         return $this->hasMany(IpFollowUp::class,'top_most_parent_id','id');
    }

    public function patients()
    {
         return $this->hasMany(User::class,'top_most_parent_id','id')->where('user_type_id',6);
    }

    public function employees()
    {
         return $this->hasMany(User::class,'top_most_parent_id','id')->where('user_type_id',3);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class,'patient_id','id');
    }

    public function deviations()
    {
        return $this->hasMany(Deviation::class,'patient_id','id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class,'user_id','id');
    }

    public function allChildBranches(){
        return $this->hasMany(self::class, 'branch_id', 'id')->whereIn('user_type_id', [2,11])->with('allChildBranches');
    }

    public function childs()
    {
         return $this->hasMany(User::class,'top_most_parent_id','id');
    }


    
}
