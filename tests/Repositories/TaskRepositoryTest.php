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
        $task = factory(Task::class)
            ->states('tag', 'assignees')
            ->raw([
                'due_date' => date('Y-m-d h:i:s', strtotime('+3 days')),
            ]);

        $createdTask = $this->taskRepo->store($task);

        /** @var Task $taskRecord */
        $taskRecord = Task::with(['tags', 'taskAssignee'])->findOrFail($createdTask->id);
        $this->assertEquals($task['title'], $taskRecord->title);
        $this->assertEquals($task['tags'][0], $taskRecord->tags[0]->id);

        $assigneeIds = $taskRecord->taskAssignee->pluck('id');
        collect($task['assignees'])->map(function ($userId) use ($assigneeIds) {
            $this->assertContains($userId, $assigneeIds);
        });
    }

    /** @test */
    public function test_can_update_task_with_tags_and_assignees()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();
        $preparedTask = factory(Task::class)
            ->states('tag', 'assignees')
            ->raw([
                'title'    => 'random string',
                'due_date' => date('Y-m-d h:i:s', strtotime('+3 days')),
            ]);

        $updatedTask = $this->taskRepo->update($preparedTask, $task->id);

        $this->assertTrue($updatedTask);

        /** @var Task $taskRecord */
        $taskRecord = Task::with(['tags', 'taskAssignee'])->findOrFail($task->id);
        $this->assertEquals('random string', $taskRecord->title);
        $this->assertEquals($preparedTask['tags'][0], $taskRecord->tags[0]->id);

        $assigneeIds = $taskRecord->taskAssignee->pluck('id');
        collect($task['assignees'])->map(function ($userId) use ($assigneeIds) {
            $this->assertContains($userId, $assigneeIds);
        });
    }

    /** @test */
    public function test_can_generate_unique_task_number_when_task_project_is_different()
    {
        $task = factory(Task::class)->create();
        $updateTask = factory(Task::class)->raw(['due_date' => date('Y-m-d h:i:s', strtotime('+3 days'))]);

        $updatedTask = $this->taskRepo->update($updateTask, $task->id);

        $this->assertNotEmpty(Task::findOrFail($task->id)->pluck('task_number'));
    }

    /** @test */
    public function it_can_retrieve_task_list()
    {
        $tasks = factory(Task::class)->times(2)->create();

        $taskList = $this->taskRepo->getTaskList();

        $this->assertCount(2, $taskList);
        $tasks->map(function (Task $task) use ($taskList) {
            $this->assertContains($task->title, $taskList);
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

        $taskList = $this->taskRepo->getTaskList([$project->id]);

        $this->assertCount(2, $taskList);

        $taskIds = $taskList->keys();
        $tasks->map(function (Task $task) use ($taskList, $taskIds) {
            $this->assertContains($task->title, $taskList);
            $this->assertContains($task->id, $taskIds);
        });
    }

    /** @test */
    public function it_can_add_comment()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();
        $comment = $this->taskRepo->addComment([
            'comment' => 'random text',
            'task_id' => $task->id,
        ]);

        $this->assertNotEmpty($comment);
        $this->assertEquals('random text', $comment->comment);
        $this->assertEquals($this->defaultUserId, $comment->createdUser->id);
    }

    /** @test */
    public function test_can_return_unique_task_number()
    {
        $project = factory(Project::class)->create();
        factory(Task::class)->create([
            'project_id'  => $project->id,
            'task_number' => 3,
        ]);

        $uniqueTaskNumber = $this->taskRepo->getUniqueTaskNumber($project->id);

        $this->assertNotEmpty($uniqueTaskNumber);
        $this->assertEquals(4, $uniqueTaskNumber, '+1 index');
    }

    /** @test */
    public function test_can_return_unique_task_number_one_when_no_tasks_on_given_project()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $uniqueTaskNumber = $this->taskRepo->getUniqueTaskNumber($project->id);

        $this->assertNotEmpty($uniqueTaskNumber);
        $this->assertEquals(1, $uniqueTaskNumber);
    }

    /** @test */
    public function test_can_get_active_tasks_of_logged_in_user_for_given_project_id()
    {
        $farhan = factory(User::class)->create();
        $task = factory(Task::class)->create();
        $task->taskAssignee()->sync([$farhan->id]);

        $activeTask = factory(Task::class)->create();
        $activeTask->taskAssignee()->sync([$this->defaultUserId]);

        $completedTask = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);
        $completedTask->taskAssignee()->sync([$this->defaultUserId]);

        $myTasks = $this->taskRepo->myTasks(['project_id' => $activeTask->project_id]);

        $this->assertCount(1, $myTasks['tasks']);
        $this->assertEquals($activeTask->id, $myTasks['tasks'][0]->id);
        $this->assertEquals(Task::STATUS_ACTIVE, $myTasks['tasks'][0]->status);
        $this->assertEquals($this->defaultUserId, $activeTask->fresh()->taskAssignee[0]->id);
    }

    /** @test */
    public function test_can_get_active_task_of_logged_in_user()
    {
        $farhan = factory(User::class)->create();
        $task = factory(Task::class)->create();
        $task->taskAssignee()->sync([$farhan->id]);

        $activeTask = factory(Task::class)->create();
        $activeTask->taskAssignee()->sync([$this->defaultUserId]);

        $completedTask = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);
        $completedTask->taskAssignee()->sync([$this->defaultUserId]);

        $this->actingAs($farhan);

        $myTasks = $this->taskRepo->myTasks();

        $this->assertCount(1, $myTasks['tasks']);
        $this->assertEquals($task->id, $myTasks['tasks'][0]->id);
        $this->assertEquals(Task::STATUS_ACTIVE, $myTasks['tasks'][0]->status);
    }

    /** @test */
    public function test_user_with_manage_project_permission_can_get_all_active_tasks()
    {
        $monika = factory(User::class)->create();
        $task = factory(Task::class)->create();
        $task->taskAssignee()->sync([$monika->id]);

        $activeTask = factory(Task::class)->create();
        $activeTask->taskAssignee()->sync([$monika->id]);

        $completedTask = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);
        $completedTask->taskAssignee()->sync([$monika->id]);

        $myTasks = $this->taskRepo->myTasks();

        $this->assertCount(2, $myTasks['tasks']);
    }

    /** @test */
    public function test_can_get_only_active_tasks_of_logged_in_user_without_permission()
    {
        $activeTaskOfAnotherUser = factory(Task::class)->create();
        $activeTaskOfAnotherUser->taskAssignee()->sync([$this->defaultUserId]);

        $farhan = factory(User::class)->create();
        $this->actingAs($farhan);
        $activeTask = factory(Task::class)->create();
        $activeTask->taskAssignee()->sync([$farhan->id]);

        $completedTask = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);
        $completedTask->taskAssignee()->sync([$farhan->id]);

        $myTasks = $this->taskRepo->myTasks();

        $this->assertCount(1, $myTasks['tasks']);
    }

    /** @test */
    public function test_can_get_task_details_with_task_duration()
    {
        $task = factory(Task::class)->create();
        /** @var TimeEntry $firstTimeEntry */
        $firstTimeEntry = factory(TimeEntry::class)->create([
            'duration' => 5,
            'task_id'  => $task->id,
        ]);
        $secondTimeEntry = factory(TimeEntry::class)->create([
            'duration' => 10,
            'task_id'  => $task->id,
        ]);

        $taskDetails = $this->taskRepo->getTaskDetails($task->id);

        $this->assertEquals($task->id, $taskDetails->id);
        $this->assertEquals('00 Hours and 15 Minutes', $taskDetails->totalDuration);
        $this->assertEquals(15, $taskDetails->totalDurationMin);
    }

    /** @test */
    public function test_can_get_task_details_of_given_user()
    {
        $farhan = factory(User::class)->create();
        $task = factory(Task::class)->create();
        /** @var TimeEntry $firstEntry */
        $firstEntry = factory(TimeEntry::class)->create(['task_id' => $task->id]);
        $secondTimeEntry = factory(TimeEntry::class)->create([
            'duration' => 125,
            'task_id'  => $task->id,
            'user_id'  => $farhan->id,
        ]);

        $taskDetails = $this->taskRepo->getTaskDetails($task->id, [
            'user_id' => $farhan->id,
        ]);

        $this->assertEquals($firstEntry->task_id, $taskDetails->id);
        $this->assertEquals($farhan->id, $taskDetails->timeEntries[0]->user_id);
        $this->assertEquals('02 Hours and 05 Minutes', $taskDetails->totalDuration);
        $this->assertEquals(125, $taskDetails->totalDurationMin);
    }

    /** @test */
    public function test_can_get_task_details_of_given_user_from_start_time_and_end_time()
    {
        $task = factory(Task::class)->create();
        $farhan = factory(User::class)->create();
        /** @var TimeEntry $firstEntry */
        $firstEntry = factory(TimeEntry::class)->create([
            'duration' => 35,
            'user_id'  => $farhan->id,
            'task_id'  => $task->id,
        ]);

        $startTime = date('Y-m-d H:i:s', strtotime($firstEntry->end_time.'+1 hours'));
        $endTime = date('Y-m-d H:i:s', strtotime($startTime.'+1 hours'));
        $secondEntry = factory(TimeEntry::class)->create([
            'task_id'    => $task->id,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'duration'   => 130,
            'user_id'    => $farhan->id,
        ]);

        $taskDetails = $this->taskRepo->getTaskDetails($task->id, [
            'user_id'    => $farhan->id,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]);

        $this->assertEquals($task->id, $taskDetails->id);
        $this->assertEquals($farhan->id, $taskDetails->timeEntries[0]->user_id);
        $this->assertEquals($startTime, $taskDetails->timeEntries[0]->start_time);
        $this->assertEquals($endTime, $taskDetails->timeEntries[0]->end_time);
        $this->assertEquals('02 Hours and 10 Minutes', $taskDetails->totalDuration);
        $this->assertEquals(130, $taskDetails->totalDurationMin);
    }

    /** @test */
    public function test_can_update_task_status()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        $updatedTaskStatus = $this->taskRepo->updateStatus($task->id);

        $this->assertTrue($updatedTaskStatus);

        $task = Task::findOrFail($task->id);
        $this->assertEquals(Task::STATUS_COMPLETED, $task->status);
    }

    /** @test */
    public function test_tags_are_created_and_attached_to_given_task()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        /** @var Tag $tag */
        $tag = factory(Tag::class)->make();

        $this->taskRepo->attachTags($task, [$tag->name]);

        $attachedTag = $task->fresh()->tags;
        $this->assertNotEmpty($attachedTag);
        $this->assertEquals($tag->name, $attachedTag[0]['name']);
    }

    /** @test */
    public function test_existing_tags_are_attached_to_given_task()
    {
        /** @var Task $task */
        $task = factory(Task::class)->create();

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $this->taskRepo->attachTags($task, [$tag->id]);

        $attachedTag = $task->fresh()->tags;
        $this->assertNotEmpty($attachedTag);
        $this->assertEquals($tag->id, $attachedTag[0]['id']);
    }
}
