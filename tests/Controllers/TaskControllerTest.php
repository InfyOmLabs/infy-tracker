<?php

namespace Tests\Controllers;

use App\Models\Comment;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

/**
 * Class TaskControllerTest.
 */
class TaskControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_shows_tasks()
    {
        $this->mockRepo(self::$task);

        $mockedTaskResponse = $this->prepareTaskInputs();
        $this->taskRepository->expects('getTaskData')
            ->andReturn($mockedTaskResponse);

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200)
            ->assertViewIs('tasks.index')
            ->assertSeeText('Tasks')
            ->assertSeeText('Assign To')
            ->assertSeeText('Due Date')
            ->assertViewHasAll($mockedTaskResponse);
    }

    /** @test */
    public function it_can_shows_task_details()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create(['description' => 'N/A']);
        $prefix = substr($task->prefix_task_number, 1);

        $this->mockRepo(self::$task);

        $mockedTaskResponse = array_merge($this->prepareTaskInputs(),
            ['task' => $task, 'attachmentUrl' => url(Task::PATH)]
        );
        $this->taskRepository->expects('getTaskData')
            ->andReturn($mockedTaskResponse);

        $response = $this->getJson(route('tasks.show', $prefix));

        $response->assertStatus(200)
            ->assertViewIs('tasks.show')
            ->assertSeeText('Task Details')
            ->assertSeeText('Edit Detail')
            ->assertSeeText('No comments added yet')
            ->assertSeeText('N/A')
            ->assertViewHasAll($mockedTaskResponse);
    }

    public function prepareTaskInputs()
    {
        return [
            'assignees'     => ['id' => 1, 'name' => 'Dummy User'],
            'projects'      => ['id' => 1, 'name' => 'Dummy Project'],
            'tags'          => ['id' => 1, 'name' => 'Dummy Tag'],
            'activityTypes' => ['id' => 1, 'name' => 'Dummy ActivityType'],
            'status'        => Task::STATUS_ARR,
            'tasks'         => ['id' => 1, 'name' => 'Dummy Task'],
            'taskStatus'    => Task::STATUS_ARR,
            'priority'      => Task::PRIORITY,
            'taskBadges'    => ['id' => 1, 'name' => 'Dummy Badge'],
        ];
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
        $this->mockRepo(self::$task);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->taskRepository->expects('updateStatus')
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
        $this->mockRepo(self::$task);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->taskRepository->expects('getTaskDetails')
            ->with($task->id, [])
            ->andReturn($task->toArray());

        $response = $this->getJson("task-details/$task->id");

        $this->assertExactResponseData($response, $task->toArray(), 'Task retrieved successfully.');
    }

    /** @test */
    public function test_can_get_task_of_logged_in_user_for_given_project()
    {
        $this->mockRepo(self::$task);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $mockResponse = ['tasks' => ['id' => $task->id, 'title' => $task->title]];
        $this->taskRepository->expects('myTasks')
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
