<?php

/* @var $factory Factory */

use App\Models\Client;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Client::class, function (Faker $faker) {
    $department = factory(\App\Models\Department::class)->create();
    return [
        'name'    => $faker->name,
        'email'   => $faker->unique()->email,
        'website' => $faker->domainName,
        'department_id' => $department->id,
    ];
});
