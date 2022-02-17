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


        \DB::table('email_templates')->truncate();

        $template1 = CompanyType::create(['id' => '1','name' => 'Forgot Password','content'=> '<table style="width: 560px; margin: 0 auto; font-family: \'Inter\', sans-serif;"> <tbody> <tr> <td style="text-align: center; padding:20px;"><a href="#"><img alt="" src="https://aceuss.3mad.in /assets/images/logo11.jpg" /></a></td> </tr> <tr> <td> <table style="box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -webkit-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -moz-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -ms-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -o-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); width: 100%; text-align: center; padding: 20px; margin-bottom: 25px;"> <tbody> <tr> <td> <h2 style="margin:10px 0 0 0;">Reset Aceuss password !</h2> </td> </tr> </tbody> </table> </td> </tr> <tr> <td> <table style="box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -webkit-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -moz-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -ms-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -o-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); width: 100%; padding: 25px; margin-bottom: 25px;"> <tbody> <tr> <td> <p style="font-size: 15px; line-height: 25px; font-family: \'Inter\', sans-serif; margin: 0 0 15px 0;">Dear&nbsp;{{name}},</p> <p style="font-size: 15px; line-height: 25px; font-family: \'Inter\', sans-serif; margin: 0 0 5px 0;">You recently requested to password reset for your Aceuss account. Check below detail to reset it.</p> </td> </tr> <tr> <td style="vertical-align: top;">&nbsp;</td> </tr> <tr> <td>&nbsp;</td> </tr> <tr> <td colspan="2" style="text-align: center; padding-top: 27px;">{{passowrd_link}}</td> </tr> <tr> <td style="font-size: 13px; padding-top: 5px; text-align: center;">If you did not requested a reset password, please ingore this email or report to our support team. This password reset is only valid for the next 30 minutes.</td> </tr> <tr> <td style="text-align: center; padding-top: 15px;"><span style="font-size: 13px; text-align: center;">Happy Tracking,</span> <h6 style="font-size: 15px; padding-top: 5px; text-align: center; margin: 0px; font-weight: 700;">The Aceuss Team</h6> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> <table style="max-width: 528px; margin: 0 auto; font-family: \'Inter\', sans-serif;"> <tbody> <tr> <td style="font-size: 12.99px; color: #9b9b9b; line-height: 22px; text-align: center; padding: 19px 0 20px 0;">TM and copyright &copy; 2020 Aceuss Inc.  All Rights Reserved / <a href="https://aceuss.3mad.in/privacy-policy/" style="color: #74a4ec;">Privacy Policy</a> / <a href="https://aceuss.3mad.in/terms-of-services/" style="color: #74a4ec;">Terms of use</a></td> </tr> </tbody> </table> ']);
        $template2 = CompanyType::create(['id' => '2','name' => 'Welcome Mail','content'=>'<table style="width: 560px; margin: 0 auto; font-family: \'Inter\', sans-serif;">
            <tbody> <tr> <td style="text-align: center; padding:20px;"><a href="#"><img alt="" src="https://aceuss.3mad.in /assets/images/logo11.jpg" /></a></td> </tr> <tr> <td> <table style="box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -webkit-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -moz-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -ms-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -o-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); width: 100%; text-align: center; padding: 20px; margin-bottom: 25px;"> <tbody> <tr> <td> <h2 style="margin:10px 0 0 0;">Welcome to Aceuss </h2> </td> </tr> </tbody> </table> </td> </tr> <tr> <td> <table style="box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -webkit-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -moz-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -ms-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); -o-box-shadow: 0 0 29px 0 rgba(0,0,0,0.1); width: 100%; padding: 25px; margin-bottom: 25px;"> <tbody> <tr> <td> <p style="font-size: 15px; line-height: 25px; font-family: \'Inter\', sans-serif; margin: 0 0 15px 0;">Dear&nbsp;{{name}},</p> <p style="font-size: 15px; line-height: 25px; font-family: \'Inter\', sans-serif; margin: 0 0 5px 0;">You can see your sent data below. </p><br> <p style="font-size: 15px; line-height: 25px; font-family: \'Inter\', sans-serif; margin: 0 0 5px 0;">Note: Please change your password into website/App for your future safety..</p> </td> </tr> <tr> <td style="vertical-align: top;">&nbsp;</td> </tr> <tr> <td> <table class="table" cellspacing="0" border="0" cellpadding="0" width="100%" style="padding: 15px; color: #9b9b9b;"> <tr> <th>Name</th> <th>Email</th> <th>Contact</th> <th>City</th> <th>Zipcode</th> </tr> <tr> <td>{{name}}</td> <td>{{email}}</td> <td>{{contact_number}}</td> <td>{{city}}</td> <td>{{zipcode}}</td> </tr> </table> </td> </tr> <tr> <td>&nbsp;</td> </tr> </tbody> </table> </td> </tr> </tbody> </table> <table style="max-width: 528px; margin: 0 auto; font-family: \'Inter\', sans-serif;"> <tbody> <tr> <td style="font-size: 12.99px; color: #9b9b9b; line-height: 22px; text-align: center; padding: 19px 0 20px 0;">TM and copyright &copy; 2020 Aceuss Inc.  All Rights Reserved / <a href="https://aceuss.3mad.in/privacy-policy/" style="color: #74a4ec;">Privacy Policy</a> / <a href="https://aceuss.3mad.in/terms-of-services/" style="color: #74a4ec;">Terms of use</a></td> </tr> </tbody> </table>']);

    }
}
