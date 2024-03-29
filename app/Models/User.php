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
use App\Models\EmployeeAssignedWorkingHour;
use App\Models\AssignTask;
use App\Models\Language;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\TopMostParentId;
use App\Models\Stampling;
use App\Models\Schedule;
use App\Models\PatientEmployee;
use App\Models\EmployeeBranch;
use mervick\aesEverywhere\AES256;
use DateTimeInterface;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles,SoftDeletes,LogsActivity,TopMostParentId;

    protected $guard_name = 'api';
    protected $dates = ['deleted_at'];
    protected $appends = ['company_types','patient_types','on_vacation', 'branch_patient_number', 'branch_employee_number','personCount'];
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
        'branch_name',
        'branch_email',
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
        'licence_key',
        'licence_end_date',
        'licence_status',
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
        'avatar',
        'schedule_start_date',
        'report_verify',
        'verification_method',
        'contact_person_number',
        'is_family_member',
        'is_caretaker',
        'is_contact_person',
        'is_guardian',
        'is_other',
        'is_other_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;
    
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function getBranchPatientNumberAttribute()
    {
        if(in_array($this->user_type_id, [1,11]))
        {
            return User::where('branch_id', $this->id)->where('user_type_id', 6)->count();
        }
        return 0;
    }
    
    public function getBranchEmployeeNumberAttribute()
    {
        if(in_array($this->user_type_id, [1,11]))
        {
            return User::where('branch_id', $this->id)->where('user_type_id', 3)->count();
        }
        return 0;
    }

    public function getPersonCountAttribute()
    {
         return User::where('parent_id', $this->id)->count();
    }
    
    public function getNameAttribute($value)
    {
        if(env('ENC_DEC', false))
        {
            return (!empty($value)) ? AES256::encrypt($value, env('ENCRYPTION_KEY')) : NULL;
        }
        return $value;
    }

    public function getEmailAttribute($value)
    {
        if(env('ENC_DEC', false))
        {
            return (!empty($value)) ? AES256::encrypt($value, env('ENCRYPTION_KEY')) : NULL;
        }
        return $value;
    }

    public function getContactNumberAttribute($value)
    {
        if(env('ENC_DEC', false))
        {
            return (!empty($value)) ? AES256::encrypt($value, env('ENCRYPTION_KEY')) : NULL;
        }
        return $value;
    }

    public function getPersonalNumberAttribute($value)
    {
        if(env('ENC_DEC', false))
        {
            return (!empty($value)) ? AES256::encrypt($value, env('ENCRYPTION_KEY')) : NULL;
        }
        return $value;
    }

    public function getFullAddressAttribute($value)
    {
        if(env('ENC_DEC', false))
        {
            return (!empty($value)) ? AES256::encrypt($value, env('ENCRYPTION_KEY')) : NULL;
        }
        return $value;
    }
    
    public function companyinfo()
    {
        return $this->belongsTo(self::class, 'top_most_parent_id', 'id')->withoutGlobalScope('top_most_parent_id');
    }
    public function companySetting()
    {
        return $this->belongsTo(CompanySetting::class,'id','user_id')->withTrashed();
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
        return $this->belongsTo(CategoryMaster::class,'category_id','id')->withoutGlobalScope('top_most_parent_id')->withTrashed();
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

    public function agencyHours()
    {
         return $this->hasMany(AgencyWeeklyHour::class,'user_id','id');
    }

    public function persons()
    {
         return $this->hasMany(self::class,'parent_id','id');
    }

    public function personsWithIp()
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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class,'user_id','id');
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

    public function assignedActivities()
    {
        return $this->hasMany(ActivityAssigne::class,'user_id','id');
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

    public function allTrashedChildBranches(){
        return $this->hasMany(self::class, 'branch_id', 'id')->whereIn('user_type_id', [2,11])->onlyTrashed()->with('allTrashedChildBranches');
    }

    public function childs()
    {
         return $this->hasMany(User::class,'top_most_parent_id','id');
    }

    public function assignedWork()
    {
        return $this->hasOne(EmployeeAssignedWorkingHour::class,'emp_id','id')->orderBy('id','desc');
    }

    // public function onVacations()
    // {
    //      return $this->hasMany(Schedule::class,'user_id','id')->where('leave_applied',1)->where('shift_date',date('Y-m-d'));
    // }

    public function getOnVacationAttribute()
    {
        $data = Schedule::where('user_id',$this->id)->where('leave_applied',1)->where('shift_date',date('Y-m-d'));
        if($data->count() > 0)
        {
            return true;
        }
        return false;
    }

    public function leaves()
    {
        return $this->hasMany(Schedule::class,'user_id','id')->where('leave_type','leave');
    }

    public function vacations()
    {
        return $this->hasMany(Schedule::class,'user_id','id')->where('leave_type','vacation');
    }

    public function schedules()
    {
         return $this->hasMany(Schedule::class,'user_id','id');
    }

    public function stamplings()
    {
         return $this->hasMany(Stampling::class,'top_most_parent_id','id');
    }

    public function patientEmployees()
    {
        return $this->hasMany(PatientEmployee::class,'patient_id','id');
    }

    public function employeePatients()
    {
        return $this->hasMany(PatientEmployee::class,'employee_id','id');
    }

    public function branchEmployees()
    {
        return $this->hasMany(EmployeeBranch::class,'branch_id','id');
    }

    public function employeeBranches()
    {
        return $this->hasMany(EmployeeBranch::class,'employee_id','id');
    }

}
