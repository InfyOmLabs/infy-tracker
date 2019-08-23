<?php

namespace Tests\Feature\tests\Features\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
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
