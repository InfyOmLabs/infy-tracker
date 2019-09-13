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
    public function test_can_get_active_task_of_logged_in_user_for_given_project_without_permission()
    {
        $task1 = factory(Task::class)->create();
        $task1->taskAssignee()->attach($this->defaultUserId);

        $farhan = factory(User::class)->create();
        $this->actingAs($farhan);
        $activeTask = factory(Task::class)->create();
        $activeTask->taskAssignee()->attach($farhan->id);

        $completedTask = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]);
        $completedTask->taskAssignee()->attach($farhan->id);

        $tasks = $this->timeEntryRepo->getTasksByProject($activeTask->project_id);

        $this->assertCount(1, $tasks);
        $this->assertContains($activeTask->id, $tasks->keys());
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
    public function test_can_get_active_task_of_logged_in_user_having_manage_projects_permission_for_given_project()
    {
        $monika = factory(User::class)->create();

        $task1 = factory(Task::class)->create(['status' => Task::STATUS_ACTIVE]);
        $task1->taskAssignee()->attach($monika->id);

        $task2 = factory(Task::class)->create(['status' => Task::STATUS_ACTIVE, 'project_id' => $task1->project_id]);
        $task2->taskAssignee()->attach($this->defaultUserId);
        $completedTask = factory(Task::class)->create(['status' => Task::STATUS_COMPLETED]); // this should not return
        $completedTask->taskAssignee()->attach($this->defaultUserId);

        $result = $this->timeEntryRepo->getTasksByProject($task2->project_id);

        $this->assertCount(2, $result);
        $this->assertContains($task2->id, $result->keys());
    }

    /** @test */
    public function test_can_update_duration_of_time_entry_and_make_start_and_end_time_to_empty()
    {
        $timeEntry = factory(TimeEntry::class)->create();

        $result = $this->timeEntryRepo->updateTimeEntry([
            'duration'   => 120,
            'start_time' => '',
            'end_time'   => '',
        ], $timeEntry->id);

        $this->assertTrue($result);

        $timeEntry = $timeEntry->fresh();
        $this->assertEquals(120, $timeEntry->duration);
        $this->assertEmpty($timeEntry->start_time);
        $this->assertEmpty($timeEntry->end_time);
    }

    /** @test */
    public function test_can_update_duration_of_time_entry_via_stop_watch()
    {
        $timeEntry = factory(TimeEntry::class)->create();

        $result = $this->timeEntryRepo->updateTimeEntry([
            'start_time' => $timeEntry->start_time,
            'end_time'   => $timeEntry->end_time,
        ], $timeEntry->id);

        $this->assertTrue($result);
        $this->assertEquals(TimeEntry::STOPWATCH, $timeEntry->fresh()->entry_type);
    }

    /** @test */
    public function test_can_get_tasks_of_logged_in_user()
    {
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create();
        $task->taskAssignee()->sync([$user->id]);

        $loggedInUserTask1 = factory(Task::class)->create();
        $loggedInUserTask1->taskAssignee()->sync([$this->defaultUserId]);

        $loggedInUserTask2 = factory(Task::class)->create();
        $loggedInUserTask2->taskAssignee()->sync([$this->defaultUserId]);

        $result = $this->timeEntryRepo->getEntryData();

        $this->assertCount(2, $result['tasks']);
        $this->assertContains($loggedInUserTask1->title, $result['tasks']);
        $this->assertContains($loggedInUserTask2->title, $result['tasks']);
    }

    /** @test */
    public function test_can_check_updated_time_entry_time()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $input = [
            'start_time' => $this->faker->dateTime,
            'end_time'   => $this->faker->dateTime,
        ];
        $result = $this->timeEntryRepo->checkTimeUpdated($timeEntry, $input);

        $this->assertEquals(TimeEntry::VIA_FORM, $result);
        $this->assertIsNumeric($result);
    }

    /** @test */
    public function test_can_check_updated_time_entry()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $input = [
            'start_time' => $timeEntry->start_time,
            'end_time'   => $timeEntry->end_time,
        ];
        $result = $this->timeEntryRepo->checkTimeUpdated($timeEntry, $input);

        $this->assertEquals(TimeEntry::STOPWATCH, $result);
        $this->assertIsNumeric($result);
    }
}
