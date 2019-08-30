<?php

namespace Tests\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Repositories\ProjectRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ProjectRepositoryTest.
 */
class ProjectRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var ProjectRepository */
    protected $projectRepo;

    private $defaultUserId = 1;

    public function setUp(): void
    {
        parent::setUp();

        $this->projectRepo = app(ProjectRepository::class);
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_retrieve_all_projects()
    {
        $projects = factory(Project::class)->times(3)->create();

        $result = $this->projectRepo->getProjectsList();
        $this->assertCount(3, $result);

        $projects->map(function (Project $project) use ($result) {
            $this->assertContains($project->name, $result);
        });
    }

    /** @test */
    public function it_can_retrieve_projects_of_given_client()
    {
        $vishal = factory(Client::class)->create();
        $mitul = factory(Client::class)->create();

        $projects = factory(Project::class)->times(3)->create(['client_id' => $vishal->id]);
        factory(Project::class)->times(2)->create(['client_id' => $mitul->id]); // of another client

        $allProjects = $this->projectRepo->getProjectsList();
        $this->assertCount(5, $allProjects);

        $result = $this->projectRepo->getProjectsList($vishal->id);
        $this->assertCount(3, $result);

        $projects->map(function (Project $project) use ($result) {
            $this->assertContains($project->name, $result);
        });
    }

    /** @test */
    public function it_can_retrieve_projects_of_logged_in_user()
    {
        $mitul = factory(User::class)->create();
        $project = factory(Project::class)->create();
        $project->users()->sync([$mitul->id]);

        // projects of logged in user
        $projectIds = [];
        $projects = factory(Project::class)->times(3)->create();
        foreach ($projects as $project) {
            $project->users()->sync([$this->defaultUserId]);
            $projectIds[] = $project->id;
        }

        $totalProjects = $this->projectRepo->getProjectsList();
        $this->assertCount(4, $totalProjects);

        $myProjects = $this->projectRepo->getMyProjects();
        $this->assertCount(3, $myProjects);

        $myProjects->map(function (Project $project) use ($projectIds) {
            $this->assertEquals($project->users->first()->id, $this->defaultUserId);
            $this->assertContains($project->id, $projectIds);
        });
    }

    /** @test */
    public function test_user_with_manage_projects_permission_will_get_all_projects()
    {
        $mitul = factory(User::class)->create();
        $project = factory(Project::class)->create();
        $project->users()->sync([$mitul->id]);

        $projectOfLoggedInUser = factory(Project::class)->create();
        $projectOfLoggedInUser->users()->sync([$this->defaultUserId]);

        $allProjects = $this->projectRepo->getLoginUserAssignProjectsArr();

        $this->assertCount(2, $allProjects);
    }

    /** @test */
    public function it_can_retrieve_assigned_projects_list_array_of_logged_in_user()
    {
        $project = factory(Project::class)->create();
        $project->users()->sync([$this->defaultUserId]);

        $mitul = factory(User::class)->create();
        $projectOfLoggedInUser = factory(Project::class)->create();
        $projectOfLoggedInUser->users()->sync([$mitul->id]);
        $this->actingAs($mitul);

        $allProjects = $this->projectRepo->getLoginUserAssignProjectsArr();

        $this->assertCount(1, $allProjects);
        $this->assertContains($projectOfLoggedInUser->id, array_keys($allProjects));
    }
}
