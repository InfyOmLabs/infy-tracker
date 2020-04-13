<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminUserSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(DefaultRoleSeeder::class);
        $this->call(AssignDefaultRoleToUserSeeder::class);
        $this->call(DefaultActivityTypeSeeder::class);
        $this->call(AddDepartmentPermissionSeeder::class);
    }
}
