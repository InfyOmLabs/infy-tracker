<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;

class AddPrefixValueToExistRecordInProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projects = Project::get();
        /** @var Project $project */
        foreach ($projects as $project) {
            $projectName = preg_replace('/\s+/', '', $project->name);
            $projectName = substr(trim(str_replace('-', '', $projectName)), 0, 10);
            $project->update(['prefix' => strtoupper($projectName)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Project::query()->update(['prefix' => '']);
    }
}
