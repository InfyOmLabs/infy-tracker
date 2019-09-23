<?php

namespace Tests\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Repositories\ClientRepository;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ClientRepositoryTest.
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
        factory(Client::class, 3)->create();

        $clients = $this->clientRepo->getClientList();

        $this->assertCount(3, $clients);

        $clients->map(function ($client) use ($clients) {
            $this->assertContains($client, $clients);
        });
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function test_can_delete_client_with_all_its_child_records()
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();

        $projects = factory(Project::class, 2)->create(['client_id' => $client->id]);

        /** @var Task $firstTask */
        $firstTask = factory(Task::class)->create(['project_id' => $projects[0]->id]);
        $secondTask = factory(Task::class)->create(['project_id' => $projects[1]->id]);

        $timeEntry = factory(TimeEntry::class)->create(['task_id' => $firstTask->id]);

        $this->clientRepo->delete($client->id);

        $this->assertEmpty(Client::find($client->id));
        $this->assertEmpty(Project::whereClientId($client->id)->first());

        $task = Task::withTrashed()->find($firstTask->id);
        $this->assertEquals($this->loggedInUserId, $task->deleted_by);

        $timeEntry = TimeEntry::withTrashed()->find($timeEntry->id);
        $this->assertEquals($this->loggedInUserId, $timeEntry->deleted_by);
    }
}
