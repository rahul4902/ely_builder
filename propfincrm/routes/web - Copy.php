<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//dd(Hash::make(123456));

Route::get('cache', function () {
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('view:clear');
    // $exitCode = Artisan::call('optimize:clear');
    return '<center><h2> All Config Cache Cleared....</h2></center>';
});

Route::get('/', function () {
    Session::flush();
    return redirect('/');
})->name('login');
//Route::auth();
Auth::routes();
Route::get('/logout', 'UsersController@logoutsession');
Route::group(['middleware' => ['auth']], function () {

    /**
     * Main


     */



    Route::get('ajax/{id}', 'AjaxController@state');
    //Route::get('state','AjaxController@state');
    Route::get('/', 'PagesController@dashboard');
    Route::get('dashboard', 'PagesController@dashboard')->name('dashboard');

    /**
     * Users
     */
    Route::group(['prefix' => 'users'], function () {
        //before
        //	  Route::get('/deleteuser','UsersController@destroya')->name('users.delete');
        //after
        Route::get('/deleteuser', 'UsersController@destroy')->name('users.delete');
        Route::any('/data', 'UsersController@anyData')->name('users.data');
        Route::get('/taskdata/{id}', 'UsersController@taskData')->name('users.taskdata');
        Route::get('/leaddata/{id}', 'UsersController@leadData')->name('users.leaddata');
        Route::get('/clientdata/{id}', 'UsersController@clientData')->name('users.clientdata');
    });
    Route::resource('users', 'UsersController');

    /**
     * Roles
     */
    Route::resource('roles', 'RolesController');
    /**
     * Clients
     */
    Route::group(['prefix' => 'clients'], function () {
        Route::get('/data', 'ClientsController@anyData')->name('clients.data');
        Route::post('/create/cvrapi', 'ClientsController@cvrapiStart');
        Route::post('/upload/{id}', 'DocumentsController@upload');
        Route::patch('/updateassign/{id}', 'ClientsController@updateAssign');
    });
    Route::resource('clients', 'ClientsController');
    Route::resource('documents', 'DocumentsController');


    /**
     * Tasks
     */
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/data', 'TasksController@anyData')->name('tasks.data');
        Route::patch('/updatestatus/{id}', 'TasksController@updateStatus');
        Route::patch('/updateassign/{id}', 'TasksController@updateAssign');
        Route::post('/updatetime/{id}', 'TasksController@updateTime');
        Route::post('/invoice/{id}', 'TasksController@invoice');
        Route::post('/comments/{id}', 'CommentController@store');
    });
    Route::resource('tasks', 'TasksController');

    /**
     * Leads
     */
    Route::group(['prefix' => 'leads'], function () {
        Route::get('/data', 'LeadsController@anyData')->name('leads.data');
        Route::get('/nextfollowup/{id}', 'LeadsController@NextFollowUp')->name('leads.nextfollowup');
        Route::get('/GetSeniors', 'LeadsController@GetSeniors')->name('leads.GetSeniors');
        Route::get('/MeetingType', 'LeadsController@MeetingType')->name('leads.MeetingType');
        Route::get('/Leadstatus', 'LeadsController@Leadstatus')->name('leads.Leadstatus');

        Route::get('/StoreMeeting/{id}', 'LeadsController@StoreMeeting')->name('leads.StoreMeeting');



        Route::patch('/updateassign/{id}', 'LeadsController@updateAssign');
        Route::post('/notes/{id}', 'NotesController@store');
        Route::patch('/updatestatus/{id}', 'LeadsController@updateStatus');
        Route::patch('/updatefollowup/{id}', 'LeadsController@updateFollowup')->name('leads.followup');
        Route::group(['prefix' => 'bulkUpload'], function () {
            Route::get('/', 'LeadsController@bulkUploadIndex')->name('leads.bulk.upload.index');
            Route::post('/', 'LeadsController@bulkUploadStore')->name('leads.bulk.upload.store');
            Route::get('/downloadFormat', 'LeadsController@bulkUploadCSVDownloadFormat')->name('downloadFormat');
        });
    });
    Route::resource('leads', 'LeadsController');
    Route::get('dump/leads/{id}', 'LeadsController@dumpShow');


    /**
     * Settings
     */
    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', 'SettingsController@index')->name('settings.index');
        Route::patch('/permissionsUpdate', 'SettingsController@permissionsUpdate');
        Route::patch('/overall', 'SettingsController@updateOverall');
    });

    /**
     * Departments
     */
    Route::resource('departments', 'DepartmentsController');

    /**
     * Integrations
     */
    Route::group(['prefix' => 'integrations'], function () {
        Route::get('Integration/slack', 'IntegrationsController@slack');
    });
    Route::resource('integrations', 'IntegrationsController');

    /**
     * Notifications
     */
    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/getall', 'NotificationsController@getAll')->name('notifications.get');
        Route::post('/markread', 'NotificationsController@markRead');
        Route::get('/markall', 'NotificationsController@markAll');
        Route::get('/{id}', 'NotificationsController@markRead');
    });

    /**
     * Invoices
     */
    Route::group(['prefix' => 'invoices'], function () {
        Route::post('/updatepayment/{id}', 'InvoicesController@updatePayment')->name('invoice.payment.date');
        Route::post('/reopenpayment/{id}', 'InvoicesController@reopenPayment')->name('invoice.payment.reopen');
        Route::post('/sentinvoice/{id}', 'InvoicesController@updateSentStatus')->name('invoice.sent');
        Route::post('/reopensentinvoice/{id}', 'InvoicesController@updateSentReopen')->name('invoice.sent.reopen');
        Route::post('/newitem/{id}', 'InvoicesController@newItem')->name('invoice.new.item');
    });
    Route::resource('invoices', 'InvoicesController');
});

Route::post('multiple_ass_lead', 'LeadsController@multiple_ass_lead')->name('multiple_ass_lead');

Route::any('project_list', 'MasterController@projectList');
Route::any('delete_project', 'MasterController@deleteProject');
Route::any('edit_project', 'MasterController@editProject');
Route::any('save_edit_project', 'MasterController@saveEditProject');
Route::any('save_project_name', 'MasterController@saveProjectName');
Route::any('requirement_list', 'MasterController@requirementList');
Route::any('delete_requirement', 'MasterController@deleteRequirement');
Route::any('edit_requirement', 'MasterController@editRequirement');
Route::any('save_edit_requirement', 'MasterController@saveEditRequirement');
Route::any('save_requirement_name', 'MasterController@saveRequirementName');
Route::any('budget_list', 'MasterController@budgetList');
Route::any('delete_budget_range', 'MasterController@deleteBudgetRange');
Route::any('edit_budget_range', 'MasterController@editBudgetRange');
Route::any('save_edit_budget_range', 'MasterController@saveEditBudgetRange');
Route::any('save_budget_range', 'MasterController@saveBudgetRange');
Route::any('source_list', 'MasterController@sourceList');
Route::any('delete_source', 'MasterController@deleteSource');
Route::any('edit_source', 'MasterController@editSource');
Route::any('save_edit_source', 'MasterController@saveEditSource');
Route::any('save_source_name', 'MasterController@saveSourceName');
Route::any('new_sell_page', 'LeadsController@newSellPage');
Route::any('save_sell_lead', 'LeadsController@saveSellLead');
Route::any('get_lead_data', 'LeadsController@getLeadData');
Route::any('all_sell_list', 'LeadsController@allSellList');
Route::any('get_sell_view', 'LeadsController@getSellView');
Route::any('get_lead', 'LeadsController@getLeadAllData');
Route::get('leadtype/{id?}', 'LeadsController@leadtype')->name('leadtype');
Route::any('get_lead_date_wise', 'LeadsController@getLeadDateWise');
Route::any('delete_lead', 'LeadsController@deleteLead');
Route::any('edit_lead', 'LeadsController@editLead');
Route::any('update_lead', 'LeadsController@updateLead');
Route::any('push', 'LeadsController@pushNotification');
Route::any('push_hour', 'LeadsController@pushNotificationHour');

Route::any('all_meeting_and_visits', 'LeadsController@all_meeting_and_visits');
Route::any('call_logs', 'CallLogController@index');
Route::any('attendance', 'AttendanceController@index');
Route::post('dump_multiple_ass_lead', 'LeadsController@dump_multiple_ass_lead')->name('dump_multiple_ass_lead');
Route::post('send_to_dump_lead', 'LeadsController@send_to_dump_lead')->name('send_to_dump_lead');
Route::get('reverse_leads_update', 'LeadsController@reverseLeadsUpdate');
Route::get('reverse_leads', 'LeadsController@reverseLeads');
Route::get('reverse_visit_leads_update', 'LeadsController@reverseVisitLeadsUpdate');
Route::get('reverse_visit_no_booking_leads_update', 'LeadsController@reverseVisitNoBookingLeadsUpdate');
Route::get('send_team_reminders', 'LeadsController@sendTeamReminders');


Route::get('/get-lead-graph-data', 'LeadsController@getLeadGraphData');

// HR
Route::get('/ta-da', 'HrController@ta_da')->name('ta_da');
Route::get('/leaves', 'HrController@leaves')->name('leaves');

Route::any('all_lead_list', 'LeadsController@getAllLeadList');
Route::any('/lead_list/{type?}', 'LeadsController@index')->name('lead_list')->middleware('auth');
Route::any('getLeadDataAjax', 'LeadsController@getLeadDataAjax');
Route::get('leads/bulk/export','LeadsController@exportDumpLeadsToCSV')->name('leads.bulk.export');