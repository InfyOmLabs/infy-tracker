<?php

namespace Tests\Integration\Models;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
            'project_id' => $project->id,
            'task_number' => 1,
        ]);

        $task = Task::first();
        $this->assertEquals('#TODO-1', $task->prefix_task_number);
    }
}
