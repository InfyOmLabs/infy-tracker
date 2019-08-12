<?php

namespace Tests\Controllers;

use App\Models\Comment;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class TaskControllerTest.
 */
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

        /** @var User $farhan */
        $farhan = factory(User::class)->create();
        $task->taskAssignee()->sync([$farhan->id]);

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();
        $task->tags()->sync([$tag->id]);

        $response = $this->getJson('tasks/'.$task->id.'/edit');

        $this->assertSuccessDataResponse($response, $task->toArray(), 'Task retrieved successfully.');

        $data = $response->original['data'];
        $projectId = $data['project']['id'];
        $tagId = $data['tags'][0]['id'];
        $taskAssigneeId = $data['taskAssignee'][0]['id'];

        $this->assertEquals($task->project_id, $projectId);
        $this->assertEquals($tag->id, $tagId);
        $this->assertEquals($farhan->id, $taskAssigneeId);
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
    public function test_can_not_delete_task_when_task_has_one_or_more_time_entries()
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
    public function test_can_update_status_of_task()
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
            ->with($task->id, [])
            ->andReturn($task->toArray());

        $response = $this->getJson("task-details/$task->id");

        $this->assertExactResponseData($response, $task->toArray(), 'Task retrieved successfully.');
    }

    /** @test */
    public function test_can_get_task_of_logged_in_user_for_given_project()
    {
        $this->mockRepository();

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $mockResponse = ['tasks' => ['id' => $task->id, 'title' => $task->title]];
        $this->taskRepository->shouldReceive('myTasks')
            ->once()
            ->with(['project_id' => $task->project_id])
            ->andReturn($mockResponse);

        $response = $this->getJson("my-tasks?project_id=$task->project_id");

        $this->assertExactResponseData($response, $mockResponse, 'My tasks retrieved successfully.');
    }

    /** @test */
    public function test_can_get_count_of_comments_for_given_task()
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
