<?php
/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(\App\Models\TaskAttachment::class, function (Faker $faker) {
    $task = factory(Task::class)->create();

    return [
        'task_id' => $task->id,
    ];
});
