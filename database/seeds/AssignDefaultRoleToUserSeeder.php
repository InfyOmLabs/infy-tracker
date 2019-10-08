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
        /** @var Role $adminRole */
        $adminRole = Role::whereName('Admin')->first();
        /** @var Role $developerRole */
        $developerRole = Role::whereName('Developer')->first();
        /** @var Role $teamMemberRole */
        $teamMemberRole = Role::whereName('Team Member')->first();

        $permissions = Permission::all();
        $adminRole->givePermissionTo($permissions);
        $developerRole->givePermissionTo($permissions);

        $permissions = Permission::whereIn(
            'name',
            ['manage_tags', 'manage_activities', 'manage_reports', 'manage_all_tasks']
        )->get();
        $teamMemberRole->givePermissionTo($permissions);

        $roleIds = [];
        if (!empty($adminRole)) {
            $roleIds = $adminRole->id;
        }
        $users = User::get();
        /** @var User $user */
        foreach ($users as $user) {
            $user->roles()->sync($roleIds);
        }
    }
}
