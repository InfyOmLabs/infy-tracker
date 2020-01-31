<?php

namespace Tests\Controllers;

use App\Models\ActivityType;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

/**
 * Class ActivityTypeControllerTest.
 */
class ActivityTypeControllerTest extends TestCase
{
    use DatabaseTransactions;
    use MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_store_activity_type()
    {
        $this->mockRepo(self::$activityType);

        $activityType = factory(ActivityType::class)->raw();

        $this->activityTypeRepository->expects('create')
            ->with(array_merge($activityType, ['created_by' => getLoggedInUserId()]));

        $response = $this->postJson(route('activity-types.store'), $activityType);

        $this->assertSuccessMessageResponse($response, 'Activity Type created successfully.');
    }

    /** @test */
    public function it_can_retrieve_activity_type()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $response = $this->getJson(route('activity-types.edit', $activityType->id));

        $this->assertSuccessDataResponse(
            $response,
            $activityType->toArray(),
            'Activity Type retrieved successfully.'
        );
    }

    /** @test */
    public function it_can_update_activity_type()
    {
        $this->mockRepo(self::$activityType);

        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $this->activityTypeRepository->expects('update')
            ->withArgs([['name' => 'Dummy Name'], $activityType->id]);

        $response = $this->putJson(route('activity-types.update', $activityType->id), [
            'name' => 'Dummy Name',
        ]);

        $this->assertSuccessMessageResponse($response, 'Activity Type updated successfully.');
    }

    /** @test */
    public function it_can_delete_activity_type()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $response = $this->deleteJson(route('activity-types.destroy', $activityType->id));

        $this->assertSuccessMessageResponse($response, 'Activity Type deleted successfully.');

        $response = $this->getJson('activity-types/'.$activityType->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'ActivityType not found.',
        ]);
    }

    /** @test */
    public function test_not_allow_to_delete_activity_type_when_time_entries_attached_with_it()
    {
        /** @var ActivityType $activityType */
        $timeEntry = factory(TimeEntry::class)->create(['user_id' => $this->loggedInUserId]);

        $response = $this->deleteJson(route('activity-types.destroy', $timeEntry->activity_type_id));

        $response->assertJson([
            'success' => false,
            'message' => 'This activity has more than one time entry, so it can\'t be deleted.',
        ]);
    }
}
