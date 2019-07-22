<?php

namespace Tests\Controllers\Validations;

use App\Models\ActivityType;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TimeEntryControllerTest.
 */
class TimeEntryControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function add_time_entry_fails_when_task_id_is_not_passed()
    {
        $this->post('time-entries', ['task_id' => ''])
            ->assertSessionHasErrors(['task_id' => 'The task id field is required.']);
    }

    /** @test */
    public function add_time_entry_fails_when_invalid_start_time_given()
    {
        $this->post('time-entries', ['start_time' => date('Y-m-d')])
            ->assertSessionHasErrors(['start_time' => 'The start time does not match the format Y-m-d H:i:s.']);
    }

    /** @test */
    public function add_time_entry_fails_when_start_time_is_greater_than_end_time()
    {
        $startTime = date('Y-m-d H:i:s');
        $endTime = date('Y-m-d H:i:s', strtotime('-1 day'));

        $response = $this->post(
            'time-entries',
            $this->timeEntryInputs(['start_time' => $startTime, 'end_time' => $endTime])
        );
        $this->assertEquals('Invalid start time and end time.', $response->exception->getMessage());
    }

    /** @test */
    public function add_time_entry_fails_when_start_time_is_greater_than_current_time()
    {
        $startTime = date('Y-m-d H:i:s', strtotime('+1 day'));
        $endTime = date('Y-m-d H:i:s', strtotime($startTime.'+2 days'));

        $response = $this->post(
            'time-entries',
            $this->timeEntryInputs(['start_time' => $startTime, 'end_time' => $endTime])
        );

        $this->assertEquals('Start time must be less than or equal to current time.',
            $response->exception->getMessage());
    }

    /** @test */
    public function add_time_entry_fail_when_end_time_is_greater_than_current_time()
    {
        $startTime = date('Y-m-d H:i:s');
        $endTime = date('Y-m-d H:i:s', strtotime($startTime.'+2 days'));

        $response = $this->post(
            'time-entries',
            $this->timeEntryInputs(['start_time' => $startTime, 'end_time' => $endTime])
        );

        $this->assertEquals('End time must be less than or equal to current time.', $response->exception->getMessage());
    }

    /** @test */
    public function add_time_entry_of_logged_in_user()
    {
        $inputs = $this->timeEntryInputs();
        $this->post('time-entries', $inputs)->assertSessionHasNoErrors();

        $timeEntry = TimeEntry::latest()->first();
        $this->assertNotEmpty($timeEntry);
        $this->assertEquals($inputs['start_time'], $timeEntry->start_time);
        $this->assertEquals($inputs['end_time'], $timeEntry->end_time);
        $this->assertEquals(getLoggedInUserId(), $timeEntry->user_id);
    }

    /** @test */
    public function update_time_entry_fails_when_task_id_is_not_passed()
    {
        $timeEntry = factory(TimeEntry::class)->create();

        $this->put('time-entries/'.$timeEntry->id, ['task_id' => ''])
            ->assertSessionHasErrors(['task_id' => 'The task id field is required.']);
    }

    /** @test */
    public function update_time_entry_fails_when_user_try_to_update_another_users_entry()
    {
        $timeEntry1 = factory(TimeEntry::class)->create();
        $timeEntry2 = factory(TimeEntry::class)->create(['user_id' => getLoggedInUserId()]);

        $response = $this->put('time-entries/'.$timeEntry1->id, $this->timeEntryInputs());

        $this->assertEquals('Time Entry not found.',
            $response->original['message']);
    }

    /** @test */
    public function user_can_update_his_time_entries()
    {
        $timeEntry = factory(TimeEntry::class)->create(['user_id' => getLoggedInUserId()]);

        $inputs = $this->timeEntryInputs();
        $this->put('time-entries/'.$timeEntry->id, $inputs)->assertSessionHasNoErrors();

        $timeEntry = TimeEntry::latest()->first();
        $this->assertNotEmpty($timeEntry);
        $this->assertEquals(getLoggedInUserId(), $timeEntry->user_id);
        $this->assertEquals($inputs['start_time'], $timeEntry->start_time);
        $this->assertEquals($inputs['end_time'], $timeEntry->end_time);
    }

    /**
     * @param array $input
     *
     * @return array
     */
    public function timeEntryInputs($input = [])
    {
        $activityType = factory(ActivityType::class)->create();
        $task = factory(Task::class)->create();

        $startTime = date('Y-m-d H:i:s');
        $endTime = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        return array_merge([
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'task_id'          => $task->id,
            'activity_type_id' => $activityType->id,
        ], $input);
    }
}
