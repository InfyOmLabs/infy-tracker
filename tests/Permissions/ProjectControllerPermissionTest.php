<?php

namespace Tests\Permissions;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProjectControllerPermissionTest extends TestCase
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
    public function test_can_get_projects_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_projects']);

        $response = $this->getJson(route('projects.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_projects_without_permission()
    {
        $response = $this->get(route('projects.index'));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_create_project_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_projects']);

        $project = factory(Project::class)->raw();

        $response = $this->postJson(route('projects.store'), array_merge($project, ['user_ids' => []]));

        $this->assertSuccessMessageResponse($response, 'Project created successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_create_project_without_permission()
    {
        $project = factory(Project::class)->raw();

        $response = $this->post(route('projects.store'), $project);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_update_project_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_projects']);

        /** @var Project $project */
        $project = factory(Project::class)->create();
        $updateProject = factory(Project::class)->raw(['id' => $project->id]);

        $response = $this->putJson(route('projects.update', $project->id),
            array_merge($updateProject, [
                'user_ids' => [],
            ]));

        $this->assertSuccessMessageResponse($response, 'Project updated successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_project_without_permission()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();
        $updateProject = factory(Project::class)->raw(['id' => $project->id]);

        $response = $this->put(route('projects.update', $project->id), $updateProject);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_delete_project_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_projects']);

        /** @var Project $project */
        $project = factory(Project::class)->create();

        $response = $this->deleteJson(route('projects.destroy', $project->id));

        $this->assertSuccessMessageResponse($response, 'Project deleted successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_delete_project_without_permission()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $response = $this->delete(route('projects.destroy', $project->id));

        $response->assertStatus(403);
    }
}
