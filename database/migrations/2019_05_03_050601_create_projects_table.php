<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->unsignedInteger('client_id')->nullable();
            $table->text('description');
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            //foreign
            $table->foreign('client_id')->references('id')->on('clients')
                ->onDelete('set null')
                ->onUpdate('set null');

            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('set null');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::drop('projects');
    }
}
