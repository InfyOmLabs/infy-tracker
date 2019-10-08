<?php

/* @var $factory Factory */

use App\Models\Permission;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'name'         => $faker->unique()->name,
        'display_name' => $faker->name,
        'description'  => $faker->text,
    ];
});
