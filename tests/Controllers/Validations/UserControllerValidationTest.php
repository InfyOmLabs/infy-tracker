<?php

namespace Tests\Controllers\Validations;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ProjectControllerValidationTest.
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
    public function create_user_fails_when_name_is_not_passed()
    {
        $this->post('users', ['name' => ''])->assertSessionHasErrors('name');
    }

    /** @test */
    public function create_user_fails_when_email_is_not_passed()
    {
        $input = [
            'name'  => 'random string',
            'email' => '',
        ];

        $this->post('users', $input)->assertSessionHasErrors('email');
    }

    /** @test */
    public function create_user_fails_when_phone_number_is_more_than_ten_digits()
    {
        $input = [
            'name'  => 'random string',
            'email' => 'xyz@gmail.com',
            'phone' => '999999999999',
        ];

        $this->post('users', $input)->assertSessionHasErrors([
            'phone' => 'The phone number must be 10 digits long.',
        ]);
    }

    /** @test */
    public function create_user_fails_when_phone_number_is_not_numeric()
    {
        $input = [
            'name'  => 'random string',
            'email' => 'xyz@gmail.com',
            'phone' => 'abcdefghijklmnopqrstuvwxyz',
        ];

        $this->post('users', $input)->assertSessionHasErrors([
            'phone' => 'The phone must be a number.',
        ]);
    }

    /** @test */
    public function create_user_fails_when_email_is_invalid()
    {
        $input = [
            'name'  => 'random string',
            'email' => 'random email',
        ];

        $this->post('users', $input)->assertSessionHasErrors([
            'email' => 'Please enter valid email.',
        ]);
    }

    /** @test */
    public function update_user_fails_when_name_is_not_passed()
    {
        $user = factory(User::class)->create();

        $this->put('users/'.$user->id, ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function update_user_fails_when_email_is_not_passed()
    {
        $user = factory(User::class)->create();
        $input = [
            'name'  => 'random string',
            'email' => '',
        ];

        $this->put('users/'.$user->id, $input)
            ->assertSessionHasErrors(['email' => 'The email field is required.']);
    }

    /** @test */
    public function update_user_fails_when_phone_number_is_more_than_ten_digits()
    {
        $user = factory(User::class)->create();
        $input = [
            'name'  => 'random string',
            'email' => 'xyz@gmail.com',
            'phone' => '999999999999',
        ];

        $this->put('users/'.$user->id, $input)->assertSessionHasErrors([
            'phone' => 'The phone number must be 10 digits long.',
        ]);
    }

    /** @test */
    public function update_user_fails_when_phone_number_is_not_numeric()
    {
        $user = factory(User::class)->create();
        $input = [
            'name'  => 'random string',
            'email' => 'xyz@gmail.com',
            'phone' => 'abcdefghijklmnopqrstuvwxyz',
        ];

        $this->put('users/'.$user->id, $input)->assertSessionHasErrors([
            'phone' => 'The phone must be a number.',
        ]);
    }

    /** @test */
    public function update_user_fails_when_email_is_invalid()
    {
        $user = factory(User::class)->create();
        $input = [
            'name'  => 'random string',
            'email' => 'random email',
        ];

        $this->put('users/'.$user->id, $input)->assertSessionHasErrors([
            'email' => 'Please enter valid email.',
        ]);
    }
}
