<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('priority')->nullable();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('project_id');
            $table->integer('status')->default(0);
            $table->date('due_date')->nullable();
            $table->unsignedInteger('task_number')->nullable(false);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // foreign
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->foreign('project_id')->references('id')->on('projects');

            $table->foreign('created_by')->references('id')->on('users');

            $table->unique(['task_number', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tasks');
    }
}
