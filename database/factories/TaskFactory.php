<?php

/* @var $factory Factory */

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Task::class, function (Faker $faker) {
    $project = factory(Project::class)->create();

    $dueDate = date('Y-m-d H:i:s', strtotime('+ 4hours'));

    return [
        'title'       => $faker->sentence,
        'description' => $faker->text,
        'project_id'  => $project->id,
        'due_date'    => $dueDate,
        'status'      => Task::STATUS_ACTIVE,
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
    $assignees = factory(User::class, 2)->create();

    return [
        'assignees' => [$assignees[0]->id, $assignees[1]->id],
    ];
});
