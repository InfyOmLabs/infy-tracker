<?php

namespace Tests\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $projectRepository;

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
        $this->projectRepository = \Mockery::mock(ProjectRepository::class);
        $this->userRepo = \Mockery::mock(UserRepository::class);
        app()->instance(ProjectRepository::class, $this->projectRepository);
        app()->instance(UserRepository::class, $this->userRepo);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
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
        $this->mockRepository();

        /** @var Project $project */
        $project = factory(Project::class)->create();

        $this->projectRepository->shouldReceive('getMyProjects')
            ->once()
            ->andReturn($project->toArray());

        $response = $this->getJson('my-projects');

        $this->assertSuccessDataResponse($response, $project->toArray(), 'Project Retrieved successfully.');
    }

    /** @test */
    public function test_get_can_users_of_given_project_ids()
    {
        $this->mockRepository();

        /** @var User $farhan */
        $farhan = factory(User::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $farhan->projects()->attach($project->id);

        $mockResponse = [$farhan->id => $farhan->name];

        $this->userRepo->shouldReceive('getUserList')
            ->once()
            ->with([$project->id])
            ->andReturn($mockResponse);

        $response = $this->getJson('users-of-projects?projectIds='.$project->id);

        $this->assertSuccessDataResponse($response, $mockResponse, 'Users Retrieved successfully.');
    }
}
