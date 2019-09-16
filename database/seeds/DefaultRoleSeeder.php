<?php

use App\Models\Role;
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
        $roles[] = [
            'name'         => 'admin',
            'display_name' => 'Admin',
            'description'  => 'Admin',
        ];
        $roles[] = [
            'name'         => 'team_member',
            'display_name' => 'Team Member',
            'description'  => 'Team Member',
        ];
        $roles[] = [
            'name'         => 'developer',
            'display_name' => 'Developer',
            'description'  => 'Developer',
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
