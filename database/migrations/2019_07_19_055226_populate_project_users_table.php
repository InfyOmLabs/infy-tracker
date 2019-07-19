<?php

use App\Models\Task;
use Illuminate\Database\Migrations\Migration;

class PopulateProjectUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tasks = Task::with(['taskAssignee', 'project'])->get();

        /** @var Task $task */
        foreach ($tasks as $task) {
            // 1. get all assignee of task
            $taskAssignee = $task->taskAssignee->pluck('id')->toArray();
            // 2. get all assignee of that task's project
            $projectAssignee = $task->project->users->pluck('id')->toArray();

            // 3. now if that project is not assigned to assignee then assigned to them.
            foreach ($taskAssignee as $assigneeId) {
                if (!in_array($assigneeId, $projectAssignee)) {
                    $task->project->users()->attach($assigneeId);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
