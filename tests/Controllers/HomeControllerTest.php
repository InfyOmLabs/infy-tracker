<?php

namespace Tests\Controllers;

use App\Models\TimeEntry;
use App\Repositories\DashboardRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $dashboardRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->dashboardRepository = \Mockery::mock(DashboardRepository::class);
        app()->instance(DashboardRepository::class, $this->dashboardRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function test_can_retrieve_custom_report_of_logged_in_user()
    {
        $this->mockRepository();

        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $startTime = $timeEntry->start_time;
        $endTime = $timeEntry->end_time;
        $userId = $timeEntry->user_id;

        $this->dashboardRepository->shouldReceive('getWorkReport')
            ->once()
            ->with([
                'start_date' => $startTime,
                'end_date'   => $endTime,
                'user_id'    => $userId,
            ])
            ->andReturn([]);

        $response = $this->getJson("/users-work-report?start_date=$startTime&end_date=$endTime&user_id=$userId");

        $this->assertSuccessMessageResponse($response, 'Custom Report retrieved successfully.');
    }

    /** @test */
    public function test_can_retrieve_developer_work_report()
    {
        $this->mockRepository();

        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $startTime = $timeEntry->start_time;
        $endTime = $timeEntry->end_time;

        $this->dashboardRepository->shouldReceive('getDeveloperWorkReport')
            ->once()
            ->with([
                'start_date' => $startTime,
                'end_date'   => $endTime,
            ])
            ->andReturn([]);

        $response = $this->getJson("/developer-work-report?start_date=$startTime&end_date=$endTime");

        $this->assertSuccessMessageResponse($response, 'Daily Work Report retrieved successfully.');
    }
}
