<?php

namespace Tests\Repositories;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TaskRepositoryTest.
 */
class TaskRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var TaskRepository */
    protected $taskRepo;

    private $defaultUserId = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->taskRepo = app(TaskRepository::class);
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_can_store_task_with_tags_and_assignees()
    {
        $task = $this->generateTaskInputs();
        $createdTask = $this->taskRepo->store($task);

        $getTask = Task::with(['tags', 'taskAssignee'])->findOrFail($createdTask->id);
        $this->assertEquals($task['title'], $getTask->title);
        $this->assertEquals($task['tags'][0], $getTask->tags[0]->id);

        $assignees = $createdTask->fresh()->taskAssignee;
        $pluckAssigneeIds = $getTask->taskAssignee->pluck('id');
        $assignees->map(function (User $user) use ($pluckAssigneeIds) {
            $this->assertContains($user->id, $pluckAssigneeIds);
        });
    }

    /** @test */
    public function test_can_update_task_with_tags_and_assignees()
    {
        $task = factory(Task::class)->create();
        $prepareTask = $this->generateTaskInputs(['title' => 'random string']);

        $updatedTask = $this->taskRepo->update($prepareTask, $task->id);

        $this->assertTrue($updatedTask);

        $getTask = Task::with(['tags', 'taskAssignee'])->findOrFail($task->id);
        $this->assertEquals('random string', $getTask->title);
        $this->assertEquals($prepareTask['tags'][0], $getTask->tags[0]->id);

        $assignees = $task->fresh()->taskAssignee;
        $pluckAssigneeIds = $getTask->taskAssignee->pluck('id');
        $assignees->map(function (User $user) use ($pluckAssigneeIds) {
            $this->assertContains($user->id, $pluckAssigneeIds);
        });
    }

    /** @test */
    public function it_can_retrieve_task_list()
    {
        $tasks = factory(Task::class)->times(2)->create();

        $getTask = $this->taskRepo->getTaskList();

        $this->assertCount(2, $getTask);
        $tasks->map(function (Task $task) use ($getTask) {
            $this->assertContains($task->title, $getTask);
        });
    }

    /** @test */
    public function it_can_retrieve_task_list_of_given_projects()
    {
        // task with different project
        factory(Task::class)->create();

        $project = factory(Project::class)->create();
        $tasks = factory(Task::class)->times(2)->create([
            'project_id' => $project->id,
        ]);

        $getTask = $this->taskRepo->getTaskList([$project->id => $project->name]);

        $this->assertCount(2, $getTask);

        $taskIds = $getTask->keys();
        $tasks->map(function (Task $task) use ($getTask, $taskIds) {
            $this->assertContains($task->title, $getTask);
            $this->assertContains($task->id, $taskIds);
        });
    }

    /** @test */
    public function it_can_add_comment()
    {
        $task = factory(Task::class)->create();
        $getComment = $this->taskRepo->addComment([
            'comment' => 'random text',
            'task_id' => $task->id,
        ]);

        $this->assertNotEmpty($getComment);
        $this->assertEquals('random text', $getComment->comment);
        $this->assertEquals($this->defaultUserId, $getComment->createdUser->id);
    }

    /** @test */
    public function test_can_return_unique_task_index()
    {
        $project = factory(Project::class)->create();
        factory(Task::class)->create([
            'project_id'  => $project->id,
            'task_number' => 3,
        ]);
        $getUniqueIndex = $this->taskRepo->getIndex($project->id);

        $this->assertNotEmpty($getUniqueIndex);
        $this->assertEquals(4, $getUniqueIndex, '+1 index');
    }

    /** @test */
    public function test_can_return_task_number_one_when_no_tasks_on_given_project()
    {
        $project = factory(Project::class)->create();
        factory(Task::class)->create([
            'project_id'  => $project->id,
            'task_number' => '',
        ]);
        $getUniqueIndex = $this->taskRepo->getIndex($project->id);

        $this->assertNotEmpty($getUniqueIndex);
        $this->assertEquals(1, $getUniqueIndex);
    }

    /** @test */
    public function test_can_get_tasks_of_logged_in_user_for_given_project_id()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create();
        $task->taskAssignee()->sync([$user->id]);

        $loggedInUserTask1 = factory(Task::class)->create();
        $loggedInUserTask1->taskAssignee()->sync([$this->defaultUserId]);

        $loggedInUserTask2 = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);
        $loggedInUserTask2->taskAssignee()->sync([$this->defaultUserId]);

        $getMyTasks = $this->taskRepo->myTasks(['project_id' => $loggedInUserTask1->project_id]);

        $this->assertCount(1, $getMyTasks['tasks']);
        $this->assertEquals($loggedInUserTask1->id, $getMyTasks['tasks'][0]->id);
        $this->assertNotEquals(Task::STATUS_COMPLETED, $getMyTasks['tasks'][0]->status);
        $this->assertEquals($this->defaultUserId, $loggedInUserTask1->fresh()->taskAssignee[0]->id);
    }

    /** @test */
    public function test_can_get_task_of_logged_in_user()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create();
        $task->taskAssignee()->sync([$user->id]);

        $loggedInUserTask1 = factory(Task::class)->create();
        $loggedInUserTask1->taskAssignee()->sync([$this->defaultUserId]);

        $loggedInUserTask2 = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);
        $loggedInUserTask2->taskAssignee()->sync([$this->defaultUserId]);

        $getMyTasks = $this->taskRepo->myTasks();

        $this->assertCount(1, $getMyTasks['tasks']);
        $this->assertEquals($loggedInUserTask1->id, $getMyTasks['tasks'][0]->id);
        $this->assertEquals($loggedInUserTask1->title, $getMyTasks['tasks'][0]->title);
    }

    /** @test */
    public function test_can_get_task_details_with_task_duration()
    {
        $timeEntry = factory(TimeEntry::class)->create(['duration' => 5]);

        $getMyTask = $this->taskRepo->getTaskDetails($timeEntry->task_id);

        $this->assertEquals($timeEntry->task_id, $getMyTask->id);
        $this->assertEquals('00 Hours and 05 Minutes', $getMyTask->totalDuration);
    }

    /**
     * @param array $task
     *
     * @return array
     */
    public function generateTaskInputs($task = [])
    {
        $tag = factory(Tag::class)->create();
        $project = factory(Project::class)->create();
        $assignees = factory(User::class)->times(2)->create();

        return array_merge([
            'title'       => $this->faker->title,
            'description' => $this->faker->text,
            'project_id'  => $project->id,
            'due_date'    => date('Y-m-d h:i:s', strtotime('+3 days')),
            'task_number' => $this->faker->randomDigitNotNull,
            'tags'        => [$tag->id],
            'assignees'   => [$assignees[0]->id, $assignees[1]->id],
        ], $task);
    }
}
