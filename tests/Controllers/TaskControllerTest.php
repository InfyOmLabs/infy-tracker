<?php

namespace Tests\Controllers;

use App\Models\Comment;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TimeEntry;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $taskRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->taskRepository = \Mockery::mock(TaskRepository::class);
        app()->instance(TaskRepository::class, $this->taskRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function test_can_retrieve_task()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $user = factory(User::class)->create();
        $task->taskAssignee()->sync([$user->id]);

        $tag = factory(Tag::class)->create();
        $task->tags()->sync([$tag->id]);

        $response = $this->getJson('tasks/'.$task->id.'/edit');

        $this->assertSuccessDataResponse($response, $task->toArray(), 'Task retrieved successfully.');
    }

    /** @test */
    public function test_can_delete_task()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $response = $this->deleteJson('tasks/'.$task->id);

        $this->assertSuccessMessageResponse($response, 'Task deleted successfully.');

        $response = $this->getJson('tasks/'.$task->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Task not found.',
        ]);
    }

    /** @test */
    public function test_can_not_delete_task_when_task_has_time_entries()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $response = $this->deleteJson('tasks/'.$timeEntry->task_id);

        $response->assertJson([
            'success' => false,
            'message' => 'Task has one or more time entries.',
        ]);
    }

    /** @test */
    public function test_can_update_status()
    {
        $this->mockRepository();

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->taskRepository->shouldReceive('updateStatus')
            ->once()
            ->with($task->id);

        $response = $this->postJson("tasks/$task->id/update-status");
        $response->assertJson([
            'success' => true,
            'message' => 'Task status Update successfully.',
        ]);
    }

    /** @test */
    public function test_can_get_task_details()
    {
        $this->mockRepository();

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->taskRepository->shouldReceive('getTaskDetails')
            ->once()
            ->with($task->id);

        $response = $this->getJson("task-details/$task->id");
        $response->assertJson([
            'success' => true,
            'message' => 'Task retrieved successfully.',
        ]);
    }

    /** @test */
    public function test_can_get_my_tasks()
    {
        $this->mockRepository();

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->taskRepository->shouldReceive('myTasks')
            ->once()
            ->with(['project_id' => $task->project_id]);

        $response = $this->getJson("my-tasks?project_id=$task->project_id");
        $response->assertStatus(200);
    }

    /** @test */
    public function test_can_delete_attachment()
    {
        $this->mockRepository();

        /** @var TaskAttachment $taskAttachment */
        $taskAttachment = factory(TaskAttachment::class)->create();

        $this->taskRepository->shouldReceive('deleteFile')
            ->once()
            ->with($taskAttachment->id);

        $response = $this->postJson("tasks/{$taskAttachment->id}/delete-attachment", []);

        $this->assertSuccessMessageResponse($response, 'File has been deleted successfully.');
    }

    /** @test */
    public function test_can_get_attachments()
    {
        $this->mockRepository();

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->taskRepository->shouldReceive('getAttachments')
            ->once()
            ->with($task->id);

        $response = $this->getJson("tasks/{$task->id}/get-attachments");

        $this->assertSuccessMessageResponse($response, 'Task retrieved successfully.');
    }

    /** @test */
    public function test_can_count_task_comments()
    {
        /** @var Comment $comment */
        $comment = factory(Comment::class)->create();

        $response = $this->getJson("tasks/{$comment->task_id}/comments-count");

        $response->assertJson([
            'success' => true,
            'data'    => 1,
            'message' => 'Comments count retrieved successfully.',
        ]);
    }
}
