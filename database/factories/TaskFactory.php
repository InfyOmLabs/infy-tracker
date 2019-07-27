<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    $project = factory(\App\Models\Project::class)->create();

    return [
        'title'       => $faker->sentence,
        'description' => $faker->sentence,
        'project_id'  => $project->id,
        'due_date'    => $faker->dateTime,
        'task_number' => $faker->unique()->randomDigitNotNull,
    ];
});
