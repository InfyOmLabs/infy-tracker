<?php

namespace Tests\Controllers\Validations;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function create_task_fails_when_title_is_not_passed()
    {
        $this->post('tasks', ['title' => ''])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function create_task_fails_when_project_id_is_not_passed()
    {
        $this->post('tasks', ['project_id' => ''])
            ->assertSessionHasErrors('project_id');
    }

    /** @test */
    public function update_task_fails_when_title_is_not_passed()
    {
        $task = factory(Task::class)->create();

        $this->put('tasks/'.$task->id, ['title' => ''])
            ->assertSessionHasErrors(['title' => 'The title field is required.']);
    }

    /** @test */
    public function update_task_fails_when_project_id_is_not_passed()
    {
        $task = factory(Task::class)->create();

        $this->put('tasks/'.$task->id, ['title' => 'random string', 'project_id' => ''])
            ->assertSessionHasErrors(['project_id' => 'The project id field is required.']);
    }

    /** @test */
    public function allow_update_task_with_valid_input()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $inputs = array_merge($task->toArray(), ['title' => 'Any Dummy Title']);

        $this->put('tasks/'.$task->id, $inputs)
            ->assertSessionHasNoErrors();

        $this->assertEquals('Any Dummy Title', $task->fresh()->title);
    }

    /** @test */
    public function allow_update_task_with_valid_project_id()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();
        $project = factory(Project::class)->create();
        $inputs = array_merge($task->toArray(), ['project_id' => $project->id]);

        $this->put('tasks/'.$task->id, $inputs)
            ->assertSessionHasNoErrors();

        $this->assertEquals($project->id, $task->fresh()->project_id);
    }

    /** @test */
    public function update_task_status()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create(['status' => Task::STATUS_ACTIVE]);

        $this->post("tasks/$task->id/update-status", [])->assertSessionHasNoErrors();

        $this->assertEquals(Task::STATUS_COMPLETED, $task->fresh()->status);
    }
}
