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
	Route::post('user-type-permission', 'Common\NoMiddlewareController@userTypePermission');

	Route::get('activity-options', 'Common\NoMiddlewareController@activityOptions');

	Route::post('admin-files', 'Common\AdminFileController@adminFiles');


	Route::post('category-types', [App\Http\Controllers\Api\V1\User\CategoryTypeController::class, 'categoryTypes']);
	Route::get('company-setting/{user_id}', 'Common\NoMiddlewareController@companySettingDetail');
	Route::get('user-type-list', 'Common\UserLoginController@userTypeList');
	Route::post('verify-user-email', 'Common\UserLoginController@verifyUserEmail');
		
	Route::post('password-change', 'Common\NoMiddlewareController@passwordChange'); 
	Route::middleware('auth:api', 'isActiveToken','isAuthorized')->group(function () {
		Route::post('/logout', 'Common\UserLoginController@logout');

		Route::get('dashboard', 'Common\DashboardController@dashboard');

		Route::post('/change-password', 'Common\UserLoginController@changePassword');
		Route::get('user-detail', 'Common\UserLoginController@userDetail');
	
		Route::post('patient-password-change', 'Common\CommonController@patientPasswordChange');

		/*-----Request For approval------------------*/

		Route::post('request-for-approval', 'Common\RequestApprovalController@requestForApproval');
		Route::post('approval-request-list', 'Common\RequestApprovalController@approvalRequestList');
		Route::post('approval-request/{id}', 'Common\RequestApprovalController@approvedRequest');
		Route::post('reject-request', 'Common\RequestApprovalController@rejectRequest');

		
		
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

	     	 
	     	Route::post('email-templates', [App\Http\Controllers\Api\V1\Admin\EmailTemplateControlle::class, 'emailTemplates']);
			Route::apiResource('email-template',Admin\EmailTemplateControlle::class)->only(['store','destroy','show', 'update']);

			Route::apiResource('admin-file',Common\AdminFileController::class)->only(['destroy']);

			Route::get('sms-callback', [App\Http\Controllers\Api\V1\Common\CommonController::class, 'smsCallback'])->name('sms-callback');
			Route::get('test-message-send', [App\Http\Controllers\Api\V1\Common\CommonController::class, 'testMessageSend'])->name('test-message-send');

			//Logs
		    Route::post('mobile-bank-id-log', [App\Http\Controllers\Api\V1\Admin\LogController::class, 'mobileBankIdLog']);
		    Route::post('sms-log', [App\Http\Controllers\Api\V1\Admin\LogController::class, 'smsLog']);
		    Route::post('activities-log', [App\Http\Controllers\Api\V1\Admin\LogController::class, 'activitiesLog']);
		    Route::get('activities-log-info/{activity_id}', [App\Http\Controllers\Api\V1\Admin\LogController::class, 'activityLogInfo']);
		   
		});

		Route::post('file-access-log', 'Common\AdminFileController@fileAccessLog');
		Route::post('company-files', 'Common\AdminFileController@companyFiles');
		Route::apiResource('company-file-delete',Common\AdminFileController::class)->only(['destroy']);

		Route::post('update-profile', 'Common\ProfileController@updateProfile');
		Route::post('setting-update', 'User\SettingController@settingUpdate');

		Route::post('file-uploads', 'Common\FileUploadController@uploadFiles');

		Route::post('words', [App\Http\Controllers\Api\V1\Common\WordController::class, 'words']);
		Route::apiResource('word',Common\WordController::class)->only(['store','destroy', 'update']);

		Route::post('paragraphs', [App\Http\Controllers\Api\V1\Common\ParagraphController::class, 'paragraphs']);
		Route::apiResource('paragraph',Common\ParagraphController::class)->only(['store','destroy', 'update']);

		Route::post('tasks', [App\Http\Controllers\Api\V1\Common\TaskController::class, 'tasks']);
		Route::apiResource('task',Common\TaskController::class)->only(['store','destroy','show', 'update']);
		Route::post('task-edit-history','Common\TaskController@taskEditHistory');
		Route::post('task-action','Common\TaskController@taskAction');

		/*----------------Calander---------------------------*/
		Route::post('calander-task', [App\Http\Controllers\Api\V1\Common\TaskController::class, 'calanderTask']);


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
		Route::post('delete-person/{id}','User\PatientController@deletePerson');
		Route::post('ip-template-list','User\PatientController@ipTemplateList');
		Route::post('ip-followups-print/{ip_id}', 'User\PatientController@ipFollowupsPrint');
		Route::post('ip-action','User\PatientController@ipAction');

		/*--------------Person add-------------------------*/
		Route::post('patient-person-list','Common\PersonController@patientPersonList');
		Route::apiResource('person', Common\PersonController::class)->only(['store','destroy','show', 'update']);


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

		Route::post('activity-multi-delete', [App\Http\Controllers\Api\V1\User\ActivityController::class, 'activityMultiDelete']);

		Route::post('activity_assignments','User\ActivityController@activityAssignments');
		Route::post('activity-edit-history','User\ActivityController@activityEditHistory');

		Route::post('activity-action','User\ActivityController@activityAction');

		Route::post('activity-tag','User\ActivityController@activityTag');
		Route::post('activity-not-applicable','User\ActivityController@activityNotApplicable');

		Route::post('trashed-activites', [App\Http\Controllers\Api\V1\User\TrashedActivityController::class, 'trashedActivites']);
		Route::delete('trashed-activites-permanent-delete/{id}', [App\Http\Controllers\Api\V1\User\TrashedActivityController::class, 'destroy']);
		Route::get('trashed-activites-restore/{id}', [App\Http\Controllers\Api\V1\User\TrashedActivityController::class, 'restore']);


		/*-------------Journal ------------------------*/
		Route::post('journals', [App\Http\Controllers\Api\V1\User\JournalController::class, 'journals']);
		Route::apiResource('journal', User\JournalController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-journal','User\JournalController@approvedJournal');
		/*-------------Journal Action------------------------*/
		Route::post('journal-actions', [App\Http\Controllers\Api\V1\User\JournalActionController::class, 'journalActions']);
		Route::apiResource('journal-action', User\JournalActionController::class)->only(['store','destroy','show', 'update']);
		/*-------------Deviations ------------------------*/
		Route::post('deviations', [App\Http\Controllers\Api\V1\User\DeviationController::class, 'deviations']);
		Route::apiResource('deviation', User\DeviationController::class)->only(['store','destroy','show', 'update']);
		Route::post('action-deviation','User\DeviationController@actionDeviation');

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

		/*-------Post Comment-----------------------------*/


		Route::post('comment', [App\Http\Controllers\Api\V1\Common\CommentController::class, 'comment']);
		Route::post('comment-list', [App\Http\Controllers\Api\V1\Common\CommentController::class, 'commentList']);


		/*import*/
		Route::post('patient-import', [App\Http\Controllers\Api\V1\Common\ImportDataController::class, 'patientImport']);
		Route::get('download-patient-import-sample-file', [App\Http\Controllers\Api\V1\Common\ImportDataController::class, 'downloadPatientImportSampleFile']);
		

		//get permissions and relation by user_type_id
		Route::post('all-permissions', [App\Http\Controllers\Api\V1\Common\NoMiddlewareController::class, 'allPermissions']);
		Route::post('add-user-type-has-permissions', [App\Http\Controllers\Api\V1\Common\NoMiddlewareController::class, 'addUserTypeHasPermissions']);

	});
});
