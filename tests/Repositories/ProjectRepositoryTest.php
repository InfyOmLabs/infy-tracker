<?php

namespace Tests\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class ProjectRepositoryTest.
 */
class ProjectRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var ProjectRepository */
    protected $projectRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->projectRepo = app(ProjectRepository::class);
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function get_all_projects_list()
    {
        $projects = factory(Project::class)->times(3)->create();

        $result = $this->projectRepo->getProjectsList();
        $this->assertCount(3, $result);

        $projects->map(function (Project $project) use ($result) {
            $this->assertContains($project->name, $result);
        });
    }

    /** @test */
    public function get_projects_of_particular_client()
    {
        $client = factory(Client::class)->create();

        $projects = factory(Project::class)->times(3)->create(['client_id' => $client->id]);
        factory(Project::class)->times(2); // of another client

        $result = $this->projectRepo->getProjectsList($client->id);
        $this->assertCount(3, $projects);

        $projects->map(function (Project $project) use ($result) {
            $this->assertContains($project->name, $result);
        });
    }

    /** @test */
    public function get_projects_of_logged_in_user()
    {
        $anotherUser = factory(User::class)->create();
        $project = factory(Project::class)->create();
        $project->users()->sync([$anotherUser->id]);

        // projects of logged in user
        $projectIds = [];
        $projects = factory(Project::class)->times(3)->create();
        foreach ($projects as $project) {
            $project->users()->sync([getLoggedInUserId()]);
            $projectIds[] = $project->id;
        }

        /** @var Collection $myProjects */
        $myProjects = $this->projectRepo->getMyProjects();
        $this->assertCount(3, $myProjects);

        $myProjects->map(function (Project $project) use ($projectIds) {
            $this->assertEquals($project->users->first()->id, getLoggedInUserId());
            $this->assertContains($project->id, $projectIds);
        });
    }

    /** @test */
    public function get_projects_list_array_of_logged_in_user()
    {
        $anotherUser = factory(User::class)->create();
        $project = factory(Project::class)->create();
        $project->users()->sync([$anotherUser->id]);

        // projects of logged in user
        $projectsOfLoggedInUser = factory(Project::class)->create();
        $projectsOfLoggedInUser->users()->sync([getLoggedInUserId()]);

        $myProjects = $this->projectRepo->getLoginUserAssignProjectsArr();
        $this->assertCount(1, $myProjects);

        $this->assertArrayHasKey($projectsOfLoggedInUser->id, $myProjects);
        $this->assertContains($projectsOfLoggedInUser->name, $myProjects);
    }
}
