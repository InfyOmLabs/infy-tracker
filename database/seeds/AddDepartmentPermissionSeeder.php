<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AddDepartmentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionName = 'manage_department';

        $permission = Permission::create([
            'name'         => $permissionName,
            'guard_name'   => 'web',
            'display_name' => 'Manage Department',
        ]);

        /** @var Role $adminRole */
        $adminRole = Role::whereName('Admin')->first();

        $adminRole->givePermissionTo($permission);
    }
}
