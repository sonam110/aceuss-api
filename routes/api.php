<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('Api\V1')->group(function () {
	/*------------Common Route----------------------------*/
	Route::post('login', 'Common\UserLoginController@login')->name('login');	
	Route::post('forgot-password', 'Common\UserLoginController@forgetPassword');
	Route::get('authentication/reset-password/{token}', 'Common\UserLoginController@resetPassword')->name('password.reset');
	Route::post('verify-otp', 'Common\UserLoginController@verifyOtp');
	Route::post('password-reset', 'Common\UserLoginController@passwordReset');
	Route::post('password-reset-in-mobile', 'Common\UserLoginController@passwordResetInMobile');
	Route::post('country-list', 'Common\NoMiddlewareController@countryList');
	Route::post('agency-list', 'Common\NoMiddlewareController@agencyList');

	Route::post('category-types', [App\Http\Controllers\Api\V1\User\CategoryTypeController::class, 'categoryTypes']);
		
	Route::post('email-templates', [App\Http\Controllers\Api\V1\Admin\EmailTemplateControlle::class, 'emailTemplates']);
	Route::apiResource('email-template',Admin\EmailTemplateControlle::class)->only(['show', 'update']);

	Route::middleware('auth:api', 'isActiveToken','isAuthorized')->group(function () {
		Route::post('/logout', 'Common\UserLoginController@logout');
		Route::post('/change-password', 'Common\UserLoginController@changePassword');
		Route::get('user-detail', 'Common\UserLoginController@userDetail');
		Route::get('user-type-list', 'Common\UserLoginController@userTypeList');
		Route::post('verify-user-email', 'Common\UserLoginController@verifyUserEmail');
		
			/*-------------Bank detail------------------------*/
		Route::post('banks', [App\Http\Controllers\Api\V1\Common\BankDetailController::class, 'banks']);
		Route::apiResource('bank', Common\BankDetailController::class)->only(['store','destroy','show', 'update']);
		/*-------------Update salary detail------------------------*/
		Route::post('update-salary-detail', 'Common\SalaryController@updateSalaryDetail');
		Route::post('salary-detail', 'Common\SalaryController@salaryDetail');

		/*----------Roles------------------------------*/
		Route::post('roles', [App\Http\Controllers\Api\V1\Common\RoleController::class, 'roles']);
		Route::apiResource('role',Common\RoleController::class)->only(['store','destroy','show', 'update']);

		Route::post('permission-list', 'Common\CommonController@permissionList');
		Route::post('get-user-list', 'Common\CommonController@getUserList');
		Route::post('patient-types', 'Common\CommonController@pateintTypes');

		/*-----Admin Route---------------------------*/
		Route::group(['prefix' => 'administration', 'middleware' => ['admin', 'throttle:120,1']],function () {
			/*------------Permissions--------------*/
			Route::post('permissions', [App\Http\Controllers\Api\V1\Admin\PermissionController::class, 'permissions']);
			Route::apiResource('permission',Admin\PermissionController::class)->only(['store','destroy','show', 'update']);
			 /*------------Create Company--------------------*/
			Route::post('companies', [App\Http\Controllers\Api\V1\Admin\CompanyAccountController::class, 'companies']);
		    Route::apiResource('user', Admin\CompanyAccountController::class)->only(['store','destroy','show', 'update']);
			/*-------------Packages------------------------*/
			Route::post('packages', [App\Http\Controllers\Api\V1\Admin\PackageController::class, 'packages']);
			Route::post('restore-package', [App\Http\Controllers\Api\V1\Admin\PackageController::class, 'restorePackage']);
			Route::apiResource('package',Admin\PackageController::class)->only(['store','destroy','show', 'update']);
			/*-------------activity Classification------------------------*/
			Route::post('activitycls', [App\Http\Controllers\Api\V1\Admin\ActivityClsController::class, 'activitycls']);
			Route::apiResource('activity-cls',Admin\ActivityClsController::class)->only(['store','destroy','show', 'update']);
			/*-------------Module------------------------*/
			Route::post('modules', [App\Http\Controllers\Api\V1\Admin\ModuleController::class, 'modules']);
			Route::apiResource('module',Admin\ModuleController::class)->only(['store','destroy','show', 'update']);

			Route::post('assigne-package', 'Admin\ModuleController@assigenPackage');
	     	Route::post('assigne-module', 'Admin\ModuleController@assigenModule');
			

		});


		Route::post('file-uploads', 'Common\FileUploadController@uploadFiles');

		Route::post('words', [App\Http\Controllers\Api\V1\Common\WordController::class, 'words']);
		Route::apiResource('word',Common\WordController::class)->only(['store','destroy', 'update']);

		Route::post('paragraphs', [App\Http\Controllers\Api\V1\Common\ParagraphController::class, 'paragraphs']);
		Route::apiResource('paragraph',Common\ParagraphController::class)->only(['store','destroy', 'update']);

		Route::post('tasks', [App\Http\Controllers\Api\V1\Common\TaskController::class, 'tasks']);
		Route::apiResource('task',Common\TaskController::class)->only(['store','destroy','show', 'update']);

		Route::post('emergency-contacts', [App\Http\Controllers\Api\V1\Common\EmergencyContactController::class, 'emergencyContact']);
		Route::apiResource('emergency-contact',Common\EmergencyContactController::class)->only(['store','destroy', 'update']);



		/*-------User route-----------------------------------------*/
		
		/*-------------Company Type------------------------*/
		Route::post('company-types', [App\Http\Controllers\Api\V1\User\CompanyTypeController::class, 'companyTypes']);
		Route::apiResource('company-type', User\CompanyTypeController::class)->only(['store','destroy','show', 'update']);
		/*-------------Category Type------------------------*/
		
		Route::apiResource('category-type', User\CategoryTypeController::class)->only(['store','destroy','show', 'update']);

		/*-------------Category Master------------------------*/
		Route::post('category-parent-list', 'User\CategoryMasterController@categoryParentList');
		Route::post('category-child-list', 'User\CategoryMasterController@categoryChildList');
		Route::post('categories', [App\Http\Controllers\Api\V1\User\CategoryMasterController::class, 'categories']);
		Route::apiResource('category', User\CategoryMasterController::class)->only(['store','destroy','show', 'update']);

		/*-------------Department ------------------------*/
		Route::post('departments', [App\Http\Controllers\Api\V1\User\DepartmentController::class, 'departments']);
		    Route::apiResource('department', User\DepartmentController::class)->only(['store','destroy','show', 'update']);
		 /*-------------User Managment ------------------------*/
	   	Route::post('users', [App\Http\Controllers\Api\V1\User\UserController::class, 'users']);
		Route::apiResource('user', User\UserController::class)->only(['store','destroy','show', 'update']);

		/*-------------Branch -------------------*/
		Route::apiResource('branch', User\BranchController::class)->only(['store','update']);
		/*-------------work shift ------------------------*/
		Route::post('workshifts', [App\Http\Controllers\Api\V1\User\CompanyController::class, 'workshifts']);
		    Route::apiResource('work-shift', User\CompanyController::class)->only(['store','destroy','show', 'update']);

		Route::get('employee-list','User\CompanyController@employeeList');
		Route::post('shift-assigne-to-employee','User\CompanyController@shiftAssigneToEmployee');
		Route::post('view-assigne-shift','User\CompanyController@viewshiftAssigne');

		/*-------------Patient implementation plan ------------------------*/
		Route::post('ips', [App\Http\Controllers\Api\V1\User\PatientController::class, 'ipsList']);
		Route::apiResource('ip', User\PatientController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-patient-plan','User\PatientController@approvedPatientPlan');
		Route::post('patient-person-list','User\PatientController@patientPersonList');
		Route::post('ip-template-list','User\PatientController@ipTemplateList');
		Route::post('ip-followups-print/{ip_id}', 'User\PatientController@ipFollowupsPrint');


		Route::post('ip-assigne-to-employee','User\PatientController@ipAssigneToEmployee');
		Route::post('view-ip-assigne','User\PatientController@viewIpAssigne');
		Route::post('ip-edit-history','User\PatientController@ipEditHistory');

		/*-------------Ip Follow ups ------------------------*/
		Route::post('followups', [App\Http\Controllers\Api\V1\User\FollowUpsController::class, 'followups']);
		Route::apiResource('follow-up', User\FollowUpsController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-ip-follow-up','User\FollowUpsController@approvedIpFollowUp');
		Route::post('follow-up-complete','User\FollowUpsController@followUpComplete');
		Route::post('followup-edit-history','User\FollowUpsController@followupEditHistory');

		/*-------------activity ------------------------*/
		Route::post('activities', [App\Http\Controllers\Api\V1\User\ActivityController::class, 'activities']);
		Route::apiResource('activity', User\ActivityController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-activity','User\ActivityController@approvedActivity');

		Route::post('activity_assignments','User\ActivityController@activityAssignments');
		Route::post('activity-edit-history','User\ActivityController@activityEditHistory');

		Route::post('activity-action','User\ActivityController@activityAction');
		/*-------------Journal ------------------------*/
		Route::post('journals', [App\Http\Controllers\Api\V1\User\JournalController::class, 'journals']);
		Route::apiResource('journal', User\JournalController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-journal','User\JournalController@approvedJournal');
		/*-------------Deviations ------------------------*/
		Route::post('deviations', [App\Http\Controllers\Api\V1\User\JournalController::class, 'deviations']);
		Route::apiResource('deviation', User\JournalController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-deviation','User\deviationController@approveDeviation');

		/*-------------Folder------------------------*/
		Route::post('folders', [App\Http\Controllers\Api\V1\User\FolderController::class, 'folders']);
		Route::apiResource('folder', User\FolderController::class)->only(['store','destroy','show', 'update']);
		Route::post('folder-parent-list', 'User\FolderController@folderParentList');
		/*-------------File ------------------------*/
		Route::post('files', [App\Http\Controllers\Api\V1\User\FileController::class, 'files']);
		Route::apiResource('file', User\FileController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-file','User\FileController@approvedFile');

		/*-------------Questions ------------------------*/
		Route::post('questions', [App\Http\Controllers\Api\V1\User\QuestionController::class, 'questions']);
		Route::apiResource('question', User\QuestionController::class)->only(['store','destroy','show', 'update']);
	


	});
});
