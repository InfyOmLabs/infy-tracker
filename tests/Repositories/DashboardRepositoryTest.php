<?php

namespace Tests\Repositories;

use App\Repositories\DashboardRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var DashboardRepository */
    protected $dashboardRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->dashboardRepo = app(DashboardRepository::class);
    }

    /** @test */
    public function it_can_get_start_of_the_day_and_end_of_the_day_from_given_date_with_between_days()
    {
        $startDate = date('Y-m-d h:i:s');
        $endDate = date('Y-m-d h:i:s', strtotime($startDate.'+3 days'));

        $date = $this->dashboardRepo->getDate($startDate, $endDate);

        $starOfDate = Carbon::parse($startDate)->startOfDay();
        $endOfDate = Carbon::parse($endDate)->endOfDay();

        $this->assertCount(4, $date['dateArr'], 'start date + 3days');
        $this->assertEquals($starOfDate, $date['startDate']);
        $this->assertEquals($endOfDate, $date['endDate']);
    }
}
