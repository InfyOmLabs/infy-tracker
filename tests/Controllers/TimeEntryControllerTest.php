<?php

namespace Tests\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

class TimeEntryControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    /** @test */
    public function test_can_get_time_entry_details()
    {
        $this->mockRepo([self::$timeEntry]);

        /** @var TimeEntry $timerEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $projectId = $timeEntry->fresh()->task->project_id;
        $mockResponse = array_merge($timeEntry->toArray(), ['project_id' => $projectId]);

        $this->timeEntryRepository->expects('getTimeEntryDetail')
            ->with($timeEntry->id)
            ->andReturn($mockResponse);

        $response = $this->getJson("time-entries/$timeEntry->id/edit");

        $this->assertExactResponseData($response, $mockResponse, 'Time Entry retrieved successfully.');
    }

    /** @test */
    public function test_can_delete_time_entry()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $response = $this->deleteJson('time-entries/'.$timeEntry->id);

        $this->assertSuccessMessageResponse($response, 'TimeEntry deleted successfully.');

        $response = $this->getJson('time-entries/'.$timeEntry->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'TimeEntry not found.',
        ]);
    }

    /** @test */
    public function test_can_get_last_task_details_of_logged_in_user()
    {
        $this->mockRepo([self::$timeEntry]);

        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $mockResponse = [
            'task_id'     => $timeEntry->task_id,
            'activity_id' => $timeEntry->activity_type_id,
            'project_id'  => $timeEntry->task->project_id,
        ];

        $this->timeEntryRepository->expects('myLastTask')
            ->andReturn($mockResponse);

        $response = $this->getJson('user-last-task-work');

        $this->assertExactResponseData($response, $mockResponse, 'User Task retrieved successfully.');
    }

    /** @test */
    public function test_can_get_tasks_of_given_project()
    {
        $this->mockRepo([self::$timeEntry]);

        /** @var Task $task */
        $task = factory(Task::class)->create();

        $mockResponse = [$task->id => $task->title];

        $this->timeEntryRepository->expects('getTasksByProject')
            ->with($task->project_id, null)
            ->andReturn($mockResponse);

        $response = $this->getJson("projects/{$task->project_id}/tasks");

        $this->assertExactResponseData($response, $mockResponse, 'Project Tasks retrieved successfully.');
    }
}
