<?php

namespace Tests\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

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

        $response = $this->getJson("users/$farhan->id/edit");

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

        $response = $this->deleteJson('users/'.$farhan->id);

        $this->assertSuccessMessageResponse($response, 'User deleted successfully.');

        $response = $this->getJson('users/'.$farhan->id.'/edit');
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

        $this->mockRepo(self::$user);

        $this->userRepository->expects('resendEmailVerification')
            ->with($user->id);

        $response = $this->getJson("users/$user->id/send-email");

        $this->assertSuccessMessageResponse($response, 'Verification email has been sent successfully.');
    }

    /** @test */
    public function test_can_activate_user()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->mockRepo(self::$user);

        $this->userRepository->expects('activeDeActiveUser')
            ->with($user->id);

        $response = $this->postJson("users/$user->id/active-de-active", []);

        $this->assertSuccessMessageResponse($response, 'User updated successfully.');
    }

    /** @test */
    public function test_can_update_profile()
    {
        /** @var User $user */
        $user = factory(User::class)->raw();
        unset($user['email_verified_at']);
        $user['password_confirmation'] = $user['password'];

        $this->mockRepo(self::$user);

        $this->userRepository->expects('profileUpdate')
            ->with($user);

        $response = $this->postJson('users/profile-update', $user);

        $this->assertSuccessMessageResponse($response, 'Profile updated successfully.');
    }
}
