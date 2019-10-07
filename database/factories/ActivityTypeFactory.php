<?php

/* @var $factory Factory */

use App\Models\ActivityType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(ActivityType::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
