<?php

namespace Tests\Models;

use App\Models\Project;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function prefix_is_capitalized()
    {
        factory(Project::class)->create(['prefix' => 'ToDo']);

        $project = Project::first();
        $this->assertEquals('TODO', $project->prefix);
    }
}
