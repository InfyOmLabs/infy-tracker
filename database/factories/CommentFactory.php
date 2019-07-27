<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 27-07-2019
 * Time: 05:21 PM.
 */

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    $user = factory(User::class)->create();
    $task = factory(Task::class)->create();

    return [
        'comment'    => $faker->text,
        'task_id'    => $task->id,
        'created_by' => $user->id,
    ];
});
