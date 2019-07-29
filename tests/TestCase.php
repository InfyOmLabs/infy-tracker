<?php

namespace Tests;

use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

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

    public function assertSuccessMessageResponse(TestResponse $response, string $message)
    {
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => $message,
            ]);
    }

    public function assertSuccessDataResponse(TestResponse $response, array $data, string $message)
    {
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => $message,
                'data'    => $data,
            ]);
    }

    public function assertExceptionMessage(TestResponse $response, string $message)
    {
        $this->assertEquals($message, $response->exception->getMessage());
    }

    /**
     * @param string $string
     * @param string $timezone
     *
     * @return Carbon
     */
    protected function mockTime($string, $timezone = 'UTC')
    {
        Carbon::setTestNow(Carbon::parse($string, $timezone));

        return Carbon::now();
    }
}
