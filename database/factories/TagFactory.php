<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Tag;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Tag::class, function (Faker $faker) {
    $user = factory(User::class)->create();

    return [
        'name'       => $faker->name,
        'created_by' => $user->id,
    ];
});
