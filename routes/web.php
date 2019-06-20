<?php

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

Route::get('/', function () {
    return redirect('login');
});

/*
|--------------------------------------------------------------------------
| Auth Login Route
|--------------------------------------------------------------------------
*/
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

Route::post('login', 'Auth\LoginController@login');

Route::get('activate', 'AuthController@verifyAccount');

Route::post('set-password', 'AuthController@setPassword');

Route::group(['middleware' => ['auth', 'validate.user']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/users-work-report', 'HomeController@workReport')->name('users-work-report');
    Route::get('/developer-work-report', 'HomeController@developerWorkReport')->name('developers-work-report');

    Route::post('logout', 'Auth\LoginController@logout');

    Route::resource('activityTypes', 'ActivityTypeController');
    Route::post('activityTypes/{id}/update', 'ActivityTypeController@update');

    Route::resource('clients', 'ClientController');
    Route::post('clients/{id}/update', 'ClientController@update');

    Route::post('users/profile-update', 'UserController@profileUpdate');
    Route::resource('users', 'UserController');
    Route::post('users/{id}/update', 'UserController@update');
    Route::get('users/send-email/{id}', 'UserController@resendEmailVerification');

    Route::resource('tags', 'TagController');
    Route::post('tags/{id}/update', 'TagController@update');

    Route::resource('projects', 'ProjectController');
    Route::get('my-projects', 'ProjectController@getMyProjects');
    Route::post('projects/{id}/update', 'ProjectController@update');

    Route::resource('tasks', 'TaskController');
    Route::post('tasks/{id}/update', 'TaskController@update');
    Route::post('tasks/{id}/update-status', 'TaskController@updateStatus');
    Route::post('tasks/add-attachment/{id}', 'TaskController@addAttachment');
    Route::post('tasks/delete-attachment/{id}', 'TaskController@deleteAttachment');
    Route::get('tasks/get-attachments/{id}', 'TaskController@getAttachment');

    Route::resource('timeEntries', 'TimeEntryController');
    Route::post('timeEntries/{id}/update', 'TimeEntryController@update');

    Route::get('reports', 'ReportController@index')->name('reports.index');
    Route::get('timeTracker', 'TimeTrackerController@index')->name('timeTracker.index');

    Route::get('task-details/{task_id}', 'TaskController@getTaskDetails');

    Route::get('my-tasks', 'TaskController@myTasks');
    Route::get('user-last-task-work', 'TimeEntryController@getUserLastTask');
    Route::get('get-tasks/{projectId}', 'TimeEntryController@getTasks');
});
