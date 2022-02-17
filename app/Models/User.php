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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]

     */
    protected $guard_name = 'api';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'user_type_id',
        'role_id',
        'company_type_id',
        'category_id',
        'top_most_parent_id',
        'parent_id',
        'dept_id',
        'govt_id',
        'branch_id',
        'weekly_hours_alloted_by_govt',
        'name',
        'email',
        'email_verified_at',
        'password',
        'contact_number',
        'gender',
        'personal_number',
        'organization_number',
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
        'establishment_date',
        'user_color',
        'disease_description',
        'created_by',
        'password_token',
        'is_file_required',
        'status',
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
    public function parentUnit() 
    {
        return $this->belongsTo(User::class,'parent_id', 'id');
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


    
}
