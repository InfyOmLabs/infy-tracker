<?php

namespace Tests\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $userRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->userRepo = \Mockery::mock(UserRepository::class);
        app()->instance(UserRepository::class, $this->userRepo);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function test_can_retrieve_user_with_its_project_and_roles()
    {
        factory(User::class)->create();
        $user = factory(User::class)->create();

        $project = factory(Project::class)->create();
        $project->users()->sync([$user->id]);

        $role = factory(Role::class)->create();
        $role->users()->sync([$user->id]);

        $response = $this->getJson("users/$user->id/edit");

        $this->assertSuccessDataResponse($response, $user->toArray(), 'User retrieved successfully.');
    }

    /** @test */
    public function test_can_delete_user()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->deleteJson('users/'.$user->id);

        $this->assertSuccessMessageResponse($response, 'User deleted successfully.');

        $response = $this->getJson('users/'.$user->id.'/edit');
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'User not found.',
        ]);
    }

    /** @test */
    public function test_can_resend_email_verification()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->mockRepository();

        $this->userRepo->shouldReceive('resendEmailVerification')
            ->once()
            ->with($user->id)
            ->andReturn([]);

        $response = $this->getJson("users/{$user->id}/send-email");

        $this->assertSuccessMessageResponse($response, 'Verification email has been sent successfully.');
    }

    /** @test */
    public function test_can_active_de_active_user()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->mockRepository();

        $this->userRepo->shouldReceive('activeDeActiveUser')
            ->once()
            ->with($user->id)
            ->andReturn([]);

        $response = $this->postJson("users/{$user->id}/active-de-active", []);

        $this->assertSuccessMessageResponse($response, 'User updated successfully.');
    }

    /** @test */
    public function test_can_update_profile()
    {
        /** @var User $user */
        $user = factory(User::class)->make();

        $this->mockRepository();

        $this->userRepo->shouldReceive('update')
            ->once()
            ->with($user->toArray(), getLoggedInUserId())
            ->andReturn([]);

        $response = $this->postJson("users/profile-update", $user->toArray());

        $this->assertSuccessMessageResponse($response, 'Profile updated successfully.');
    }
}

