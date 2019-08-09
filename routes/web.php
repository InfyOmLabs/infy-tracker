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
        Route::resource('activity-types', 'ActivityTypeController');
        Route::post('activity-types/{activity_type}/update', 'ActivityTypeController@update');
    });

    Route::middleware('permission:manage_clients')->group(function () {
        Route::resource('clients', 'ClientController');
        Route::post('clients/{client}/update', 'ClientController@update');
    });

    Route::middleware('permission:manage_users')->group(function () {
        Route::post('users/{user}/active-de-active', 'UserController@activeDeActiveUser');
        Route::resource('users', 'UserController');
        Route::post('users/{user}/update', 'UserController@update')->where('user', '\d+');
        Route::get('users/{user}/send-email', 'UserController@resendEmailVerification');
    });

    Route::get('users/{user}/edit', 'UserController@edit');
    Route::post('users/profile-update', 'UserController@profileUpdate');

    Route::middleware('permission:manage_tags')->group(function () {
        Route::resource('tags', 'TagController');
        Route::post('tags/{tag}/update', 'TagController@update');
    });

    Route::middleware('permission:manage_projects')->group(function () {
        Route::resource('projects', 'ProjectController');
        Route::post('projects/{project}/update', 'ProjectController@update')->where('project', '\d+');
    });

    Route::middleware('permission:manage_all_tasks')->group(function () {
        Route::resource('tasks', 'TaskController');
        Route::post('tasks/{task}/update', 'TaskController@update');
        Route::post('tasks/{task}/update-status', 'TaskController@updateStatus');
        Route::post('tasks/{task}/add-attachment', 'TaskController@addAttachment');
        Route::post('tasks/{task_attachment}/delete-attachment', 'TaskController@deleteAttachment');
        Route::get('tasks/{task}/get-attachments', 'TaskController@getAttachment');
        Route::post('tasks/{task}/comments', 'CommentController@addComment');
        Route::post('tasks/{task}/comments/{comment}/update', 'CommentController@editComment');
        Route::delete('tasks/{task}/comments/{comment}', 'CommentController@deleteComment');
        Route::get('task-details/{task}', 'TaskController@getTaskDetails');
        Route::get('tasks/{task}/comments-count', 'TaskController@getCommentsCount');
        Route::get('tasks/{task}/task-users', 'TaskController@getTaskUsers');
    });

    Route::resource('time-entries', 'TimeEntryController');
    Route::post('time-entries/{time_entry}/update', 'TimeEntryController@update');

    Route::middleware('permission:manage_reports')->group(function () {
        Route::post('reports/{report}', 'ReportController@update');
        Route::resource('reports', 'ReportController');
        Route::get('users-of-projects', 'ProjectController@users');
        Route::get('projects-of-client', 'ClientController@projects');
    });

    Route::get('my-tasks', 'TaskController@myTasks');
    Route::get('user-last-task-work', 'TimeEntryController@getUserLastTask');
    Route::get('projects/{project}/tasks', 'TimeEntryController@getTasks');
    Route::get('my-projects', 'ProjectController@getMyProjects');

    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('roles', 'RoleController');
        Route::post('roles/{role}/update', 'RoleController@update');
    });
});

Route::fallback(function () {
    abort(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
});
