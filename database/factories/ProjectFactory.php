<?php

/* @var $factory Factory */

use App\Models\Client;
use App\Models\Project;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Project::class, function (Faker $faker) {
    $client = factory(Client::class)->create();

    return [
        'name'        => $faker->name,
        'prefix'      => $faker->name,
        'description' => $faker->sentence,
        'client_id'   => $client->id,
    ];
});
