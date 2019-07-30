<?php

namespace Tests\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Repositories\ClientRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $clientRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->clientRepository = \Mockery::mock(ClientRepository::class);
        app()->instance(ClientRepository::class, $this->clientRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    /** @test */
    public function it_can_store_client()
    {
        $this->mockRepository();

        /** @var Client $client */
        $client = factory(Client::class)->make();

        $this->clientRepository->shouldReceive('create')
            ->once()
            ->with(array_merge($client->toArray(), ['created_by' => getLoggedInUserId()]))
            ->andReturn([]);

        $response = $this->postJson('clients', $client->toArray());

        $this->assertSuccessMessageResponse($response, 'Client created successfully.');
    }

    /** @test */
    public function it_can_retrieve_client()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();

        $response = $this->getJson('clients/'.$client->id.'/edit');

        $this->assertSuccessDataResponse($response, $client->toArray(), 'Client retrieved successfully.');
    }

    /** @test */
    public function it_can_update_client()
    {
        $this->mockRepository();

        /** @var Client $client */
        $client = factory(Client::class)->create();

        $this->clientRepository->shouldReceive('update')
            ->once()
            ->withArgs([
                [
                    'name'    => 'Dummy Name',
                    'email'   => 'dummy@email.com',
                    'website' => 'www.google.com',
                ],
                $client->id,
            ])
            ->andReturn([]);

        $response = $this->putJson('clients/'.$client->id, [
                'name'    => 'Dummy Name',
                'email'   => 'dummy@email.com',
                'website' => 'www.google.com',
            ]
        );

        $this->assertSuccessMessageResponse($response, 'Client updated successfully.');
    }

    /** @test */
    public function it_can_delete_client()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();

        $response = $this->deleteJson('clients/'.$client->id);

        $this->assertSuccessMessageResponse($response, 'Client deleted successfully.');

        $response = $this->getJson('clients/'.$client->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Client not found.',
        ]);
    }

    /** @test */
    public function it_can_retrieve_client_projects()
    {
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $response = $this->getJson("projects-of-client?client_id=$project->client_id");

        $this->assertSuccessDataResponse(
            $response,
            [$project->id => $project->name],
            'Projects retrieved successfully.'
        );
    }
}
