<?php

namespace Tests\Controllers\Validations;

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

        $this->put('tasks/'.$task->id, ['project_id' => ''])
            ->assertSessionHasErrors(['project_id' => 'The project_id field is required.']);
    }

    /** @test */
    public function update_task_fails_when_title_is_duplicate()
    {
        $task1 = factory(Task::class)->create();
        $task2 = factory(Task::class)->create();

        $this->put('tasks/'.$task2->id, ['title' => $task1->title])
            ->assertSessionHasErrors(['title' => 'Task with same title already exist']);
    }

    /** @test */
    public function update_task_fails_when_project_id_is_duplicate()
    {
        $task1 = factory(Task::class)->create();
        $task2 = factory(Task::class)->create();

        $this->put('tasks/'.$task2->id, ['project_id' => $task1->project_id])
            ->assertSessionHasErrors(['project_id' => 'Task with same title already exist']);
    }

    /** @test */
    public function allow_update_task_with_valid_title()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->put('tasks/'.$task->id, ['title' => 'Any Dummy Title'])
            ->assertSessionHasNoErrors();

        $this->assertEquals('Any Dummy Title', $task->fresh()->title);
    }

    /** @test */
    public function allow_update_task_with_valid_project_id()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->put('tasks/'.$task->id, ['project_id' => 1])
            ->assertSessionHasNoErrors();

        $this->assertEquals(1, $task->fresh()->project_id);
    }

    /** @test */
    public function update_task_status()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);

        $this->post("tasks/$task->id/update-status", [])->assertSessionHasNoErrors();

        $this->assertEquals(Task::STATUS_ACTIVE, $task->fresh()->status);
    }
}
