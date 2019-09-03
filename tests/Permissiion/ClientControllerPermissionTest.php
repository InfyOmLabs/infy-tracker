<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClientControllerPermissionTest extends TestCase
{
    use DatabaseTransactions;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function test_can_get_clients_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_clients']);

        $response = $this->getJson(route('clients.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_clients_without_permission()
    {
        $response = $this->get(route('clients.index'));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_create_client_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_clients']);

        $client = factory(Client::class)->raw();

        $response = $this->postJson(route('clients.store'), $client);

        $this->assertSuccessMessageResponse($response, 'Client created successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_create_client_without_permission()
    {
        $client = factory(Client::class)->raw();

        $response = $this->post(route('clients.store'), $client);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_update_client_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_clients']);

        /** @var Client $client */
        $client = factory(Client::class)->create();
        $updateClient = factory(Client::class)->raw(['id' => $client->id]);

        $response = $this->putJson(route('clients.update', $client->id), $updateClient);

        $this->assertSuccessMessageResponse($response, 'Client updated successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_client_without_permission()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $updateClient = factory(Client::class)->raw(['id' => $client->id]);

        $response = $this->put(route('clients.update', $client->id), $updateClient);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_delete_client_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_clients']);

        /** @var Client $client */
        $client = factory(Client::class)->create();

        $response = $this->deleteJson(route('clients.destroy', $client->id));

        $this->assertSuccessMessageResponse($response, 'Client deleted successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_delete_client_without_permission()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();

        $response = $this->delete(route('clients.destroy', $client->id));

        $response->assertStatus(403);
    }
}
