<?php

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class DefaultActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultActivityTypes = [
            'Development',
            'Management',
            'Code Review',
            'Testing',
            'Documentation',
        ];

        foreach ($defaultActivityTypes as $activityType) {
            ActivityType::create(['name' => $activityType]);
        }
    }
}
