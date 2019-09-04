<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Report;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Report::class, function (Faker $faker) {
    $user = factory(User::class)->create();
    $startDate = date('Y-m-d H:i:s', strtotime('-1 day'));
    $endDate = date('Y-m-d H:i:s');

    return [
        'name'       => $faker->word,
        'owner_id'   => $user->id,
        'start_date' => $startDate,
        'end_date'   => $endDate,
    ];
});
