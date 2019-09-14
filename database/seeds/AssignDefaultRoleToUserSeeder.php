<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignDefaultRoleToUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Role $admin */
        $admin = Role::whereName('admin')->first();
        /** @var Role $developer */
        $developer = Role::whereName('developer')->first();
        /** @var Role $team_member */
        $team_member = Role::whereName('team_member')->first();

        $permissions = Permission::all();
        $admin->givePermissionTo($permissions);
        $developer->givePermissionTo($permissions);

        $permissions = Permission::whereIn('name', ['manage_tags', 'manage_activities', 'manage_reports', 'manage_all_tasks'])->get();
        $team_member->givePermissionTo($permissions);

//        $roleIds = [];
        /** @var Role $role */
//        $role = Role::whereName('Admin')->first();
//        if (!empty($role)) {
//            $roleIds = $role->id;
//        }
//        $users = User::get();
        /** @var User $user */
//        foreach ($users as $user) {
//            $user->roles()->sync($roleIds);
//        }
    }
}
