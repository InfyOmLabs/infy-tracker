<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Report;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Report::class, function (Faker $faker) {
    $user = factory(User::class)->create();

    return [
        'name'       => $faker->word,
        'owner_id'   => $user->id,
        'start_date' => $faker->dateTime,
        'end_date'   => $faker->dateTime,
    ];
});
