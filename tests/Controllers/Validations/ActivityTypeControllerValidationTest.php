<?php

namespace Tests\Controllers\Validations;

use App\Models\ActivityType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ActivityTypeControllerValidationTest.
 */
class ActivityTypeControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function create_activity_type_fails_when_name_is_not_passed()
    {
        $this->post(route('activity-types.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function update_activity_type_fails_when_name_is_not_passed()
    {
        $activityType = factory(ActivityType::class)->create();

        $this->put(route('activity-types.update', $activityType->id), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function update_activity_type_fails_when_name_is_duplicate()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $this->put(route('activity-types.update', $activityType->id), ['name' => 'Development'])
            ->assertSessionHasErrors(['name' => 'Activity type with same name already exist']);
    }

    /** @test */
    public function allow_update_activity_type_with_valid_name()
    {
        /** @var ActivityType $activityType */
        $activityType = factory(ActivityType::class)->create();

        $this->put(route('activity-types.update', $activityType->id), ['name' => 'Any Dummy Name'])
            ->assertSessionHasNoErrors();

        $this->assertEquals('Any Dummy Name', $activityType->fresh()->name);
    }
}
