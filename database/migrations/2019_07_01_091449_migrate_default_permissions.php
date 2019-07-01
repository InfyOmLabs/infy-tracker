<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

class MigrateDefaultPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            [
                'name' => 'manage_clients',
                'display_name' => 'Manage Clients',
                'description' => 'Visible clients tab and manage it.'
            ],
            [
                'name' => 'manage_projects',
                'display_name' => 'Manage Projects',
                'description' => 'Project tab visible and manage it.'
            ],
            [
                'name' => 'manage_tasks',
                'display_name' => 'Manage Tasks',
                'description' => 'All projects list comes into Project filter otherwise comes only related projects.Assignee Filter visible in task module otherwise own assigned and non-assigned.'
            ],
            [
                'name' => 'manage_time_entries',
                'display_name' => 'Manage Time Entries',
                'description' => 'Assignee Filter visible in time entry module otherwise own Entry.Able to visible all users otherwise own.'
            ],
            [
                'name' => 'manage_users',
                'display_name' => 'Manage Users',
                'description' => 'User tab visible'
            ],
            [
                'name' => 'manage_tags',
                'display_name' => 'Manage Tags',
                'description' => 'Able to access tags tab.'
            ],
            [
                'name' => 'manage_activities',
                'display_name' => 'Manage Activities',
                'description' => 'Able to access Activity tab.'
            ],
            [
                'name' => 'manage_reports',
                'display_name' => 'Manage Reports',
                'description' => ''
            ],
            [
                'name' => 'manage_roles',
                'display_name' => 'Manage Roles',
                'description' => ''
            ]

        ];
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       $permissions=Permission::get();
       /** @var Permission $permission */
        foreach ($permissions as $permission){
           $permission->delete();
       }
    }
}
