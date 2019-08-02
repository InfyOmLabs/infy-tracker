<?php

namespace Tests\Controllers;

use App\Models\ActivityType;
use App\Repositories\ActivityTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class ActivityTypeControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $activityTypeRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->signInWithDefaultAdminUser();

        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->activityTypeRepository = \Mockery::mock(ActivityTypeRepository::class);
        app()->instance(ActivityTypeRepository::class, $this->activityTypeRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        \Mockery::close();
    }

    /** @test */
    public function it_can_store_activity_type()
    {
        $this->mockRepository();

        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->make();

        $this->activityTypeRepository->shouldReceive('create')
            ->once()
            ->with(array_merge($activityType->toArray(), ['created_by' => getLoggedInUserId()]));

        $response = $this->postJson('activity-types', $activityType->toArray());

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
        $this->mockRepository();

        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $this->activityTypeRepository->shouldReceive('update')
            ->once()
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
