<?php

use App\Repositories\RoleRepository;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $userPermissions=[
           'manage_clients',
           'manage_projects',
           'manage_all_tasks',
           'manage_users',
           'manage_tags',
           'manage_activities'
       ];
        $permissionIds = \App\Models\Permission::whereIn('name', $userPermissions)->get()->pluck('id');
        $input = [
            'name' => 'User'
        ];
        /** @var RoleRepository $roleRepo */
        $roleRepo = app(RoleRepository::class);
        /** @var \App\Models\Role $role */
        $role = $roleRepo->create($input);
        $role->perms()->sync($permissionIds);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function down()
    {
        /** @var \App\Models\Role $role */
        $role = \App\Models\Role::where('name', 'User')->first();
        $role->perms()->sync([]);
        $role->delete();
    }
}
