<?php

namespace Tests\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

/**
 * Class UserControllerTest.
 */
class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    use MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_shows_users()
    {
        $this->mockRepo([self::$project, self::$role]);

        $mockProjectResponse = [['id' => 1, 'name' => 'Dummy Project']];
        $this->projectRepository->expects('getProjectsList')
            ->andReturn($mockProjectResponse);

        $mockRoleResponse = [['id' => 1, 'name' => 'Dummy Role']];
        $this->roleRepository->expects('getRolesList')
            ->andReturn($mockRoleResponse);

        $response = $this->get(route('users.index'));

        $response->assertStatus(200)
            ->assertViewIs('users.index')
            ->assertSeeText('Users')
            ->assertSeeText('New User')
            ->assertViewHasAll(['projects' => $mockProjectResponse, 'roles' => $mockRoleResponse]);
    }

    /** @test */
    public function test_can_retrieve_user_with_its_projects_and_roles()
    {
        $vishal = factory(User::class)->create();

        /** @var User $farhan */
        $farhan = factory(User::class)->create();

        $project = factory(Project::class)->create();
        $project->users()->sync([$farhan->id]);

        $role = factory(Role::class)->create();
        $role->users()->sync([$farhan->id]);

        $response = $this->getJson(route('users.edit', $farhan->id));

        $assertData = array_merge($farhan->toArray(), [
            'project_ids' => [$project->id],
            'role_id'     => [$role->id],
        ]);
        $this->assertSuccessDataResponse($response, $assertData, 'User retrieved successfully.');
    }

    /** @test */
    public function test_can_delete_user()
    {
        /** @var User $farhan */
        $farhan = factory(User::class)->create();

        $response = $this->deleteJson(route('users.destroy', $farhan->id));

        $this->assertSuccessMessageResponse($response, 'User deleted successfully.');

        $response = $this->getJson(route('users.edit', $farhan->id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'User not found.',
        ]);
    }

    /** @test */
    public function test_can_resend_email_verification()
    {
        $this->mockRepo(self::$user);

        /** @var User $user */
        $user = factory(User::class)->create();

        $this->userRepository->expects('resendEmailVerification')->with($user->id);

        $response = $this->getJson(route('send-email', $user->id));

        $this->assertSuccessMessageResponse($response, 'Verification email has been sent successfully.');
    }

    /** @test */
    public function test_can_activate_user()
    {
        $this->mockRepo(self::$user);

        /** @var User $user */
        $user = factory(User::class)->create();

        $this->userRepository->expects('activeDeActiveUser')->with($user->id);

        $response = $this->postJson(route('active-de-active-user', $user->id), []);

        $this->assertSuccessMessageResponse($response, 'User updated successfully.');
    }

    /** @test */
    public function test_can_update_profile()
    {
        $this->mockRepo(self::$user);

        /** @var User $user */
        $user = factory(User::class)->raw();
        unset($user['email_verified_at']);
        $user['password_confirmation'] = $user['password'];

        $this->userRepository->expects('profileUpdate')->with($user);

        $response = $this->postJson(route('update-profile'), $user);

        $this->assertSuccessMessageResponse($response, 'Profile updated successfully.');
    }
}
