<?php

namespace Tests\Controllers\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class LoginControllerTest
 */
class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_it_shows_login_form()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertSeeText('Login')
            ->assertSeeText('Sign In to your account')
            ->assertSeeText('Forgot password?');
    }

    /** @test */
    public function test_it_shows_password_reset_form()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200)
            ->assertViewIs('auth.passwords.email')
            ->assertSeeText('Reset Your Password')
            ->assertSeeText('Enter Email to reset password');
    }
}
