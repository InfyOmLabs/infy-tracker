<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    $project = factory(Project::class)->create();

    return [
        'title'       => $faker->sentence,
        'description' => $faker->text,
        'project_id'  => $project->id,
        'due_date'    => $faker->dateTime,
        'task_number' => $faker->unique()->randomDigitNotNull,
    ];
});

$factory->state(Task::class, 'tag', function () {
    $tag = factory(Tag::class)->create();

    return [
        'tags' => [$tag->id],
    ];
});

$factory->state(Task::class, 'assignees', function () {
    $assignees = factory(User::class)->times(2)->create();

    return [
        'assignees' => [$assignees[0]->id, $assignees[1]->id],
    ];
});
