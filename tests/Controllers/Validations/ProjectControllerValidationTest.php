<?php

namespace Tests\Controllers\Validations;

use App\Models\Project;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ProjectControllerValidationTest.
 */
class ProjectControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function create_project_fails_when_name_is_not_passed()
    {
        $this->post(route('projects.store'), ['name' => ''])->assertSessionHasErrors('name');
    }

    /** @test */
    public function create_project_fails_when_name_is_duplicated()
    {
        $project = factory(Project::class)->create();

        $this->post(route('projects.store'), ['name' => $project->name])
            ->assertSessionHasErrors(['name' => 'Project with same name already exist.']);
    }

    /** @test */
    public function update_project_fails_when_name_is_not_passed()
    {
        $project = factory(Project::class)->create();

        $this->put(route('projects.update', $project->id), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function update_project_fails_when_name_is_duplicated()
    {
        $project1 = factory(Project::class)->create();
        $project2 = factory(Project::class)->create();

        $inputs = array_merge($project2->toArray(), ['user_ids' => [], 'name' => $project1->name]);
        $this->put(route('projects.update', $project2->id), $inputs)
            ->assertSessionHasErrors(['name' => 'Project with same name already exist.']);
    }

    /** @test */
    public function allow_update_project_with_valid_input()
    {
        $project = factory(Project::class)->create();

        $inputs = array_merge($project->toArray(), ['user_ids' => [], 'name' => 'Dummy Project Name']);
        $this->put(route('projects.update', $project->id), $inputs)
            ->assertSessionHasNoErrors();
    }
}
