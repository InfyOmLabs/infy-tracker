<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssignDefaultRoleToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roleIds = [];
        /** @var \App\Models\Role $role */
        $role = \App\Models\Role::whereName('User')->first();
        if (!empty($role)) {
            $roleIds = $role->id;
        }
        $users = \App\Models\User::get();
        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            $user->roles()->sync($roleIds);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $users = \App\Models\User::get();
        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            $user->roles()->sync([]);
        }
    }
}
