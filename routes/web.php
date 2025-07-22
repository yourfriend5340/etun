<?php
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SchedulesController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\OrganizeController;
use App\Http\controllers\TableController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {return view('auth/login');});
//Route::resource('/web',WebController::class);
//Route::get('/footer', function () {return view('footer');});

//Auth::routes();

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/showresult', [App\Http\Controllers\HomeController::class, 'show'])->name('showresult');

Route::get('/qrcode', [App\Http\Controllers\QrcodeController::class, 'index'])->name('qrcode');
Route::get('/qrcode_show', [App\Http\Controllers\QrcodeController::class, 'show'])->name('qrcode_show');
Route::get('/qrcode_asc/{id}', [App\Http\Controllers\QrcodeController::class, 'show_result_asc'])->name('qrcode_asc');
Route::get('/qrcode_desc/{id}', [App\Http\Controllers\QrcodeController::class, 'show_result_desc'])->name('qrcode_desc');
Route::get('/qrcode/delete/{Delete_id}', [App\Http\Controllers\QrcodeController::class, 'delete'])->name('qrcode.delete');
Route::get('/qrcode/request/{Request_id}', [App\Http\Controllers\QrcodeController::class, 'request'])->name('qrcode.request');
Route::get('/qrcode/setprint', [App\Http\Controllers\QrcodeController::class, 'setprint'])->name('qrcode.setprint');
Route::get('/qrcode/print_asc/{Print_id}', [App\Http\Controllers\QrcodeController::class, 'print_asc'])->name('qrcode.print_asc');
Route::get('/qrcode/print_desc/{Print_id}', [App\Http\Controllers\QrcodeController::class, 'print_desc'])->name('qrcode.print_desc');
Route::POST('/qrcode/print', [App\Http\Controllers\QrcodeController::class, 'print'])->name('qrcode.print');
Route::POST('/qrcode/update', [App\Http\Controllers\QrcodeController::class, 'update'])->name('qrcode.update');
Route::POST('/qrcode/store',[App\http\Controllers\QrcodeController::class,'store'])->name('qrcode.store');

Route::get('/customer', [App\Http\Controllers\CustomersController::class, 'show'])->name('customer');
Route::get('/customer/delete/{Delete_id}', [App\Http\Controllers\CustomersController::class, 'destroy'])->name('customer.delete');
Route::get('/customer/request/{Request_id}', [App\Http\Controllers\CustomersController::class, 'request'])->name('customer.request');
Route::POST('/customer/update', [App\Http\Controllers\CustomersController::class, 'update'])->name('customer.update');
Route::POST('/customer/store',[App\http\Controllers\CustomersController::class,'store'])->name('customer.store');
Route::get('/customer_desc', [App\Http\Controllers\CustomersController::class, 'show_result_desc'])->name('customer_desc');
Route::get('/customer_asc', [App\Http\Controllers\CustomersController::class, 'show_result_asc'])->name('customer_asc');
Route::get('/customer/requestName/{Request_name}', [App\Http\Controllers\CustomersController::class, 'requestName'])->name('customer.requestName');

Route::get('/custome_group', [App\Http\Controllers\CustomersController::class, 'group_show'])->name('customer_group');
Route::get('/customer_group/delete/{Delete_id}', [App\Http\Controllers\CustomersController::class, 'group_destroy'])->name('customer_group.delete');
Route::get('/customer_group/request/{Request_id}', [App\Http\Controllers\CustomersController::class, 'group_request'])->name('customer_group.request');
Route::POST('/customer_group/update', [App\Http\Controllers\CustomersController::class, 'group_update'])->name('customer_group.update');
Route::POST('/customer_group/store',[App\http\Controllers\CustomersController::class,'group_store'])->name('customer_group.store');
Route::get('/customer_group_asc', [App\Http\Controllers\CustomersController::class, 'group_show_result_asc'])->name('customer_group_asc');


Route::get('/customer_active', [App\Http\Controllers\CustomersController::class, 'active_show'])->name('customer.active');
Route::get('/customer_active/delete/{Delete_id}', [App\Http\Controllers\CustomersController::class, 'active_destroy'])->name('customer_active.delete');
Route::get('/customer_active/request/{Request_id}', [App\Http\Controllers\CustomersController::class, 'active_request'])->name('customer_active.request');
Route::POST('/customer_active/update', [App\Http\Controllers\CustomersController::class, 'active_update'])->name('customer_active.update');
Route::POST('/customer_active/store',[App\http\Controllers\CustomersController::class,'active_store'])->name('customer_active.store');
Route::get('/customer_active_asc', [App\Http\Controllers\CustomersController::class, 'active_show_result_asc'])->name('customer_active_asc');


Route::get('/user/delete/{Delete_id}', [App\Http\Controllers\Auth\UserController::class, 'destroy'])->name('user.delete');
Route::get('/user_desc', [App\Http\Controllers\Auth\UserController::class, 'show_result_desc'])->name('user_desc');
Route::get('/user_asc', [App\Http\Controllers\Auth\UserController::class, 'show_result_asc'])->name('user_asc');

Route::middleware('auth')->group(function () {
Route::get('/patrol_record', [App\Http\Controllers\PatrolRecordController::class, 'index'])->name('patrol_record');
Route::get('/patrol_record_desc', [App\Http\Controllers\PatrolRecordController::class, 'show_result_desc'])->name('patrol_record_desc');
Route::get('/patrol_record_asc', [App\Http\Controllers\PatrolRecordController::class, 'show_result_asc'])->name('patrol_record_asc');
Route::get('/patrol_record/request/name={search_id}&start_time={start_time}&end_time={end_time}&upload_time={upload_time}',
            [App\Http\Controllers\PatrolRecordController::class,'show'])->name('patrol_record.request');
Route::get('/patrol_record/request_desc/name={search_id}&start_time={start_time}&end_time={end_time}&upload_time={upload_time}',
            [App\Http\Controllers\PatrolRecordController::class,'show_desc'])->name('patrol_record.request_desc');
Route::POST('patrol_record/import',[App\Http\Controllers\PatrolRecordController::class,'import'])->name('patrol_record.import');
Route::POST('/patrol_record/export',[App\Http\Controllers\PatrolRecordController::class,'export'])->name('patrol_record.export');
});

Route::get('/employee', [App\Http\Controllers\EmployeeController::class, 'show'])->name('employee');
Route::get('/employee_desc', [App\Http\Controllers\EmployeeController::class, 'show_result_desc'])->name('employee_desc');
Route::get('/employee_asc', [App\Http\Controllers\EmployeeController::class, 'show_result_asc'])->name('employee_asc');
Route::get('/employee/delete/{Delete_id}', [App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employee.delete');
Route::get('/employee/request/{Request_id}', [App\Http\Controllers\EmployeeController::class, 'request'])->name('employee.request');
Route::get('/employee/requestName/{Request_name}', [App\Http\Controllers\EmployeeController::class, 'requestName'])->name('employee.requestName');
Route::POST('employee/store',[App\Http\Controllers\EmployeeController::class,'store'])->name('employee.store');
Route::POST('/employee/update', [App\Http\Controllers\EmployeeController::class, 'update'])->name('employee.update');

Route::get('/announcement', [App\Http\Controllers\AnnouncementsController::class, 'index'])->name('announcement');//->middleware('can:group_admin');
Route::get('/announcement_desc', [App\Http\Controllers\AnnouncementsController::class, 'show_result_desc'])->name('announcement_desc');
Route::POST('/announcement/store',[App\Http\Controllers\AnnouncementsController::class,'store'])->name('announcement.store');
Route::get('/announcement/top/{id}',[App\Http\Controllers\AnnouncementsController::class,'top'])->name('announcement.top');
Route::get('/announcement/request/{Request_id}', [App\Http\Controllers\AnnouncementsController::class, 'request'])->name('announcement.request');
Route::get('/announcement/delete/{Delete_id}', [App\Http\Controllers\AnnouncementsController::class, 'destroy'])->name('announcement.delete');
Route::POST('/announcement/update', [App\Http\Controllers\AnnouncementsController::class, 'update'])->name('announcement.update');

Route::get('/contactgroup', [App\Http\Controllers\ContactController::class, 'groupindex'])->name('contact.group');
Route::get('/contactgroup_asc', [App\Http\Controllers\ContactController::class, 'groupshow'])->name('contact_group.asc');
Route::POST('/contactgroup/store', [App\Http\Controllers\ContactController::class, 'groupstore'])->name('contact_group.store');
Route::get('/contactgroup/delete/{Delete_id}', [App\Http\Controllers\ContactController::class, 'groupdestroy'])->name('contact_group.delete');
Route::get('/contactgroup/request/{Request_id}', [App\Http\Controllers\ContactController::class, 'grouprequest'])->name('contact_group.request');
Route::POST('/contactgroup/update', [App\Http\Controllers\ContactController::class, 'groupupdate'])->name('contact_group.update');

Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::get('/contact_asc', [App\Http\Controllers\ContactController::class, 'show'])->name('contact.asc');
Route::POST('/contact/store', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/delete/{Delete_id}', [App\Http\Controllers\ContactController::class, 'destroy'])->name('contact.delete');
Route::get('/contact/request/{Request_id}', [App\Http\Controllers\ContactController::class, 'request'])->name('contact.request');
Route::POST('/contact/update', [App\Http\Controllers\ContactController::class, 'update'])->name('contact.update');

Route::get('/clocksalary', [App\Http\Controllers\ClockSalaryController::class, 'index'])->name('clocksalary');
Route::get('/clocksalary/{id}', [App\Http\Controllers\ClockSalaryController::class, 'show_result'])->name('clocksalary_request');
Route::get('/clocksalary/add/empname={empname}&name={cusid}&salary={addsalary}', [App\Http\Controllers\ClockSalaryController::class, 'add'])->name('clocksalary_add');
Route::get('/clocksalary/delete/{Delete_id}', [App\Http\Controllers\ClockSalaryController::class, 'delete'])->name('clocksalary.delete');

Route::get('/organize', [App\Http\Controllers\OrganizeController::class, 'index'])->name('organize');
Route::get('/organize/request/{Request_id}', [App\Http\Controllers\OrganizeController::class, 'request'])->name('organize.request');
Route::get('/organize/delete/{Delete_id}', [App\Http\Controllers\OrganizeController::class, 'destroy'])->name('organize.delete');
Route::get('/organize_asc', [App\Http\Controllers\OrganizeController::class, 'show'])->name('organize_asc');
Route::POST('organize/store',[OrganizeController::class,'store'])->name('organize.store');
Route::POST('/organize/update', [App\Http\Controllers\OrganizeController::class, 'update'])->name('organize.update');

Route::get('/schedule', [SchedulesController::class, 'index'])->name('schedule');
Route::get('schedules/exportcsv', [SchedulesController::class, 'exportCSV'])->name('schedules.exportcsv');
Route::get('schedules/exportexcel', [SchedulesController::class, 'exportExcel'])->name('schedules.exportexcel');
Route::get('schedules/export',[SchedulesController::class,'export'])->name('schedules.export');
Route::POST('schedules/importdata/', [SchedulesController::class, 'importData'])->name('schedules.importdata');
Route::POST('schedules/validateandimportdata/', [SchedulesController::class, 'validateAndImportdata'])->name('schedules.validateandimportdata');
Route::POST('schedules/exportbyagecsv', [SchedulesController::class, 'exportByAgeCSV'])->name('schedules.exportbyagecsv');
Route::POST('schedules/import',[SchedulesController::class,'import'])->name('schedules.import');
Route::POST('schedules/export',[SchedulesController::class,'export'])->name('schedules.export');
Route::POST('schedules/download_example',[SchedulesController::class,'download_example'])->name('schedules.download_example');

Route::get('/table', [TableController::class, 'index'])->name('table');
Route::get('/table/download/{id}', [TableController::class, 'download'])->name('table.download');
Route::get('/table_overview', [TableController::class, 'overview'])->name('table.overview');
Route::POST('/table_attendance', [TableController::class, 'attendance'])->name('table.attendance');
Route::POST('/table_salary', [TableController::class, 'salary'])->name('table.salary');
Route::POST('/table_resign', [TableController::class, 'resign'])->name('table.resign');
Route::POST('/table_leave', [TableController::class, 'leave'])->name('table.leave');
Route::POST('/table_export_access_salary', [TableController::class, 'export_access_salary'])->name('table.export_access_salary');
Route::POST('/table_import_access_salary', [TableController::class, 'import_access_salary'])->name('table.import_access_salary');
Route::POST('/table_export_extra_schedule', [TableController::class, 'export_extra_schedule'])->name('table.export_extra_schedule');
Route::get('/table/request/{Request_id}', [App\Http\Controllers\TableController::class, 'request'])->name('table.request');
Route::get('/table/additional/{Request_id}', [App\Http\Controllers\TableController::class, 'additional'])->name('table.additional');
Route::get('/table/update/id={update_id}&status={update_status}&emp={update_emp}',[App\Http\Controllers\TableController::class,'updateStatus'])->name('table.updateStatus');
Route::get('/table/update/addtional/id={update_id}&additional={update_status}',[App\Http\Controllers\TableController::class,'updateAdditional'])->name('table.updateAdditional');
Route::get('/table/requestoverview/name={name}&start_time={start_time}&end_time={end_time}',[App\Http\Controllers\TableController::class,'requestoverview'])->name('table.requestoverview');

Route::get('/punch_record', [App\Http\Controllers\TableController::class, 'showPunch'])->name('punch_record');
Route::get('/punch_record/request/name={search_id}&start_time={start_time}&end_time={end_time}',
            [App\Http\Controllers\TableController::class,'requestPunch'])->name('punch_record.request');

Route::POST('ajaxRequest',[AjaxController::class,'ajaxRequestPost'])->name('ajaxRequest');
Route::POST('ajaxRequestCustomer',[AjaxController::class,'ajaxRequestCustomer'])->name('ajaxRequestCustomer');
Route::POST('ajaxRequestSchedule',[AjaxController::class,'ajaxRequestSchedule'])->name('ajaxRequestSchedule');
//Route::get('ajaxRequestSchedule',[AjaxController::class,'ajaxRequestSchedule'])->name('ajaxRequestSchedule');

Route::get('/403', function () {abort(403, '抱歉，你没有使用此功能權限！');});
//Route::get('/403', function () {return view('/errors/403');});





