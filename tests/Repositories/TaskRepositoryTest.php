<?php

namespace Tests\Repositories;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
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

    public function setUp(): void
    {
        parent::setUp();
        $this->taskRepo = app(TaskRepository::class);
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
