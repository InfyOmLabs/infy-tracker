<?php

namespace Tests\Permissions;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class UserControllerPermissionTest.
 */
class UserControllerPermissionTest extends TestCase
{
    use DatabaseTransactions;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function test_can_get_users_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_users']);

        $response = $this->getJson(route('users.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_users_without_permission()
    {
        $response = $this->get(route('users.index'));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_create_user_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_users']);

        $user = factory(User::class)->raw();

        $response = $this->postJson(route('users.store'), $user);

        $this->assertSuccessMessageResponse($response, 'User created successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_create_user_without_permission()
    {
        $user = factory(User::class)->raw();

        $response = $this->post(route('users.store'), $user);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_update_user_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_users']);

        /** @var User $user */
        $user = factory(User::class)->create();
        $updateUser = factory(User::class)->raw(['id' => $user->id]);

        $response = $this->putJson(route('users.update', $user->id), $updateUser);

        $this->assertSuccessMessageResponse($response, 'User updated successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_user_without_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $updateUser = factory(User::class)->raw(['id' => $user->id]);

        $response = $this->put(route('users.update', $user->id), $updateUser);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_delete_user_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_users']);

        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->deleteJson(route('users.destroy', $user->id));

        $this->assertSuccessMessageResponse($response, 'User deleted successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_delete_user_without_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->delete(route('users.destroy', $user->id));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_activate_user_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_users']);

        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->postJson(route('active-de-active-user', $user->id), []);

        $this->assertSuccessMessageResponse($response, 'User updated successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_activate_user_without_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->post(route('active-de-active-user', $user->id), []);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_resend_email_verification_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_users']);

        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->getJson(route('send-email', $user->id));

        $this->assertSuccessMessageResponse($response, 'Verification email has been sent successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_resend_email_verification_without_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->get(route('send-email', $user->id));

        $response->assertStatus(403);
    }
}
