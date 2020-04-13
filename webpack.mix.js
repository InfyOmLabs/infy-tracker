const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

/* Copy */
mix.copyDirectory('resources/assets/img', 'public/assets/img');

/* CSS */
mix
    .sass('resources/assets/style/sass/laravel/app.scss', 'public/assets/style/css/app.css')
    .sass('resources/assets/style/sass/style.scss', 'public/assets/style/css/style.css')
    .sass('resources/assets/style/sass/dashboard.scss', 'public/assets/style/css/dashboard.css')
    .sass('resources/assets/style/sass/task-detail.scss', 'public/assets/style/css/task-detail.css')
    .sass('resources/assets/style/sass/report.scss', 'public/assets/style/css/report.css')
    .version();

/* JS */
mix
    .js('resources/assets/js/custom.js', 'public/assets/js/custom.js')
    .js('resources/assets/js/time_tracker/time_tracker.js', 'public/assets/js/time_tracker/time_tracker.js')
    .js('resources/assets/js/users/user.js', 'public/assets/js/users/user.js')
    .js('resources/assets/js/time_entries/time_entry.js', 'public/assets/js/time_entries/time_entry.js')
    .js('resources/assets/js/clients/client.js', 'public/assets/js/clients/client.js')
    .js('resources/assets/js/projects/project.js', 'public/assets/js/projects/project.js').
    js('resources/assets/js/task/task.js', 'public/assets/js/task/task.js').
    js('resources/assets/js/activity_types/activity.js',
        'public/assets/js/activity_types/activity.js').
    js('resources/assets/js/tags/tag.js', 'public/assets/js/tags/tag.js').
    js('resources/assets/js/report/report.js',
        'public/assets/js/report/report.js').
    js('resources/assets/js/dashboard/dashboard.js',
        'public/assets/js/dashboard/dashboard.js').
    js('resources/assets/js/dashboard/developers-daily-report.js',
        'public/assets/js/dashboard/developers-daily-report.js').
    js('resources/assets/js/task/task_detail.js',
        'public/assets/js/task/task_detail.js').
    js('resources/assets/js/profile/profile.js',
        'public/assets/js/profile/profile.js').
    js('resources/assets/js/roles/role.js', 'public/assets/js/roles/role.js').
    js('resources/assets/js/task/task_time_entry.js',
        'public/assets/js/task/task_time_entry.js').
    js('resources/assets/js/department/department.js',
        'public/assets/js/department/department.js').
    version();
