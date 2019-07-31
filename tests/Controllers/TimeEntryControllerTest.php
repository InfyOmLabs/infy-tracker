<?php

namespace Tests\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Repositories\TimeEntryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class TimeEntryControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $timeEntryRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->timeEntryRepository = \Mockery::mock(TimeEntryRepository::class);
        app()->instance(TimeEntryRepository::class, $this->timeEntryRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function test_can_get_time_entry_details()
    {
        $this->mockRepository();

        /** @var TimeEntry $timerEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $this->timeEntryRepository->shouldReceive('getTimeEntryDetail')
            ->once()
            ->with($timeEntry->id)
            ->andReturn([]);

        $response = $this->getJson("time-entries/$timeEntry->id/edit");

        $this->assertSuccessMessageResponse($response, 'Time Entry retrieved successfully.');
    }

    /** @test */
    public function test_can_delete_time_entry()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $response = $this->deleteJson('time-entries/'.$timeEntry->id);

        $response->assertJson(['success' => true]);
        $response->assertStatus(200);

        $response = $this->getJson('time-entries/'.$timeEntry->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'TimeEntry not found.',
        ]);
    }

    /** @test */
    public function test_can_get_user_last_task()
    {
        $this->mockRepository();

        $this->timeEntryRepository->shouldReceive('myLastTask')
            ->once()
            ->with()
            ->andReturn([]);

        $response = $this->getJson('user-last-task-work');

        $this->assertSuccessMessageResponse($response, 'User Task retrieved successfully.');
    }

    /** @test */
    public function test_can_get_user_task_by_project()
    {
        $this->mockRepository();

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $this->timeEntryRepository->shouldReceive('getTasksByProject')
            ->once()
            ->with($task->project_id, $task->id)
            ->andReturn([]);

        $response = $this->getJson("projects/{$task->project_id}/tasks?task_id=$task->id");

        $this->assertSuccessMessageResponse($response, 'Project Tasks retrieved successfully.');
    }
}