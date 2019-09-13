<?php

namespace Tests\Permissions;

use App\Models\ActivityType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ActivityTypeControllerPermissionTest extends TestCase
{
    use DatabaseTransactions;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function test_can_get_activity_types_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_activities']);

        $response = $this->getJson(route('activity-types.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_activity_types_without_permission()
    {
        $response = $this->get(route('activity-types.index'));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_create_activity_type_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_activities']);

        $activityType = factory(ActivityType::class)->raw();

        $response = $this->postJson(route('activity-types.store'), $activityType);

        $this->assertSuccessMessageResponse($response, 'Activity Type created successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_create_activity_type_without_permission()
    {
        $activityType = factory(ActivityType::class)->raw();

        $response = $this->post(route('activity-types.store'), $activityType);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_update_activity_type_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_activities']);

        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();
        $updateActivityType = factory(ActivityType::class)->raw(['id' => $activityType->id]);

        $response = $this->putJson(route('activity-types.update', $activityType->id), $updateActivityType);

        $this->assertSuccessMessageResponse($response, 'Activity Type updated successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_activity_type_without_permission()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();
        $updateActivityType = factory(ActivityType::class)->raw(['id' => $activityType->id]);

        $response = $this->put(route('activity-types.update', $activityType->id), $updateActivityType);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_delete_activity_type_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_activities']);

        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $response = $this->deleteJson(route('activity-types.destroy', $activityType->id));

        $this->assertSuccessMessageResponse($response, 'Activity Type deleted successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_delete_activity_type_without_permission()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $response = $this->delete(route('activity-types.destroy', $activityType->id));

        $response->assertStatus(403);
    }
}
