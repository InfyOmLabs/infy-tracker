<?php

namespace Tests\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

class ProjectControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_shows_projects()
    {
        $this->mockRepo([self::$client, self::$user]);

        $mockClientResponse = [['id' => 1, 'name' => 'Dummy Client']];
        $this->clientRepository->expects('getClientList')
            ->andReturn($mockClientResponse);

        $mockUserResponse = [['id' => 1, 'name' => 'Dummy User']];
        $this->userRepository->expects('getUserList')
            ->andReturn($mockUserResponse);

        $response = $this->get(route('projects.index'));

        $response->assertStatus(200)
            ->assertViewIs('projects.index')
            ->assertSeeText('Projects')
            ->assertSeeText('New Project')
            ->assertViewHasAll(['clients' => $mockClientResponse, 'users' => $mockUserResponse]);
    }

    /** @test */
    public function it_can_retrieve_project()
    {
        $user = factory(User::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $project->users()->sync([$user->id]);

        $response = $this->getJson('projects/'.$project->id.'/edit');

        $this->assertSuccessDataResponse($response,
            [
                'project' => $project->toArray(),
                'users'   => [$user->id],
            ],
            'Project retrieved successfully.'
        );
    }

    /** @test */
    public function test_can_delete_project()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $response = $this->deleteJson('projects/'.$project->id);

        $this->assertSuccessMessageResponse($response, 'Project deleted successfully.');

        $response = $this->getJson('projects/'.$project->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Project not found.',
        ]);
    }

    /** @test */
    public function test_can_get_projects_of_logged_in_user()
    {
        $this->mockRepo([self::$project]);

        /** @var Project $project */
        $project = factory(Project::class)->create();

        $this->projectRepository->expects('getMyProjects')
            ->andReturn($project->toArray());

        $response = $this->getJson('my-projects');

        $this->assertSuccessDataResponse($response, $project->toArray(), 'Project Retrieved successfully.');
    }

    /** @test */
    public function test_get_can_users_of_given_project_ids()
    {
        $this->mockRepo([self::$user]);

        /** @var User $farhan */
        $farhan = factory(User::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $farhan->projects()->attach($project->id);

        $mockResponse = [$farhan->id => $farhan->name];

        $this->userRepository->expects('getUserList')
            ->with([$project->id])
            ->andReturn($mockResponse);

        $response = $this->getJson('users-of-projects?projectIds='.$project->id);

        $this->assertSuccessDataResponse($response, $mockResponse, 'Users Retrieved successfully.');
    }
}
