<?php

namespace Tests\Controllers;

use App\Models\ActivityType;
use App\Repositories\ActivityTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

class ActivityTypeControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_shows_activity_types()
    {
        $response = $this->getJson(route('activity-types.index'));

        $response->assertStatus(200)
            ->assertViewIs('activity_types.index')
            ->assertSeeText('Activity Types')
            ->assertSeeText('New Activity Type');
    }

    /** @test */
    public function it_can_store_activity_type()
    {
        $this->mockRepo(self::$activityType);

        $activityType = factory(ActivityType::class)->raw();

        $this->activityTypeRepository->expects('create')
            ->with(array_merge($activityType, ['created_by' => getLoggedInUserId()]));

        $response = $this->postJson('activity-types', $activityType);

        $this->assertSuccessMessageResponse($response, 'Activity Type created successfully.');
    }

    /** @test */
    public function it_can_retrieve_activity_type()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $response = $this->getJson('activity-types/'.$activityType->id.'/edit');

        $this->assertSuccessDataResponse($response, $activityType->toArray(), 'Activity Type retrieved successfully.');
    }

    /** @test */
    public function it_can_update_activity_type()
    {
        $this->mockRepo(self::$activityType);

        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $this->activityTypeRepository->expects('update')
            ->withArgs([['name' => 'Dummy Name'], $activityType->id]);

        $response = $this->putJson(
            'activity-types/'.$activityType->id,
            ['name' => 'Dummy Name']
        );

        $this->assertSuccessMessageResponse($response, 'Activity Type updated successfully.');
    }

    /** @test */
    public function it_can_delete_activity_type()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $response = $this->deleteJson('activity-types/'.$activityType->id);

        $this->assertSuccessMessageResponse($response, 'Activity Type deleted successfully.');

        $response = $this->getJson('activity-types/'.$activityType->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'ActivityType not found.',
        ]);
    }
}
