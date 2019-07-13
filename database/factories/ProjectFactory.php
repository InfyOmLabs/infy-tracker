<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Project;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'name'        => $faker->name,
        'prefix'      => $faker->name,
        'description' => $faker->sentence,
    ];
});
