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
Auth::routes();
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');

Route::post('login', 'Auth\LoginController@login');

Route::get('activate', 'AuthController@verifyAccount');

Route::post('set-password', 'AuthController@setPassword');

Route::group(['middleware' => ['auth', 'validate.user']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/users-work-report', 'HomeController@workReport')->name('users-work-report');
    Route::get('/developer-work-report', 'HomeController@developerWorkReport')->name('developers-work-report');

    Route::post('logout', 'Auth\LoginController@logout');

    Route::middleware('permission:manage_activities')->group(function () {
        Route::resource('activityTypes', 'ActivityTypeController');
        Route::post('activityTypes/{id}/update', 'ActivityTypeController@update');
    });

    Route::middleware('permission:manage_clients')->group(function () {
        Route::resource('clients', 'ClientController');
        Route::post('clients/{id}/update', 'ClientController@update');
    });

    Route::middleware('permission:manage_users')->group(function () {
        Route::post('users/profile-update', 'UserController@profileUpdate');
        Route::post('users/{id}/active-de-active', 'UserController@activeDeActiveUser');
        Route::resource('users', 'UserController');
        Route::post('users/{id}/update', 'UserController@update');
        Route::get('users/send-email/{id}', 'UserController@resendEmailVerification');
    });

    Route::middleware('permission:manage_tags')->group(function () {
        Route::resource('tags', 'TagController');
        Route::post('tags/{id}/update', 'TagController@update');
    });

    Route::middleware('permission:manage_projects')->group(function () {
        Route::resource('projects', 'ProjectController');
        Route::post('projects/{id}/update', 'ProjectController@update');
    });

    Route::middleware('permission:manage_all_tasks')->group(function () {
        Route::resource('tasks', 'TaskController');
        Route::post('tasks/{id}/update', 'TaskController@update');
        Route::post('tasks/{id}/update-status', 'TaskController@updateStatus');
        Route::post('tasks/add-attachment/{id}', 'TaskController@addAttachment');
        Route::post('tasks/delete-attachment/{id}', 'TaskController@deleteAttachment');
        Route::get('tasks/get-attachments/{id}', 'TaskController@getAttachment');
        Route::post('comments/new', 'CommentController@addComment');
        Route::post('comments/{id}/update', 'CommentController@editComment');
        Route::get('comments/{id}/delete', 'CommentController@deleteComment');
        Route::get('task-details/{task_id}', 'TaskController@getTaskDetails');
    });

    Route::resource('timeEntries', 'TimeEntryController');
    Route::post('timeEntries/{id}/update', 'TimeEntryController@update');

    Route::get('reports', 'ReportController@index')->name('reports.index')->middleware('permission:manage_reports');

    Route::get('my-tasks', 'TaskController@myTasks');
    Route::get('user-last-task-work', 'TimeEntryController@getUserLastTask');
    Route::get('get-tasks/{projectId}', 'TimeEntryController@getTasks');
    Route::get('my-projects', 'ProjectController@getMyProjects');

    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', 'RoleController');
        Route::post('roles/{id}/update', 'RoleController@update');
    });
});

Route::fallback(function () {
    abort(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
});
