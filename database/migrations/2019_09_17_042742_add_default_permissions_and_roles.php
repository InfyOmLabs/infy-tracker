<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDefaultPermissionsAndRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create permissions
        $oldPermissions = DB::table('entrust_permissions')->get();
        foreach ($oldPermissions as $permission) {
            Permission::create([
                'name'         => $permission->name,
                'display_name' => $permission->display_name,
                'description'  => $permission->description,
            ]);
        }

        $oldRoles = DB::table('entrust_roles')->get();

        foreach ($oldRoles as $role) {
            $role = Role::create([
                'name'         => $role->name,
                'display_name' => $role->display_name,
                'description'  => $role->description,
            ]);

            $roleOldPermission = DB::table('entrust_permission_role as epr')
                ->leftJoin('entrust_roles as er', 'er.id', '=', 'epr.role_id')
                ->where('er.name', '=', $role->name)
                ->get(['epr.permission_id'])
                ->pluck('permission_id')
                ->toArray();

            $role->givePermissionTo($roleOldPermission);
        }

        $rolUsers = DB::table('entrust_role_user as eru')
            ->leftJoin('entrust_roles as er', 'er.id', '=', 'eru.role_id')
            ->leftJoin('roles as r', 'er.name', '=', 'r.name')
            ->get(['eru.user_id', 'r.id as role_id']);

        foreach ($rolUsers as $rolUser) {
            $user = User::find($rolUser->user_id);
            $user->roles()->sync($rolUser->role_id);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
