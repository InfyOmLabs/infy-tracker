<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskControllerPermissionTest extends TestCase
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
    public function test_can_get_tasks_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_tasks_without_permission()
    {
        $response = $this->get(route('tasks.index'));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_create_task_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        $task = factory(Task::class)->raw();

        $response = $this->postJson(route('tasks.store'), $task);

        $this->assertSuccessMessageResponse($response, 'Task created successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_create_task_without_permission()
    {
        $task = factory(Task::class)->raw();

        $response = $this->post(route('tasks.store'), $task);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_show_task_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->getJson(route('tasks.show', $task->id));

        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function test_not_allow_to_show_task_without_permission()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->get(route('tasks.show', $task->id));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_update_task_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Task $task */
        $task = factory(Task::class)->create();
        $updateTask = factory(Task::class)->raw(['id' => $task->id]);

        $response = $this->putJson(route('tasks.update', $task->id), $updateTask);

        $this->assertSuccessMessageResponse($response, 'Task updated successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_task_without_permission()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();
        $updateTask = factory(Task::class)->raw(['id' => $task->id]);

        $response = $this->put(route('tasks.update', $task->id), $updateTask);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_delete_task_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->deleteJson(route('tasks.destroy', $task->id));

        $this->assertSuccessMessageResponse($response, 'Task deleted successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_delete_task_without_permission()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->delete(route('tasks.destroy', $task->id));

        $response->assertStatus(403);
    }
}
