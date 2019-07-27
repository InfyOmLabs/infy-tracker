<?php

namespace Tests\Repositories;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use App\Repositories\TimeEntryRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TimeEntryRepositoryTest.
 */
class TimeEntryRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var TimeEntryRepository */
    protected $timeEntryRepo;

    private $defaultUserId = 1;

    public function setUp(): void
    {
        parent::setUp();

        $this->timeEntryRepo = app(TimeEntryRepository::class);
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_can_return_latest_time_entry_details_of_logged_in_user()
    {
        $vishal = factory(User::class)->create();

        $timeEntry1 = factory(TimeEntry::class)->create(['user_id' => $this->defaultUserId]);
        $this->mockTime(Carbon::now()->addHour());
        $timeEntry2 = factory(TimeEntry::class)->create(['user_id' => $this->defaultUserId]);
        $timeEntry3 = factory(TimeEntry::class)->create(['user_id' => $vishal->id]);

        $result = $this->timeEntryRepo->myLastTask();

        $this->assertNotEmpty($result);
        $this->assertEquals($timeEntry2->task_id, $result['task_id']);
        $this->assertEquals($timeEntry2->activity_type_id, $result['activity_id']);
        $this->assertEquals($timeEntry2->task->project->id, $result['project_id']);
    }

    /** @test */
    public function test_can_get_details_of_given_time_entry()
    {
        $timeEntry1 = factory(TimeEntry::class)->create();
        $timeEntry2 = factory(TimeEntry::class)->create();

        $result = $this->timeEntryRepo->getTimeEntryDetail($timeEntry1->id);

        $this->assertNotEmpty($result);
        $this->assertEquals($timeEntry1->id, $result->id);
        $this->assertEquals($timeEntry1->task->project->id, $result->project_id);
    }

    /** @test */
    public function test_can_get_active_task_of_logged_in_user_for_given_project()
    {
        $vishal = factory(User::class)->create();
        $task1 = factory(Task::class)->create(['status' => Task::STATUS_ACTIVE]);
        $task1->taskAssignee()->attach($vishal->id);

        $task2 = factory(Task::class)->create(['status' => Task::STATUS_ACTIVE]);
        $task2->taskAssignee()->attach($this->defaultUserId);
        $completedTask = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]); // this should not return
        $completedTask->taskAssignee()->attach($this->defaultUserId);

        $result = $this->timeEntryRepo->getTasksByProject($task2->project_id);
        $this->assertCount(1, $result);
        $this->assertContains($task2->id, $result->keys());
    }

    /** @test */
    public function test_can_get_specific_active_task_of_logged_in_user_for_given_project()
    {
        $task1 = factory(Task::class)->create(['status' => Task::STATUS_ACTIVE]);
        $task1->taskAssignee()->attach($this->defaultUserId);
        $task2 = factory(Task::class)->create(['status' => Task::STATUS_ACTIVE]);
        $task2->taskAssignee()->attach($this->defaultUserId);

        $result = $this->timeEntryRepo->getTasksByProject($task2->project_id, $task2->id);
        $this->assertCount(1, $result);
        $this->assertContains($task2->id, $result->keys());
    }

    /** @test */
    public function test_can_update_duration_of_time_entry_and_make_start_and_end_time_to_empty()
    {
        $timeEntry = factory(TimeEntry::class)->create();

        $result = $this->timeEntryRepo->updateTimeEntry(
            ['duration' => 120, 'start_time' => '', 'end_time' => ''], $timeEntry->id
        );
        $this->assertTrue($result);

        $timeEntry = $timeEntry->fresh();
        $this->assertEquals(120, $timeEntry->duration);
        $this->assertEmpty($timeEntry->start_time);
        $this->assertEmpty($timeEntry->end_time);
    }
}
