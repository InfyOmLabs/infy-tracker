<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTimeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned();
            $table->unsignedInteger('activity_type_id');
            $table->unsignedInteger('user_id');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->integer('duration');
            $table->text('note');
            $table->unsignedInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // foreign
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->foreign('task_id')->references('id')->on('tasks');

            $table->foreign('activity_type_id')->references('id')->on('activity_types');

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('time_entries');
    }
}
