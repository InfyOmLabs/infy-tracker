<?php

use App\Models\Permission;
use App\Repositories\RoleRepository;
use Illuminate\Database\Seeder;

class DefaultRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userPermissions = [
            'manage_clients',
            'manage_projects',
            'manage_all_tasks',
            'manage_users',
            'manage_tags',
            'manage_activities',
            'manage_reports',
            'manage_roles',
            'manage_time_entries',
        ];
        $permissionIds = Permission::whereIn('name', $userPermissions)->get()->pluck('id');
        $input = [
            'name' => 'Admin',
        ];
        /** @var RoleRepository $roleRepo */
        $roleRepo = app(RoleRepository::class);
        /** @var \App\Models\Role $role */
        $role = $roleRepo->create($input);
        $role->perms()->sync($permissionIds);
    }
}
