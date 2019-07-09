<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input = [
            'name'              => 'InfyTracker Admin',
            'email'             => 'admin@infytracker.local',
            'password'          => Hash::make('InfyTrack3r'),
            'set_password'      => true,
            'is_email_verified' => true,
            'is_active'         => true,
        ];

        User::create($input);
    }
}
