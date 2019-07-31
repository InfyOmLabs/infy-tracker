<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 27-07-2019
 * Time: 05:21 PM.
 */

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(\App\Models\TaskAttachment::class, function (Faker $faker) {
    $task = factory(Task::class)->create();

    return [
        'task_id' => $task->id,
    ];
});
