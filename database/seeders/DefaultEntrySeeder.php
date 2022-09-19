<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\CompanyType;
use App\Models\CategoryType;
use App\Models\EmailTemplate;
use App\Models\Language;
use App\Models\Package;
use App\Models\ActivityOption;
use App\Models\Module;
use App\Models\UserType;
use App\Models\User;
use App\Models\EmployeeType;
use App\Models\BookmarkMaster;

class DefaultEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create([
            'title'          => 'English',
            'value'      => 'en',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
        Language::create([
            'title'          => 'Swedish',
            'value'      => 'sw',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        Package::create([
            'name'                  => 'Basic pack',
            'price'                 =>'540',
            'is_on_offer'           =>'1',
            'discount_type'         =>'1',
            'discount_value'        =>'67',
            'discounted_price'      =>'178.2',
            'validity_in_days'      =>'100',
            'number_of_patients'    =>'100',
            'number_of_employees'   =>'50',
            'status'                =>'1',
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s')
        ]);


        ActivityOption::truncate();
        $ActivityOption1 = ActivityOption::create(['id' => '1','option'=>'Efforts managed with staff on time']);
        $ActivityOption1 = ActivityOption::create(['id' => '2','option'=>'Efforts managed with staff not on time','is_journal' => '1','is_deviation'=>'0']);
        $ActivityOption2 = ActivityOption::create(['id' => '3','option'=>'Could fix himself','is_journal' => '1','is_deviation'=>'0']);
        $ActivityOption3 = ActivityOption::create(['id' => '4','option'=>'The customer did not want','is_journal' => '1','is_deviation'=>'0']);
        $ActivityOption4 = ActivityOption::create(['id' => '5','option'=>'Staff could not','is_journal' => '1','is_deviation'=>'1']);


        Module::create(['id' => '1','name'=>'Activity']);
        Module::create(['id' => '2','name'=>'Journal']);
        Module::create(['id' => '3','name'=>'Deviation']);
        Module::create(['id' => '4','name'=>'Schedule']);
        Module::create(['id' => '5','name'=>'Stampling']);

        $UserType1 = UserType::create(['id' => '1','name' => 'Super Admin']);
        $UserType2 = UserType::create(['id' => '2','name' => 'Company']);
        $UserType3 = UserType::create(['id' => '3','name' => 'Employee']);
        $UserType4 = UserType::create(['id' => '4','name' => 'Hospital']);
        $UserType5 = UserType::create(['id' => '5','name' => 'Nurse']);
        $UserType6 = UserType::create(['id' => '6','name' => 'Patient']);
        $UserType7 = UserType::create(['id' => '7','name' => 'careTaker']);
        $UserType8 = UserType::create(['id' => '8','name' => 'FamilyMember']);
        $UserType9 = UserType::create(['id' => '9','name' => 'ContactPerson']);
        $UserType10 = UserType::create(['id' => '10','name' => 'careTakerFamily']);
        $UserType11 = UserType::create(['id' => '11','name' => 'Branch']);
        $UserType12 = UserType::create(['id' => '12','name' => 'Guardian']);
        $UserType13 = UserType::create(['id' => '13','name' => 'Presented']);
        $UserType14 = UserType::create(['id' => '14','name' => 'Participated']);
        $UserType15 = UserType::create(['id' => '15','name' => 'Other']);
        $UserType15 = UserType::create(['id' => '16','name' => 'Admin Employee']);
   

        \DB::table('employee_types')->truncate();
        $patientType1 = EmployeeType::create(['id' => '1','type'=>'patient','designation' => 'Minor Child']);
        $patientType2 = EmployeeType::create(['id' => '2','type'=>'patient','designation' => 'Student']);
        $patientType3 = EmployeeType::create(['id' => '3','type'=>'patient','designation' => 'Working']);
        $patientType4 = EmployeeType::create(['id' => '4','type'=>'patient','designation' => 'Old age']);
        $patientType5 = EmployeeType::create(['id' => '4','type'=>'patient','designation' => 'Not Working']);

        //Bookmarks
        BookmarkMaster::create([
            'target'    => 'dashboard',
            'title'     => 'Analytics Dashboard',
            'icon'      => 'Home',
            'link'      => '/dashboard/analytics'
        ]);

    }
}
