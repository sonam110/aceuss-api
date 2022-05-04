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
	          "email" => "The email field is required", 
	          "email_invalid" => "The email must be a valid email address", 
	          "password" => "The password field is required.", 
	          "confirm_password" => "Password  and confirm password does not match.", 
	          "user_not_found" => "Unable to find user", 
	          "account_inactive" => "Your account is temparory inactive please contact to your admin",
	          "account_deactive" => "Your account is permanently deactivate please contact to your admin",
	          "unable_generate_token" => "Unable to generate token",
	          "wrong_password" => "Wrong Password",
	          "email_not_exists" => "This email id is not exists",
	          "password_reset_link" => "Password Reset link has been sent to your registered email id",
	        ], 
	        "PasswordReset" => [
	          "token" => "The token field is required", 
	          "email" => "The email field is required", 
	          "email_invalid" => "The email must be a valid email address", 
	          "password" => "The password field is required.", 
	          "confirm_password" => "Password  and confirm password does not match.", 
	          "success" => "Password reset successfully", 
	        ], 
	        "ChangePassword" => [
	          "old_password" => "The old password field is required", 
	          "new_password" => "The  new password field is required", 
	          "new_password_confirm" => "Password  and confirm password does not match", 
	          "new_password_confirmation" => "The confirm password field is required.",  
	        ], 
	     "UserValidation" => [
	     "id" => "User id is required",
	     "role_id" => "Please select user role",
            "user_type_id" => "User type is required",
            "company_type_id" => "Company type is required",
            "category_id" => "Category is is required",
            "user_type_id" => "User type is required",
            "name" => "Name is required",
            "email" => "Email address is required",
            "email_invalid" => "Email address is invalid",
            "password" => "Password is required",
            "password_min" => "Password should be 8 character long",
            "contact_number" => "Contact number is required",
            "create" => "User Added Successfully",
            "update" => "User Updated Successfully",
            "delete" => "User Deleted Successfully",
            "id_not_found" => "Id Not Found",
	        ],
	        "Package" => [
	        'id' => 'Id is required',
	        'name' => 'Name is required',
            'price' => 'Price is required',
            'validity_in_days' => 'Validity in days field is required',
            'number_of_patients' => 'No of patients field is required',
            'number_of_employees' => 'No of employee field is required', 
            'discount_type' => 'The discount type field is required', 
            'discount_value' => 'The discount value field is required', 
            "create" => "Package Added Successfully",
            "update" => "Package Updated Successfully",
            "delete" => "Package Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "package_assigne" => "Package Assigne successfully",
	        ], 
	        "Activity" => [
	        'id' => 'Id is required',
	        'name' => 'Name is required',
            'activity_class_id.required' => 'Activity classification is field is required',
            'category_id.required' => 'Category field is required',
            'title.required' =>  'Title field is required',
            'description.required' =>  'Description is required',
            'activity_type.required' => 'Activity type field is required',
            'activity_type_in.required' => 'Please select Activity type correct option',
            'start_date.required' =>  'Start date field is required',
            'start_time.required' =>  'Start time field is required',
            'end_date.required' =>  'End date must be greather than start date',
            'end_time.required' =>  'End time must be greather than start time',
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "approve" => "Approved Successfully",
            "assigne" => "Assigned Successfully",
            "id_not_found" => "Id Not Found",
            "name_already_exists" => "This name Already Exist",
            "activity_id" => "Activity id field is required",
            "user_id" => "User id field is required",
            "assignment_date" => "Assignment date field is required",
            "assignment_day" => "Assignment day field is required",
	        ], 
	        "Module" => [
	        'id' => 'Id is required',
	        'name' => 'Name is required',
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "name_already_exists" => "This name Already Exist",
            "module_assigne" => "Moduel Assigne successfully",
	        ],
	        "CompanyType" => [
	        'id' => 'Id is required',
	        'name' => 'Name is required',
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "name_already_exists" => "This name Already Exist",
	        ],
	        "CategoryType" => [
	        'id' => 'Id is required',
	        'name' => 'Name is required',
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "name_already_exists" => "This name Already Exist",
	        ],
	        "CategoryMaster" => [
	        'id' => 'Id is required',
	        'category_type_id' => 'Category type Id is required',
	        'name' => 'Name is required',
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "parent_id_not_found" => "Parent id Not Found",
            "name_already_exists" => "This name Already Exist",
	        ],
	        "Salary" => [
	        'user_id' => 'User Id is required',
	        'salary_per_month' => 'Salary per month field is required',
	        'salary_package_start_date' => 'Package Start date  is required',
	        'salary_package_end_date' => 'Package End date is required',
            "salary_package_end_date_after" => "Package end date must be grether then package start date",
            "update" => "Salary Updated Successfully",
 
	        ], 
	        "Bank" => [
	        'id' => 'Id is required',
	        'bank_name' => 'Bank Name is required',
	        'account_number' => 'Account Number is required',
	        'clearance_number' => 'Clearance Number is required',
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "name_already_exists" => "This name Already Exist",
	        ],
	        "Department" => [
	        'id' => 'Id is required',
	        'name' => 'Name is required',
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "name_already_exists" => "This name Already Exist",
	        ], 
	        "Company" => [
	        'id' => 'Id is required',
	        'shift_name' => 'Shift Name is required',
	        'shift_start_time' => 'Shift start time is required',
            "shift_end_time" => "Shift end time is required",
            "shift_end_time_after" => "Shift End time must be greather than start time",
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "name_already_exists" => "This name Already Exist",
            'user_id' => 'User id is required',
            'shift_id' => 'Shift id is required',
	        'shift_start_date' => 'Shift start date is required',
            "shift_end_date" => "Shift end date is required",
            "shift_end_date_after" => "Shift End date must be greather than start date",
            "shift_already_assigne" => "This shift is already assigne to this user",
            "shift_already_assigne_date" => "shift is already assigne to this user on this date.",
	        ],
	        "IP" => [
	        'id' => 'Id is required',
	        'user_id' => 'User id is required',   
            'category_id' => 'Category id is required',   
            'subcategory_id' => 'Subcategory id is required',   
            'what_happened' => 'What happend field is required',   
            'how_it_happened' => 'How is happend field is required',   
            'when_it_started' => 'When it started field is required',   
            'what_to_do' => 'What to do field required',   
            'goal' => 'Goal field is required',   
            'sub_goal' => 'Sub goal field required',   
            'plan_start_date' => 'Plan start date is required',   
            'plan_start_time' => 'Plan start time is required',   
            'remark' => 'Remark field is required', 
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "assigne" => "Ip Assigne successfully",
            "approve" => "Ip Approve successfully",
            "id_not_found" => "Id Not Found",
            "user_id" => "User Id is required",
            "ip_id" => "Ip id is required",
            "patient_already_assigne" => "This Patient plan is already assigne to this employee.",
	        ], 
            "FollowUp" => [
            "id" => "Id is required",
            "ip_id" => "Ip id  is required",  
            "title" => "Title field is required",   
            "description" => "Description field is required",   
            "follow_up_type" => "Follow up type is required",   
            "start_date" => "Start date field is required",      
            "start_time" => "Start time  field is required",  
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "approve" => "Approved Successfully",
            "id_not_found" => "Id Not Found",
            "ip_not_found" => "Ip Not Found",
            ],
            "Journal" => [
            "id" => "Id is required",
            'activity_id' => 'Activity id field is required',   
            'category_id' => 'Category id field is required',   
            'title' => 'Title field is required',   
            'description' => 'Description field is required', 
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "approve" => "Approved Successfully",
            "id_not_found" => "Id Not Found",
            ],
            "Deviation" => [
            "id" => "Id is required",  
            'category_id' => 'Category id field is required',   
            'sub_category_id' => 'Subcategory field is required',   
            'description' => 'Description field is required', 
            'date_time' => 'date and time field is required', 
            'immediate_action' => 'Immediate action field is required', 
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "approve" => "Approved Successfully",
            "id_not_found" => "Id Not Found",
            ],
            "role" => [
            "se_name" => "Se name field is required",  
            'permissions' => 'Permissions id field is required',   
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "role_not_found" => "Role Not Found",
            ],
            "permission" => [
            "name" => "Name field is required",  
            "name_unique" => "Name field must be unique",  
            "se_name" => "Se name field is required",  
            "se_name_unique" => "Se name field must be unique",  
            'group_name' => 'Group name field is required',   
            "create" => "Added Successfully",
            "update" => "Updated Successfully",
            "delete" => "Deleted Successfully",
            "id_not_found" => "Id Not Found",
            "per_not_found" => "Permission Not Found",
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
