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
    public function test_create_task_fails_when_title_is_not_passed()
    {
        $this->post(route('tasks.store'), ['title' => ''])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function test_create_task_fails_when_project_id_is_not_passed()
    {
        $this->post(route('tasks.store'), ['project_id' => ''])
            ->assertSessionHasErrors('project_id');
    }

    /** @test */
    public function test_create_task_fail_with_non_existing_project_id()
    {
        $this->post(route('tasks.store'), ['project_id' => 999])
            ->assertSessionHasErrors();
    }

    /** @test */
    public function test_create_task_fail_with_invalid_due_date()
    {
        $task = factory(Task::class)->raw([
            'due_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        $response = $this->post(route('tasks.store'), $task);

        $this->assertExceptionMessage($response, 'due_date must be greater than today\'s date.');
    }

    /** @test */
    public function test_update_task_fails_when_title_is_not_passed()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->put(route('tasks.update', $task->id), ['title' => ''])
            ->assertSessionHasErrors(['title' => 'The title field is required.']);
    }

    /** @test */
    public function test_update_task_fails_when_project_id_is_not_passed()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->put(route('tasks.update', $task->id), ['title' => 'random string', 'project_id' => ''])
            ->assertSessionHasErrors(['project_id' => 'The project id field is required.']);
    }

    /** @test */
    public function test_update_task_fail_with_non_existing_project_id()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->put(route('tasks.update', $task->id), ['project_id' => 999])
            ->assertSessionHasErrors();
    }

    /** @test */
    public function test_update_task_fail_with_invalid_due_date()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();
        $dueDate = date('Y-m-d H:i:s', strtotime('-1 day'));

        $input = array_merge($task->toArray(), ['due_date' => $dueDate]);
        $response = $this->put(route('tasks.update', $task->id), $input);

        $this->assertExceptionMessage($response, 'due_date must be greater than today\'s date.');
    }

    /** @test */
    public function it_can_update_task_with_valid_input()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $inputs = array_merge($task->toArray(), ['title' => 'Any Dummy Title']);

        $this->put(route('tasks.update', $task->id), $inputs)
            ->assertSessionHasNoErrors();

        $this->assertEquals('Any Dummy Title', $task->fresh()->title);
    }

    /** @test */
    public function it_can_update_task_with_valid_project_id()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();
        $project = factory(Project::class)->create();
        $inputs = array_merge($task->toArray(), ['project_id' => $project->id]);

        $this->put(route('tasks.update', $task->id), $inputs)
            ->assertSessionHasNoErrors();

        $this->assertEquals($project->id, $task->fresh()->project_id);
    }

    /** @test */
    public function it_can_update_task_status_from_active_to_completed()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->post(route('task.update-status', $task->id), [])->assertSessionHasNoErrors();

        $this->assertEquals(Task::STATUS_COMPLETED, $task->fresh()->status);
    }
}
