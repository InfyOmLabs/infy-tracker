<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateValueOfTaskNumberInTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $projects = \App\Models\Project::all()->pluck('id')->toArray();
            foreach ($projects as $project) {
                $tasks = \App\Models\Task::withTrashed()->whereProjectId($project)->get();
                $taskNumber = 1;
                foreach ($tasks as $task) {
                    $task->task_number = $taskNumber;
                    $task->save();
                    $taskNumber++;
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            \App\Models\Task::query()->update(['task_number' => null]);
        });
    }
}
