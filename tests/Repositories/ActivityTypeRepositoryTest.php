<?php

namespace Tests\Repositories;

use App\Models\ActivityType;
use App\Repositories\ActivityTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class ActivityTypeRepositoryTest.
 */
class ActivityTypeRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var ActivityTypeRepository */
    protected $activityTypeRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->activityTypeRepo = app(ActivityTypeRepository::class);
    }

    /** @test */
    public function it_can_retrieve_activity_types_list()
    {
        /** @var Collection $activityTypes */
        $activityTypes = factory(ActivityType::class, 3)->create();

        $activityTypesResult = $this->activityTypeRepo->getActivityTypeList();

        // we already have 5 default activity types via seeder
        $this->assertCount(8, $activityTypesResult);

        $activityTypes->map(function (ActivityType $activityType) use ($activityTypesResult) {
            $this->assertContains($activityType->name, $activityTypesResult);
        });
    }
}
