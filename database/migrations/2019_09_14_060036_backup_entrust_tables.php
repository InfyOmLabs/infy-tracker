<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class BackupEntrustTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('permissions', 'entrust_permissions');
        Schema::rename('roles', 'entrust_roles');
        Schema::rename('role_user', 'entrust_role_user');
        Schema::rename('permission_role', 'entrust_permission_role');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('entrust_permissions', 'permissions');
        Schema::rename('entrust_roles', 'roles');
        Schema::rename('entrust_role_user', 'role_user');
        Schema::rename('entrust_permission_role', 'permission_role');
    }
}
