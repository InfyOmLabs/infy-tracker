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
    public function test_can_delete_client_with_all_its_child_records()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();
        $task = factory(Task::class)->create(['project_id' => $project->id]);
        $timeEntry = factory(TimeEntry::class)->create(['task_id' => $task->id]);

        $response = $this->deleteJson(route('clients.destroy', $project->client_id));

        $this->assertSuccessMessageResponse($response, 'Client deleted successfully.');

        $response = $this->getJson(route('clients.edit', $project->client_id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Client not found.',
        ]);

        $this->assertEmpty(Project::whereClientId($project->client_id)->first());
        $task = Task::withTrashed()->find($task->id);
        $this->assertEquals($this->loggedInUserId, $task->deleted_by);
        $timeEntry = TimeEntry::withTrashed()->find($timeEntry->id);
        $this->assertEquals($this->loggedInUserId, $timeEntry->deleted_by);
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
