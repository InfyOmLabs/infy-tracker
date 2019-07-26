<?php

namespace Tests;

use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @var \Faker\Generator */
    public $faker;

    use CreatesApplication;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = Factory::create();
    }

    public function signInWithDefaultAdminUser()
    {
        $user = User::first();

        return $this->actingAs($user);
    }
}
