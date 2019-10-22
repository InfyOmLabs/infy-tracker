<?php

/* @var $factory Factory */

use App\Models\ActivityType;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(TimeEntry::class, function (Faker $faker) {
    $user = factory(User::class)->create();
    $activityType = factory(ActivityType::class)->create();
    $task = factory(Task::class)->create();

    $startTime = date('Y-m-d H:i:s');
    $endTime = date('Y-m-d H:i:s', strtotime($startTime.'+1 hours'));

    return [
        'task_id'          => $task->id,
        'activity_type_id' => $activityType->id,
        'user_id'          => $user->id,
        'start_time'       => $startTime,
        'end_time'         => $endTime,
        'duration'         => $faker->randomDigit,
        'note'             => $faker->sentence,
        'entry_type'       => TimeEntry::STOPWATCH,
    ];
});
