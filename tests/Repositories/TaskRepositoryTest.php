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
    public function it_can_store_task_with_tags_and_its_assigned_user()
    {
        $task = $this->generateTaskInputs();

        $createdTask = $this->taskRepo->store($task);

        $getTask = Task::with(['tags', 'taskAssignee'])->findOrFail($createdTask->id);

        $this->assertEquals($task['title'], $getTask->title);
        $this->assertEquals($task['tags'][0], $getTask->tags[0]->id);

        $this->assertCount(2, $getTask->taskAssignee);
        $this->assertEquals($task['assignees'][0], $getTask->taskAssignee[0]->id);
        $this->assertEquals($task['assignees'][1], $getTask->taskAssignee[1]->id);
    }

    /** @test */
    public function it_can_update_task_with_tags_and_its_assigned_user()
    {
        $task = factory(Task::class)->create();

        $prepareTask = $this->generateTaskInputs(['title' => 'random string']);

        $updatedTask = $this->taskRepo->update($prepareTask, $task->id);

        $this->assertTrue($updatedTask);

        $getTask = Task::with(['tags', 'taskAssignee'])->findOrFail($task->id);

        $this->assertEquals('random string', $getTask->title);
        $this->assertEquals($prepareTask['tags'][0], $getTask->tags[0]->id);

        $this->assertCount(2, $getTask->taskAssignee);
        $this->assertEquals($prepareTask['assignees'][0], $getTask->taskAssignee[0]->id);
        $this->assertEquals($prepareTask['assignees'][1], $getTask->taskAssignee[1]->id);
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
            'due_date'    => date('Y-m-d h:i:s', strtotime("+3 days")),
            'task_number' => $this->faker->randomDigitNotNull,
            'tags'        => [$tag->id],
            'assignees'   => [$assignees[0]->id, $assignees[1]->id],
        ], $task);
    }
}
