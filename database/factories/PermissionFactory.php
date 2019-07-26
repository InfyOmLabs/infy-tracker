<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Permission;
use Faker\Generator as Faker;

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'name'         => $faker->name,
        'display_name' => $faker->name,
        'description'  => $faker->text,
    ];
});
