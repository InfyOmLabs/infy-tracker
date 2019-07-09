<?php

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
        $roleIds = [];
        /** @var Role $role */
        $role = Role::whereName('Admin')->first();
        if (!empty($role)) {
            $roleIds = $role->id;
        }
        $users = User::get();
        /** @var User $user */
        foreach ($users as $user) {
            $user->roles()->sync($roleIds);
        }
    }
}
