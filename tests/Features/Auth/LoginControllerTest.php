<?php

namespace Tests\Features\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function test_it_shows_login_form()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200)
                 ->assertViewIs('auth.login')
                 ->assertSeeText('Login')
                 ->assertSeeText('Sign In to your account')
                 ->assertSeeText('Forgot password?');
    }
}
