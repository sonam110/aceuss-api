<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\CompanyType;
use App\Models\CategoryType;
use App\Models\EmailTemplate;
class DefaultEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('company_types')->truncate();
        $companyType1 = CompanyType::create(['id' => '1','created_by'=>'1','name' => 'Group Living']);
        $companyType2 = CompanyType::create(['id' => '2','created_by'=>'1','name' => 'Home Living']);
        $companyType3 = CompanyType::create(['id' => '3','created_by'=>'1','name' => 'Single Living']);

        \DB::table('category_types')->truncate();
        $cataegoryType1 = CategoryType::create(['id' => '1','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Activity']);
        $cataegoryType2 = CategoryType::create(['id' => '2','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Implementation Plan']);
        $cataegoryType3 = CategoryType::create(['id' => '3','created_by'=>'1','top_most_parent_id'=>'1','name' => 'User']);
        $cataegoryType4 = CategoryType::create(['id' => '4','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Deviation']);
        $cataegoryType5 = CategoryType::create(['id' => '5','created_by'=>'1','top_most_parent_id'=>'1','name' => 'FollowUps']);
        $cataegoryType6 = CategoryType::create(['id' => '6','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Journal']);
        $cataegoryType7 = CategoryType::create(['id' => '7','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Patient']);
        $cataegoryType8 = CategoryType::create(['id' => '8','created_by'=>'1','top_most_parent_id'=>'1','name' => 'Employee']);

    }
}
