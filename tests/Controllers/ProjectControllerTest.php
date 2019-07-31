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
    public function test_can_get_my_project()
    {
        $this->mockRepository();

        $this->projectRepository->shouldReceive('getMyProjects')
            ->once()
            ->with()
            ->andReturn();

        $response = $this->getJson('my-projects');

        $this->assertSuccessMessageResponse($response, 'Project Retrieved successfully.');
    }

    /** @test */
    public function test_can_get_users()
    {
        $this->mockRepository();

        /** @var Project $project */
        $projects = factory(Project::class)->times(2)->create();

        $this->userRepo->shouldReceive('getUserList')
            ->once()
            ->with([$projects[0]->id])
            ->andReturn();

        $response = $this->getJson('users-of-projects?projectIds='.$projects[0]->id);

        $this->assertSuccessMessageResponse($response, 'Users Retrieved successfully.');
    }
}
