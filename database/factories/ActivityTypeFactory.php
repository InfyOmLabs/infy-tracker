<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\ActivityType;
use Faker\Generator as Faker;

$factory->define(ActivityType::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
