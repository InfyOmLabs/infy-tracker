<?php

/* @var $factory Factory */

use App\Models\Task;
use App\Models\TaskAttachment;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(TaskAttachment::class, function (Faker $faker) {
    $task = factory(Task::class)->create();

    return [
        'task_id' => $task->id,
    ];
});
