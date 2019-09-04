<?php

use App\Models\Comment;
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

    /**
     * @test
     */
    public function test_can_update_task_status_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->postJson(route('task.update-status', $task->id), []);

        $this->assertSuccessMessageResponse($response, 'Task status Update successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_task_status_without_permission()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->post(route('task.update-status', $task->id), []);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_get_task_attachments_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->getJson(route('task.attachments', $task->id));

        $this->assertSuccessMessageResponse($response, 'Task retrieved successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_task_attachments_without_permission()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->get(route('task.attachments', $task->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_can_add_task_comment_with_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Comment $comment */
        $comment = factory(Comment::class)->create();

        $response = $this->postJson(route('task.comments', $comment->task_id), [
            'comment' => $comment->comment,
        ]);

        $this->assertSuccessMessageResponse($response, 'Comment has been added successfully.');
    }

    /** @test */
    public function test_not_allow_to_add_task_comment_without_permission()
    {
        /** @var Comment $comment */
        $comment = factory(Comment::class)->create();

        $response = $this->post(route('task.comments', $comment->task_id), [
            'comment' => $comment->comment,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_can_update_task_comment_with_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Comment $comment */
        $comment = factory(Comment::class)->create(['created_by' => $this->user->id]);
        $newText = $this->faker->text;

        $response = $this->postJson(route('task.update-comment', [$comment->task_id, $comment->id]), [
            'comment' => $newText,
        ]);

        $this->assertSuccessMessageResponse($response, 'Comment has been updated successfully.');
    }

    /** @test */
    public function test_not_allow_to_update_task_comment_without_permission()
    {
        /** @var Comment $comment */
        $comment = factory(Comment::class)->create(['created_by' => $this->user->id]);
        $newText = $this->faker->text;

        $response = $this->post(route('task.update-comment', [$comment->task_id, $comment->id]), [
            'comment' => $newText,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_can_delete_task_comment_with_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        /** @var Comment $comment */
        $comment = factory(Comment::class)->create(['created_by' => $this->user->id]);

        $response = $this->deleteJson(route('task.delete-comment', [$comment->task_id, $comment->id]));

        $this->assertSuccessMessageResponse($response, 'Comment has been deleted successfully.');
    }

    /** @test */
    public function test_not_allow_to_delete_task_comment_without_permission()
    {
        /** @var Comment $comment */
        $comment = factory(Comment::class)->create(['created_by' => $this->user->id]);

        $response = $this->delete(route('task.delete-comment', [$comment->task_id, $comment->id]));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_can_get_task_details_with_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        $task = factory(Task::class)->create();

        $response = $this->getJson(route('task.get-details', $task->id));

        $this->assertSuccessMessageResponse($response, 'Task retrieved successfully.');
    }

    /** @test */
    public function test_not_allow_to_get_task_details_without_permission()
    {
        $task = factory(Task::class)->create();

        $response = $this->get(route('task.get-details', $task->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_can_get_task_comments_count_with_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        $task = factory(Task::class)->create();

        $response = $this->getJson(route('task.comments-count', $task->id));

        $this->assertSuccessMessageResponse($response, 'Comments count retrieved successfully.');
    }

    /** @test */
    public function test_not_allow_to_get_task_comments_count_without_permission()
    {
        $task = factory(Task::class)->create();

        $response = $this->get(route('task.comments-count', $task->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_can_get_task_users_with_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_all_tasks']);

        $task = factory(Task::class)->create();

        $response = $this->getJson(route('task.users', $task->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function test_not_allow_to_get_task_users_without_permission()
    {
        $task = factory(Task::class)->create();

        $response = $this->get(route('task.users', $task->id));

        $response->assertStatus(403);
    }
}
