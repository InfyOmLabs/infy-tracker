<?php

use App\Models\Task;
use Illuminate\Database\Migrations\Migration;

class UpdatePriorityToExistRecordsInTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tasks = Task::get();
        /** @var Task $task */
        foreach ($tasks as $task) {
            if (empty($task->priority)) {
                $task->update(['priority' => 'medium']);
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

    }
}
