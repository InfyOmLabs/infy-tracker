<?php

use App\Models\ActivityType;
use Illuminate\Database\Migrations\Migration;

class AddDefaultActivityTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('activity_types')->truncate();
    }
}
