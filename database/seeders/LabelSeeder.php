<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Label;
use App\Models\Group;
use DB;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('labels')->delete();
    	DB::table('groups')->delete();
    	$groupLabels = [
    		"LoginValidation" => [
    			"message_email" => "The email field is required", 
    			"message_email_invalid" => "The email must be a valid email address", 
    			"message_password" => "The password field is required.", 
    			"message_confirm_password" => "Password  and confirm password does not match.", 
    			"message_user_not_found" => "Unable to find user", 
    			"message_account_inactive" => "Your account is temparory inactive please contact to your admin",
    			"message_account_deactive" => "Your account is permanently deactivate please contact to your admin",
    			"message_unable_generate_token" => "Unable to generate token",
    			"message_wrong_password" => "Wrong Password",
    			"message_email_not_exists" => "This email id is not exists",
    			"message_password_reset_link" => "Password Reset link has been sent to your registered email id",
    		], 
    		"PasswordReset" => [
    			"message_token" => "The token field is required", 
    			"message_email" => "The email field is required", 
    			"message_email_invalid" => "The email must be a valid email address", 
    			"message_password" => "The password field is required.", 
    			"message_confirm_password" => "Password  and confirm password does not match.", 
    			"message_success" => "Password reset successfully", 
    		], 
    		"ChangePassword" => [
    			"message_old_password" => "The old password field is required", 
    			"message_new_password" => "The  new password field is required", 
    			"message_new_password_confirm" => "Password  and confirm password does not match", 
    			"message_new_password_confirmation" => "The confirm password field is required.",  
    		], 
    		"UserValidation" => [
    			"message_id" => "User id is required",
    			"message_role_id" => "Please select user role",
    			"message_user_type_id" => "User type is required",
    			"message_company_type_id" => "Company type is required",
    			"message_category_id" => "Category is is required",
    			"message_user_type_id" => "User type is required",
    			"message_name" => "Name is required",
    			"message_email" => "Email address is required",
    			"message_email_invalid" => "Email address is invalid",
    			"message_password" => "Password is required",
    			"message_password_min" => "Password should be 8 character long",
    			"message_contact_number" => "Contact number is required",
    			"message_create" => "User Added Successfully",
    			"message_update" => "User Updated Successfully",
    			"message_delete" => "User Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    		],
    		"Package" => [
    			'message_id' => 'Id is required',
    			'message_name' => 'Name is required',
    			'message_price' => 'Price is required',
    			'message_validity_in_days' => 'Validity in days field is required',
    			'message_number_of_patients' => 'No of patients field is required',
    			'message_number_of_employees' => 'No of employee field is required', 
    			'message_discount_type' => 'The discount type field is required', 
    			'message_discount_value' => 'The discount value field is required', 
    			"message_create" => "Package Added Successfully",
    			"message_update" => "Package Updated Successfully",
    			"message_delete" => "Package Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_package_assigne" => "Package Assigne successfully",
    		], 
    		"Activity" => [
    			'message_id' => 'Id is required',
    			'message_name' => 'Name is required',
    			'message_activity_class_id.required' => 'Activity classification is field is required',
    			'message_category_id.required' => 'Category field is required',
    			'message_title.required' =>  'Title field is required',
    			'message_description.required' =>  'Description is required',
    			'message_activity_type.required' => 'Activity type field is required',
    			'message_activity_type_in.required' => 'Please select Activity type correct option',
    			'message_start_date.required' =>  'Start date field is required',
    			'message_start_time.required' =>  'Start time field is required',
    			'message_end_date.required' =>  'End date must be greather than start date',
    			'message_end_time.required' =>  'End time must be greather than start time',
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_approve" => "Approved Successfully",
    			"message_assigne" => "Assigned Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    			"message_activity_id" => "Activity id field is required",
    			"message_user_id" => "User id field is required",
    			"message_assignment_date" => "Assignment date field is required",
    			"message_assignment_day" => "Assignment day field is required",
    		], 
    		"Module" => [
    			'message_id' => 'Id is required',
    			'message_name' => 'Name is required',
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    			"message_module_assigne" => "Moduel Assigne successfully",
    		],
    		"CompanyType" => [
    			'message_id' => 'Id is required',
    			'message_name' => 'Name is required',
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    		],
    		"CategoryType" => [
    			'message_id' => 'Id is required',
    			'message_name' => 'Name is required',
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    		],
    		"CategoryMaster" => [
    			'message_id' => 'Id is required',
    			'message_category_type_id' => 'Category type Id is required',
    			'message_name' => 'Name is required',
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_parent_id_not_found" => "Parent id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    		],
    		"Salary" => [
    			'message_user_id' => 'User Id is required',
    			'message_salary_per_month' => 'Salary per month field is required',
    			'message_salary_package_start_date' => 'Package Start date  is required',
    			'message_salary_package_end_date' => 'Package End date is required',
    			"message_salary_package_end_date_after" => "Package end date must be grether then package start date",
    			"message_update" => "Salary Updated Successfully",

    		], 
    		"Bank" => [
    			'message_id' => 'Id is required',
    			'message_bank_name' => 'Bank Name is required',
    			'message_account_number' => 'Account Number is required',
    			'message_clearance_number' => 'Clearance Number is required',
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    		],
    		"Department" => [
    			'message_id' => 'Id is required',
    			'message_name' => 'Name is required',
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    		], 
    		"Company" => [
    			'message_id' => 'Id is required',
    			'message_shift_name' => 'Shift Name is required',
    			'message_shift_start_time' => 'Shift start time is required',
    			"message_shift_end_time" => "Shift end time is required",
    			"message_shift_end_time_after" => "Shift End time must be greather than start time",
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_name_already_exists" => "This name Already Exist",
    			'message_user_id' => 'User id is required',
    			'message_shift_id' => 'Shift id is required',
    			'message_shift_start_date' => 'Shift start date is required',
    			"message_shift_end_date" => "Shift end date is required",
    			"message_shift_end_date_after" => "Shift End date must be greather than start date",
    			"message_shift_already_assigne" => "This shift is already assigne to this user",
    			"message_shift_already_assigne_date" => "shift is already assigne to this user on this date.",
    		],
    		"IP" => [
    			'message_id' => 'Id is required',
    			'message_user_id' => 'User id is required',   
    			'message_category_id' => 'Category id is required',   
    			'message_subcategory_id' => 'Subcategory id is required',   
    			'message_what_happened' => 'What happend field is required',   
    			'message_how_it_happened' => 'How is happend field is required',   
    			'message_when_it_started' => 'When it started field is required',   
    			'message_what_to_do' => 'What to do field required',   
    			'message_goal' => 'Goal field is required',   
    			'message_sub_goal' => 'Sub goal field required',   
    			'message_plan_start_date' => 'Plan start date is required',   
    			'message_plan_start_time' => 'Plan start time is required',   
    			'message_remark' => 'Remark field is required', 
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_assigne" => "Ip Assigne successfully",
    			"message_approve" => "Ip Approve successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_user_id" => "User Id is required",
    			"message_ip_id" => "Ip id is required",
    			"message_patient_already_assigne" => "This Patient plan is already assigne to this employee.",
    		], 
    		"FollowUp" => [
    			"message_id" => "Id is required",
    			"message_ip_id" => "Ip id  is required",  
    			"message_title" => "Title field is required",   
    			"message_description" => "Description field is required",   
    			"message_follow_up_type" => "Follow up type is required",   
    			"message_start_date" => "Start date field is required",      
    			"message_start_time" => "Start time  field is required",  
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_approve" => "Approved Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_ip_not_found" => "Ip Not Found",
    		],
    		"Journal" => [
    			"message_id" => "Id is required",
    			'message_activity_id' => 'Activity id field is required',   
    			'message_category_id' => 'Category id field is required',   
    			'message_title' => 'Title field is required',   
    			'message_description' => 'Description field is required', 
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_approve" => "Approved Successfully",
    			"message_id_not_found" => "Id Not Found",
    		],
    		"Deviation" => [
    			"message_id" => "Id is required",  
    			'message_category_id' => 'Category id field is required',   
    			'message_sub_category_id' => 'Subcategory field is required',   
    			'message_description' => 'Description field is required', 
    			'message_date_time' => 'date and time field is required', 
    			'message_immediate_action' => 'Immediate action field is required', 
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_approve" => "Approved Successfully",
    			"message_id_not_found" => "Id Not Found",
    		],
    		"role" => [
    			"message_se_name" => "Se name field is required",  
    			'message_permissions' => 'Permissions id field is required',   
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_role_not_found" => "Role Not Found",
    		],
    		"permission" => [
    			"message_name" => "Name field is required",  
    			"message_name_unique" => "Name field must be unique",  
    			"message_se_name" => "Se name field is required",  
    			"message_se_name_unique" => "Se name field must be unique",  
    			'message_group_name' => 'Group name field is required',   
    			"message_create" => "Added Successfully",
    			"message_update" => "Updated Successfully",
    			"message_delete" => "Deleted Successfully",
    			"message_id_not_found" => "Id Not Found",
    			"message_per_not_found" => "Permission Not Found",
    		],
    	];
    	foreach ($groupLabels as $key => $label) 
    	{
    		if(Group::where('name',$key)->count() > 0)
    		{
    			$group = Group::where('name',$key)->first();
    		}
    		else
    		{
    			$group = new Group;
    			$group->name    = $key;
    			$group->status  = 1;
    			$group->save();
    		}	

    		foreach ($label as $key1 => $value) 
    		{
    			if(Label::where('group_id',$group->id)->where('label_name',$key1)->where('language_id',1)->count() > 0)
    			{
    				$label = Label::where('group_id',$group->id)->where('label_name',$key1)->where('language_id',1)->first();
    			}
    			else
    			{
    				$label = new Label;
    			}
    			$label->group_id         = $group->id;
    			$label->language_id      = 1;
    			$label->label_name       = $key1;
    			$label->label_value      = $value;
    			$label->status           = 1;
    			$label->save(); 
    		}
    	}


    }
}
