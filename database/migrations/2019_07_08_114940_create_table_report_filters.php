<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableReportFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('report_id')->unsigned();
            $table->string('param_type');
            $table->integer('param_id')->unsigned();
            $table->timestamps();

            $table->foreign('report_id')
                ->references('id')->on('reports')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_filters');
    }
}
