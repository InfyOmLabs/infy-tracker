<?php

namespace Tests\Repositories;

use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ClientRepositoryTest
 * @package Tests\Repositories
 */
class ClientRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var ClientRepository */
    protected $clientRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->clientRepo = app(ClientRepository::class);
    }

    /** @test */
    public function it_can_retrieve_clients_list()
    {
        factory(Client::class)->times(3)->create();

        $clients = $this->clientRepo->getClientList();
        $this->assertCount(3, $clients);

        $clients->map(function ($client) use ($clients) {
            $this->assertContains($client, $clients);
        });
    }
}