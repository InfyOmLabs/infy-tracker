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
        'prefix'      => substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 5),
        'description' => $faker->sentence,
        'client_id'   => $client->id,
    ];
});
