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

Route::group(['middleware' => ['auth', 'validate.user', 'user.activated']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/users-work-report', 'HomeController@workReport')->name('users-work-report');
    Route::get('/developer-work-report', 'HomeController@developerWorkReport')->name('developers-work-report');
    Route::get('/users-open-tasks', 'HomeController@userOpenTasks')->name('users-open-tasks');

    Route::post('logout', 'Auth\LoginController@logout');
    Route::group(['middleware' => ['permission:manage_activities']], function () {
        Route::resource('activity-types', 'ActivityTypeController');
    });

    Route::group(['middleware' => ['permission:manage_clients']], function () {
        Route::resource('clients', 'ClientController');
    });

    Route::group(['middleware' => ['permission:manage_users']], function () {
        Route::post('users/{user}/active-de-active', 'UserController@activeDeActiveUser')
            ->name('active-de-active-user');
        Route::resource('users', 'UserController');
        Route::get('users/{user}/send-email', 'UserController@resendEmailVerification')->name('send-email');
    });

    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::post('users/profile-update', 'UserController@profileUpdate')->name('update-profile');
    Route::post('users/change-password', 'UserController@changePassword')->name('change-password');

    Route::group(['middleware' => ['permission:manage_tags']], function () {
        Route::resource('tags', 'TagController');
    });

    Route::group(['middleware' => ['permission:manage_projects']], function () {
        Route::resource('projects', 'ProjectController');
    });

    // tasks routes
    Route::resource('tasks', 'TaskController');
    Route::post('tasks/{task}/update-status', 'TaskController@updateStatus')->name('task.update-status');
    Route::post('tasks/{task}/add-attachment', 'TaskController@addAttachment')->name('task.add-attachment');
    Route::post('tasks/{task_attachment}/delete-attachment', 'TaskController@deleteAttachment')
        ->name('task.delete-attachment');
    Route::get('tasks/{task}/get-attachments', 'TaskController@getAttachment')->name('task.attachments');
    Route::post('tasks/{task}/comments', 'CommentController@addComment')->name('task.comments');
    Route::post(
        'tasks/{task}/comments/{comment}/update',
        'CommentController@editComment'
    )->name('task.update-comment');
    Route::delete(
        'tasks/{task}/comments/{comment}',
        'CommentController@deleteComment'
    )->name('task.delete-comment');
    Route::get('task-details/{task}', 'TaskController@getTaskDetails')->name('task.get-details');
    Route::get('tasks/{task}/comments-count', 'TaskController@getCommentsCount')->name('task.comments-count');
    Route::get('tasks/{task}/users', 'TaskController@getTaskUsers')->name('task.users');

    Route::resource('time-entries', 'TimeEntryController');
    Route::post('time-entries/{time_entry}/update', 'TimeEntryController@update');
    Route::post('start-timer', 'TimeEntryController@getStartTimer');
    Route::get('copy-today-activity', 'TimeEntryController@copyTodayActivity')->name('copy-today-activity');

    Route::resource('reports', 'ReportController');
    Route::get('users-of-projects', 'ProjectController@users')->name('users-of-projects');
    Route::get('projects-of-client', 'ClientController@projects')->name('projects-of-client');
    Route::get('clients-of-department', 'DepartmentController@clients')->name('clients-of-department');

    Route::get('my-tasks', 'TaskController@myTasks')->name('my-tasks');
    Route::get('user-last-task-work', 'TimeEntryController@getUserLastTask')->name('user-last-task-work');
    Route::get('projects/{project}/tasks', 'TimeEntryController@getTasks')->name('project-tasks');
    Route::get('my-projects', 'ProjectController@getMyProjects')->name('my-projects');

    Route::group(['middleware' => ['permission:manage_roles']], function () {
        Route::resource('roles', 'RoleController');
    });

    Route::group(['middleware' => ['permission:manage_department']], function () {
        Route::resource('departments', 'DepartmentController');
        Route::post('departments/{department}/update', 'DepartmentController@update');
    });
});

Route::fallback(function () {
    abort(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
});
