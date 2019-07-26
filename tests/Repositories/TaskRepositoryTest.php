<?php

namespace Tests\Repositories;

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
        $tag = factory(Tag::class)->create();
        $assignees = factory(User::class)->times(2)->create();
        $task = [
            'title'       => $this->faker->title,
            'description' => $this->faker->text,
            'project_id'  => $this->faker->randomDigitNotNull,
            'due_date'    => date('Y-m-d h:i:s', strtotime('+3 days')),
            'task_number' => $this->faker->randomDigitNotNull,
            'tags'        => [$tag->id],
            'assignees'   => [$assignees[0]->id, $assignees[1]->id],
        ];
        $createdTask = $this->taskRepo->store($task);

        $getTask = Task::with(['tags', 'taskAssignee'])->findOrFail($createdTask->id);

        $this->assertEquals($task['title'], $getTask->title);
        $this->assertEquals($tag->id, $getTask->tags[0]->id);

        $this->assertCount(2, $getTask->taskAssignee);
        $this->assertEquals($assignees[0]->id, $getTask->taskAssignee[0]->id);
        $this->assertEquals($assignees[1]->id, $getTask->taskAssignee[1]->id);
    }

    /** @test */
    public function it_can_update_task_with_tags_and_its_assigned_user()
    {
        $task = factory(Task::class)->create();

        $tag = factory(Tag::class)->create();
        $assignees = factory(User::class)->times(2)->create();

        $updateTask = [
            'title'       => 'random string',
            'task_number' => $this->faker->randomDigitNotNull,
            'description' => $this->faker->text,
            'due_date'    => date('Y-m-d h:i:s', strtotime('+3 days')),
            'tags'        => [$tag->id],
            'assignees'   => [$assignees[0]->id, $assignees[1]->id],
        ];

        $updatedTask = $this->taskRepo->update($updateTask, $task->id);

        $this->assertTrue($updatedTask);

        $getTask = Task::with(['tags', 'taskAssignee'])->findOrFail($task->id);

        $this->assertEquals('random string', $getTask->title);
        $this->assertEquals($tag->id, $getTask->tags[0]->id);

        $this->assertCount(2, $getTask->taskAssignee);
        $this->assertEquals($assignees[0]->id, $getTask->taskAssignee[0]->id);
        $this->assertEquals($assignees[1]->id, $getTask->taskAssignee[1]->id);
    }
}
