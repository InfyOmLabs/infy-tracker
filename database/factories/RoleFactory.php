<?php

/* @var $factory Factory */

use App\Models\Role;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name'         => $faker->unique()->name,
        'display_name' => $faker->name,
        'description'  => $faker->text,
    ];
});
