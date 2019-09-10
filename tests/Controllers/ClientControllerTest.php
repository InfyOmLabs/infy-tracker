<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 29-07-2019
 * Time: 03:18 PM.
 */

namespace Tests\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

/**
 * Class ClientControllerTest.
 */
class ClientControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_shows_clients()
    {
        $response = $this->getJson(route('clients.index'));

        $response->assertStatus(200)
            ->assertViewIs('clients.index')
            ->assertSeeText('Clients')
            ->assertSeeText('New Client');
    }

    /** @test */
    public function it_can_store_client()
    {
        $this->mockRepo(self::$client);

        $client = factory(Client::class)->raw();

        $this->clientRepository->expects('create')
            ->with(array_merge($client, ['created_by' => $this->loggedInUserId]));

        $response = $this->postJson('clients', $client);

        $this->assertSuccessMessageResponse($response, 'Client created successfully.');
    }

    /** @test */
    public function it_can_retrieve_client()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();

        $response = $this->getJson(route('clients.edit', $client->id));

        $this->assertSuccessDataResponse($response, $client->toArray(), 'Client retrieved successfully.');
    }

    /** @test */
    public function it_can_update_client()
    {
        $this->mockRepo(self::$client);

        $client = factory(Client::class)->create();
        $fakeClient = factory(Client::class)->raw();

        $this->clientRepository->expects('update')->withArgs([$fakeClient, $client->id]);

        $response = $this->putJson(route('clients.update', $client->id), $fakeClient);

        $this->assertSuccessMessageResponse($response, 'Client updated successfully.');
    }

    /** @test */
    public function it_can_delete_client()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $project = factory(Project::class)->create(['client_id' => $client->id]);
        $task = factory(Task::class)->create(['project_id' => $project->id]);
        $timeEntry = factory(TimeEntry::class)->create(['task_id' => $task->id]);

        $response = $this->deleteJson(route('clients.destroy', $client->id));

        $this->assertSuccessMessageResponse($response, 'Client deleted successfully.');

        //testing client deleted or not.
        $response = $this->getJson(route('clients.edit', $client->id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Client not found.',
        ]);

        //testing project deleted or not.
        $response = $this->getJson(route('projects.edit', $project->id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Project not found.',
        ]);

        //testing task deleted or not.
        $response = $this->getJson(route('tasks.edit', $task->id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Task not found.',
        ]);

        //testing timeEntry deleted or not.
        $response = $this->getJson(route('time-entries.edit', $timeEntry->id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'TimeEntry not found.',
        ]);
    }

    /** @test */
    public function test_can_retrieve_projects_of_given_client()
    {
        $this->mockRepo(self::$project);

        /** @var Client $client */
        $client = factory(Client::class)->create();

        /** @var Project $project */
        $project = factory(Project::class)->create(['client_id' => $client->id]);

        $mockResponse = ['id' => $project->id, 'name' => $project->name];

        $this->projectRepository->expects('getProjectsList')
            ->with($client->id)
            ->andReturn($mockResponse);

        $response = $this->getJson(route('projects-of-client', ['client_id' => $client->id]));

        $this->assertSuccessDataResponse($response, $mockResponse, 'Projects retrieved successfully.');
    }
}
