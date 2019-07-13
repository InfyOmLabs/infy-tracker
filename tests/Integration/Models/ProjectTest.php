<?php

namespace Tests\Integration\Models;

use App\Models\Project;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function prefix_is_capitalized()
    {
        factory(Project::class)->create(['prefix' => 'ToDo']);

        $project = Project::first();
        $this->assertEquals('TODO', $project->prefix);
    }
}
