<?php

namespace Tests\Models;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TaskTest
 */
class TaskTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function get_due_date_in_date_string_format()
    {
        factory(Task::class)->create(['due_date' => Carbon::create(2019, 07, 13)]);

        $task = Task::first();
        $this->assertEquals('2019-07-13', $task->due_date);
    }

    /** @test */
    public function get_prefix_task_number()
    {
        $project = factory(Project::class)->create(['prefix' => 'ToDo']);

        factory(Task::class)->create([
            'project_id'  => $project->id,
            'task_number' => 1,
        ]);

        $task = Task::first();
        $this->assertEquals('#TODO-1', $task->prefix_task_number);
    }

    /** @test */
    public function get_task_of_specific_project()
    {
        $project1 = factory(Project::class)->create();
        $project2 = factory(Project::class)->create();

        factory(Task::class)->create([
            'project_id'  => $project1->id,
        ]);

        $task2 = factory(Task::class)->create([
            'project_id'  => $project2->id,
        ]);

        $tasks = Task::ofProject($project2->id)->get();
        $this->assertCount(1, $tasks);

        /** @var Task $firstTask */
        $firstTask = $tasks->first();
        $this->assertEquals($task2->id, $firstTask->id);
        $this->assertEquals($project2->id, $firstTask->project_id);
    }
}
