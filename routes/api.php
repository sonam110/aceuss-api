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
		Route::post('activity-count', 'Common\DashboardController@activityCount');

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

		Route::post('company-subscription-extend', [App\Http\Controllers\Api\V1\User\CompanyController::class, 'companySubscriptionExtend']);

		/*-----Admin Route---------------------------*/
		Route::group(['prefix' => 'administration', 'middleware' => ['admin', 'throttle:120,1']],function () {
			/*------------Permissions--------------*/
			Route::post('permissions', [App\Http\Controllers\Api\V1\Admin\PermissionController::class, 'permissions']);
			Route::apiResource('permission',Admin\PermissionController::class)->only(['store','destroy','show', 'update']);
			 /*------------Create Company--------------------*/
			Route::post('companies', [App\Http\Controllers\Api\V1\Admin\CompanyAccountController::class, 'companies']);
		    Route::apiResource('user', Admin\CompanyAccountController::class)->only(['store','destroy','show', 'update']);
		    Route::post('company-stats/{id}', [App\Http\Controllers\Api\V1\Admin\CompanyAccountController::class, 'companyStats']);

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

			/*-------------Bookmark Master------------------------*/
			Route::post('bookmark-masters', [App\Http\Controllers\Api\V1\Admin\BookmarkMasterController::class, 'bookmarkMasters']);
			Route::apiResource('bookmark-master',Admin\BookmarkMasterController::class)->only(['store','destroy','show', 'update']);


			/*-------------Group------------------------*/
			Route::post('groups', [App\Http\Controllers\Api\V1\Admin\GroupController::class, 'groups']);
			Route::apiResource('group',Admin\GroupController::class)->only(['store','destroy','show', 'update']);

			/*-------------Label------------------------*/
			Route::post('labels', [App\Http\Controllers\Api\V1\Admin\LabelController::class, 'labels']);
			Route::apiResource('label',Admin\LabelController::class)->only(['store','destroy','show', 'update']);
			Route::post('/labels-import', [App\Http\Controllers\Api\V1\Admin\LabelController::class, 'labelsImport']);
			Route::post('/labels-export', [App\Http\Controllers\Api\V1\Admin\LabelController::class, 'labelsExport']);

			/*----------------------Language------------------*/
			Route::apiResource('language',Admin\LanguageController::class)->only(['store','destroy','show', 'update','index']);


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
		    
	     	Route::post('licence-keys', [App\Http\Controllers\Api\V1\Admin\ManageLicenceController::class, 'index']);
			Route::apiResource('licence-key',Admin\ManageLicenceController::class)->only(['store','destroy','show', 'update']);
			Route::post('assign-licence-key/{id}', [App\Http\Controllers\Api\V1\Admin\ManageLicenceController::class, 'assignLicenceKey']);
			Route::post('cancel-licence-key/{user_id}', [App\Http\Controllers\Api\V1\Admin\ManageLicenceController::class, 'cancelLicenceKey']);

		});

		Route::get('change-language/{language_id}', [App\Http\Controllers\Api\V1\Common\UserLoginController::class, 'changeLanguage']);

		//messages
    	Route::post('get-users', [App\Http\Controllers\Api\V1\Common\MessagingController::class, 'getUsers']);
    	Route::post('get-users-with-latest-message', [App\Http\Controllers\Api\V1\Common\MessagingController::class, 'getUsersWithLatestMessage']);
    	Route::post('get-messages', [App\Http\Controllers\Api\V1\Common\MessagingController::class, 'getMessages']);

		//Notification
		Route::post('/notifications',[App\Http\Controllers\Api\V1\Common\NotificationController::class,'index']);
		Route::apiResource('/notification', 'Common\NotificationController')->only('store','destroy','show');
		Route::get('/notification/{id}/read', [App\Http\Controllers\Api\V1\Common\NotificationController::class,'read']);
		Route::get('/user-notification-read-all', [App\Http\Controllers\Api\V1\Common\NotificationController::class,'userNotificationReadAll']);
		Route::get('/user-notification-delete', [App\Http\Controllers\Api\V1\Common\NotificationController::class,'userNotificationDelete']);
		Route::post('/notification-check', [App\Http\Controllers\Api\V1\Common\NotificationController::class,'notificationCheck']);
		Route::get('/unread-notification-count', [App\Http\Controllers\Api\V1\Common\NotificationController::class,'unreadNotificationsCount']);


		Route::post('file-access-log', 'Common\AdminFileController@fileAccessLog');
		Route::get('file-access-history', 'Common\AdminFileController@fileAccessHistory');
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
		Route::post('user-email-update', [App\Http\Controllers\Api\V1\User\UserController::class, 'emailUpdate']);

	   	Route::get('get-licence-status', [App\Http\Controllers\Api\V1\User\UserController::class, 'getLicenceStatus']);

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
		Route::post('get-tmw-wise-activity-report','User\StatisticsActivityController@getTWMwiseActivityReport');
		Route::post('get-ip-goal-subgoal-report','User\StatisticsActivityController@getIPGoalSubgoalReport');

		Route::post('trashed-activites', [App\Http\Controllers\Api\V1\User\TrashedActivityController::class, 'trashedActivites']);
		Route::delete('trashed-activites-permanent-delete/{id}', [App\Http\Controllers\Api\V1\User\TrashedActivityController::class, 'destroy']);
		Route::get('trashed-activites-restore/{id}', [App\Http\Controllers\Api\V1\User\TrashedActivityController::class, 'restore']);


		/*-------------Journal ------------------------*/
		Route::post('journals', [App\Http\Controllers\Api\V1\User\JournalController::class, 'journals']);
		Route::apiResource('journal', User\JournalController::class)->only(['store','destroy','show', 'update']);
		Route::post('approved-journal','User\JournalController@approvedJournal');
		Route::post('action-journal','User\JournalController@actionJournal');
		Route::post('is-active-journal','User\JournalController@isActiveJournal');
		Route::post('statistics-journal','User\StatisticsJournalController@statisticsJournal');
		Route::post('get-twm-wise-journal-report','User\StatisticsJournalController@getTWMwiseJournalReport');
		Route::post('print-journal','User\JournalController@printJournal');

		/*-------------Journal Action------------------------*/
		Route::post('journal-actions', [App\Http\Controllers\Api\V1\User\JournalActionController::class, 'journalActions']);
		Route::apiResource('journal-action', User\JournalActionController::class)->only(['store','destroy','show', 'update']);
		Route::post('action-journal-action','User\JournalActionController@actionJournalAction');

		/*-------------Deviations ------------------------*/
		Route::post('deviations', [App\Http\Controllers\Api\V1\User\DeviationController::class, 'deviations']);
		Route::apiResource('deviation', User\DeviationController::class)->only(['store','destroy','show', 'update']);
		Route::post('action-deviation','User\DeviationController@actionDeviation');
		Route::get('print-deviation/{deviation_id}','User\DeviationController@printDeviation');

		Route::post('statistics-deviation','User\StatisticsDeviationController@statisticsDeviation');
		Route::post('get-twm-wise-deviation','User\StatisticsDeviationController@getTWMwiseReport');
		Route::post('get-month-wise-deviation','User\StatisticsDeviationController@getMonthWiseReport');

		/*------------- Patient Cashier -------------------*/
		Route::post('patient-cashiers', [App\Http\Controllers\Api\V1\User\PatientCashierController::class, 'patientCashiers']);
		Route::apiResource('patient-cashier', User\PatientCashierController::class)->only(['store']);
		Route::post('patient-cashiers-export', [App\Http\Controllers\Api\V1\User\PatientCashierController::class, 'patientCashiersExport']);
		

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

		Route::post('bookmarks', [App\Http\Controllers\Api\V1\User\BookmarkController::class, 'Bookmarks']);
		Route::apiResource('bookmark', User\BookmarkController::class)->only(['store','destroy','show', 'update']);

		/*-------------OVHour------------------------*/
			Route::post('ovhours', [App\Http\Controllers\Api\V1\User\OVHourController::class, 'ovhours']);
			Route::apiResource('ovhour',User\OVHourController::class)->only(['store','destroy','show', 'update']);
			Route::post('obe-hours-import', [App\Http\Controllers\Api\V1\User\OVHourController::class, 'obeHoursImport']);

		//-----------------------Schedule---------------------//
		Route::post('schedules', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'schedules']);
		Route::post('schedules-copy', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'schedulesCopy']);
		Route::apiResource('schedule', User\ScheduleController::class)->only(['store','destroy','show', 'update']);
		Route::get('user-schedules/{id}', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'getUserSchedules']);

		Route::post('schedule-clones', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'scheduleClones']);
		Route::post('schedule-reports', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'scheduleReports']);
		Route::post('schedules-dates', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'schedulesDates']);
		Route::post('schedule-filter', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'scheduleFilter']);
		Route::post('schedule-stats', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'scheduleStats']);
		Route::post('patient-completed-hours', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'patientCompletedHours']);
		Route::post('schedule-approve', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'scheduleApprove']);
		Route::post('schedule-verify', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'scheduleVerify']);
		Route::post('employee-datewise-work', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'employeeDatewiseWork']);
		Route::post('employee-working-hours-export', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'employeeWorkingHoursExport']);
		Route::post('patient-assigned-hours-export', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'patientAssignedHoursExport']);

		Route::post('get-schedules-data', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'getSchedulesData']);
		Route::post('get-patients-data', [App\Http\Controllers\Api\V1\User\ScheduleController::class, 'getPatientsData']);

		//-----------------------Leave---------------------//
		Route::post('leaves', [App\Http\Controllers\Api\V1\User\LeaveController::class, 'leaves']);
		Route::apiResource('leave', User\LeaveController::class)->only(['store','destroy','show', 'update']);
		Route::get('user-leaves/{id}', [App\Http\Controllers\Api\V1\User\LeaveController::class, 'getUserLeaves']);
		Route::post('leaves-approve', [App\Http\Controllers\Api\V1\User\LeaveController::class, 'leavesApprove']);
		Route::get('leaves-approve-by-group-id/{group_id}', [App\Http\Controllers\Api\V1\User\LeaveController::class, 'leavesApproveByGroupId']);
		Route::post('leave-schedule-slot-selected', [App\Http\Controllers\Api\V1\User\LeaveController::class, 'leaveScheduleSlotSelected']);
		Route::post('company-leave', [App\Http\Controllers\Api\V1\User\LeaveController::class, 'companyLeave']);
		Route::get('company-leaves', [App\Http\Controllers\Api\V1\User\LeaveController::class, 'getCompanyLeaves']);

		//-----------------------Stampling---------------------//
		Route::post('stamplings', [App\Http\Controllers\Api\V1\User\StamplingController::class, 'stamplings']);
		Route::apiResource('stampling', User\StamplingController::class)->only(['store','destroy','show', 'update']);
		Route::get('stamp-in-data', [App\Http\Controllers\Api\V1\User\StamplingController::class, 'stampInData']);
		Route::post('stampling-reports', [App\Http\Controllers\Api\V1\User\StamplingController::class, 'stamplingReports']);
		Route::post('stampling-datewise-reports', [App\Http\Controllers\Api\V1\User\StamplingController::class, 'stamplingDatewiseReports']);

		//-----------------------Emp Assi Working Hour---------------------//
		Route::post('employee-assigned-working-hours', [App\Http\Controllers\Api\V1\User\EmployeeAssignedWorkingHourController::class, 'employeeAssignedWorkingHours']);
		Route::apiResource('employee-assigned-working-hour', User\EmployeeAssignedWorkingHourController::class)->only(['store','destroy','show', 'update']);

		//-----------------------ScheduleMaster---------------------//
		Route::post('schedule-templates', [App\Http\Controllers\Api\V1\User\ScheduleTemplateController::class, 'scheduleTemplates']);
		Route::apiResource('schedule-template', User\ScheduleTemplateController::class)->only(['store','destroy','show', 'update']);
		Route::post('schedule-template-change-status/{id}', [App\Http\Controllers\Api\V1\User\ScheduleTemplateController::class, 'changeStatus']);

		//----------------Module-Request-------------------------//
		Route::post('module-requests', [App\Http\Controllers\Api\V1\User\ModuleRequestController::class, 'moduleRequests']);
		Route::apiResource('module-request', User\ModuleRequestController::class)->only(['store','destroy','show', 'update']);
		Route::post('module-request-status/{id}', [App\Http\Controllers\Api\V1\User\ModuleRequestController::class, 'changeStatus']);
		
	});

	Route::post('get-labels', 'Common\NoAuthController@getLabels');
	Route::post('labels-import', 'Common\NoAuthController@labelsImport');
	Route::get('get-label-by-language-id/{id}', 'Common\NoAuthController@getLabelByLanguageId');
	Route::get('get-languages', 'Common\NoAuthController@getLanguages');
	Route::get('get-email-templates', 'Common\NoAuthController@getEmailTemplates');
	Route::post('get-modules', 'Common\NoAuthController@getModules');

	
});
