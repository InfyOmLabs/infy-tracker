<?php

/* @var $factory Factory */

use App\Models\Report;
use App\Models\ReportFilter;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(ReportFilter::class, function (Faker $faker) {
    $report = factory(Report::class)->create();

    return [
        'param_id'   => $faker->randomDigit,
        'report_id'  => $report->id,
        'param_type' => $faker->word,
    ];
});
