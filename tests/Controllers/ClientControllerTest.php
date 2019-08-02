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
use App\Repositories\ClientRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Class ClientControllerTest.
 */
class ClientControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $clientRepository;

    protected $defaultUserId = 1;

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

        $client = factory(Client::class)->make()->toArray();

        $this->clientRepository->shouldReceive('create')
            ->once()
            ->with(array_merge($client, ['created_by' => $this->loggedInUserId]));

        $response = $this->postJson('clients', $client);

        $this->assertSuccessMessageResponse($response, 'Client created successfully.');
    }

    /** @test */
    public function it_can_retrieve_client()
    {
        $client = factory(Client::class)->create();

        $response = $this->getJson('clients/'.$client->id.'/edit');

        $this->assertSuccessDataResponse($response, $client->toArray(), 'Client retrieved successfully.');
    }

    /** @test */
    public function it_can_update_client()
    {
        $this->mockRepository();

        $client = factory(Client::class)->create();
        $fakeClient = factory(Client::class)->make()->toArray();

        $this->clientRepository->shouldReceive('update')
            ->once()
            ->withArgs([$fakeClient, $client->id]);

        $response = $this->putJson(
            'clients/'.$client->id,
            $fakeClient
        );

        $this->assertSuccessMessageResponse($response, 'Client updated successfully.');
    }

    /** @test */
    public function it_can_delete_client()
    {
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
}
