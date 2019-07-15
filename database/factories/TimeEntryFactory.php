<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\ActivityType;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(TimeEntry::class, function (Faker $faker) {
    $user = factory(User::class)->create();
    $activityType = factory(ActivityType::class)->create();
    $task = factory(Task::class)->create();

    return [
        'task_id'          => $task->id,
        'activity_type_id' => $activityType->id,
        'user_id'          => $user->id,
        'start_time'       => $faker->dateTime,
        'end_time'         => $faker->dateTime,
        'duration'         => $faker->randomDigit,
        'note'             => $faker->sentence,
    ];
});
