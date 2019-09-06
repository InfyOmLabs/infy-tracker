<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteInAllTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_types', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });
    }
}
