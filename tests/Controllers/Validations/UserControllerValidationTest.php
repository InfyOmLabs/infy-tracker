<?php

namespace Tests\Controllers\Validations;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class UserControllerValidationTest.
 */
class UserControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_create_user_fails_when_name_is_not_passed()
    {
        $this->post('users', ['name' => ''])->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_user_fails_when_email_is_not_passed()
    {
        $this->post('users', ['email' => ''])->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_create_user_fails_when_phone_number_is_more_than_ten_digits()
    {
        $this->post('users', ['phone' => '999999999999'])->assertSessionHasErrors([
            'phone' => 'The phone number must be 10 digits long.',
        ]);
    }

    /** @test */
    public function test_create_user_fails_when_phone_number_is_not_numeric()
    {
        $this->post('users', ['phone' => 'abcdefghijklmnopqrstuvwxyz'])->assertSessionHasErrors([
            'phone' => 'The phone must be a number.',
        ]);
    }

    /** @test */
    public function test_create_user_fails_when_email_is_invalid()
    {
        $this->post('users', ['email' => 'random email'])->assertSessionHasErrors([
            'email' => 'Please enter valid email.',
        ]);
    }

    /** @test */
    public function it_can_create_user()
    {
        $input = [
            'name'  => 'random string',
            'email' => 'dummy@gmail.com',
        ];
        $this->post('users', $input)->assertSessionHasNoErrors();

        $user = User::whereName('random string')->first();
        $this->assertNotEmpty($user);
        $this->assertEquals('random string', $user->name);
    }

    /** @test */
    public function test_update_user_fails_when_name_is_not_passed()
    {
        $user = factory(User::class)->create();

        $this->put('users/'.$user->id, ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_user_fails_when_email_is_not_passed()
    {
        $user = factory(User::class)->create();

        $this->put('users/'.$user->id, ['email' => ''])
            ->assertSessionHasErrors(['email' => 'The email field is required.']);
    }

    /** @test */
    public function test_update_user_fails_when_phone_number_is_more_than_ten_digits()
    {
        $user = factory(User::class)->create();

        $this->put('users/'.$user->id, ['phone' => '999999999999'])->assertSessionHasErrors([
            'phone' => 'The phone number must be 10 digits long.',
        ]);
    }

    /** @test */
    public function test_update_user_fails_when_phone_number_is_not_numeric()
    {
        $user = factory(User::class)->create();

        $this->put('users/'.$user->id, ['phone' => 'abcdefghijklmnopqrstuvwxyz'])->assertSessionHasErrors([
            'phone' => 'The phone must be a number.',
        ]);
    }

    /** @test */
    public function test_update_user_fails_when_email_is_invalid()
    {
        $user = factory(User::class)->create();

        $this->put('users/'.$user->id, ['email' => 'random email'])->assertSessionHasErrors([
            'email' => 'Please enter valid email.',
        ]);
    }

    /** @test */
    public function it_can_update_user_with_valid_input()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $input = [
            'name'  => 'random string',
            'email' => 'dummy@gmail.com',
        ];

        $this->put('users/'.$user->id, $input)->assertSessionHasNoErrors();

        $this->assertEquals('random string', $user->fresh()->name);
    }
}