<?php

namespace Tests\Repositories;

use App\Models\TimeEntry;
use App\Repositories\DashboardRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\TestCase;

class DashboardRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var DashboardRepository */
    protected $dashboardRepo;

    private $defaultUserId = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->dashboardRepo = app(DashboardRepository::class);
        $this->signInWithDefaultAdminUser();
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

    /** @test */
    public function test_can_get_work_report_of_user_between_given_date()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $input = [
            'start_date' => $timeEntry->start_time,
            'end_date'   => $timeEntry->end_time,
            'user_id'    => $timeEntry->user_id,
        ];

        $workReport = $this->dashboardRepo->getWorkReport($input);

        $starDate = date('d-M', strtotime($input['start_date']));
        $this->assertEquals($starDate, $workReport['date'][0]);

        $projectName = $timeEntry->task->project->name;
        $this->assertEquals($projectName, $workReport['projects'][0]);

        $this->assertEquals($projectName, $workReport['data'][0]->label);

        $duration = round($timeEntry->duration / 60, 2);
        $this->assertEquals($duration, $workReport['data'][0]->data[0]);

        $this->assertEquals($timeEntry->duration, $workReport['totalRecords']);

        $datePeriod = Carbon::parse($input['start_date'])->format('d M, Y').' - '.Carbon::parse($input['end_date'])->format('d M, Y');
        $this->assertEquals($datePeriod, $workReport['label']);
    }

    /** @test */
    public function test_can_get_work_developer_daily_work_report_of_user_between_given_date()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $input = [
            'start_date' => $timeEntry->start_time,
            'end_date'   => $timeEntry->end_time,
        ];

        $workReport = $this->dashboardRepo->getDeveloperWorkReport($input);

        $this->assertEquals($timeEntry->user->name, $workReport['result'][1]->name);

        $totalHours = round($timeEntry->duration / 60, 2);
        $this->assertEquals($totalHours, $workReport['result'][1]->total_hours);

        $this->assertEquals($timeEntry->user->name, $workReport['result'][1]->name);

        $hours = Arr::pluck($workReport['result'], 'total_hours');
        $this->assertEquals($hours[1], $workReport['data']['data'][1]);

        $this->assertEquals($totalHours, $workReport['totalRecords']);

        $day = Carbon::parse($input['start_date'])->startOfDay()->format('dS M, Y').' Report';
        $this->assertEquals($day, $workReport['label']);
    }
}
