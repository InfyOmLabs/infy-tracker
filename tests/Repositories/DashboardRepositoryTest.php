<?php

namespace Tests\Repositories;

use App\Models\TimeEntry;
use App\Models\User;
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

    public function setUp(): void
    {
        parent::setUp();
        $this->dashboardRepo = app(DashboardRepository::class);
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_should_return_start_of_the_day_and_end_of_the_day_dates_with_dates_between_it()
    {
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime($startDate.'+3 days'));

        $date = $this->dashboardRepo->getDate($startDate, $endDate);

        $starOfDate = Carbon::parse($startDate)->startOfDay();
        $endOfDate = Carbon::parse($endDate)->endOfDay();
        $this->assertCount(4, $date['dateArr'], 'start date + 3days');

        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        foreach ($date['dateArr'] as $dateValue) {
            $this->assertEquals($startDate, $dateValue);
            $startDate = date('Y-m-d', strtotime($startDate.'+1 days'));
        }
        $this->assertEquals($starOfDate, $date['startDate']);
        $this->assertEquals($endOfDate, $date['endDate']);
    }

    /** @test */
    public function test_can_get_work_report_of_user_between_given_date()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();
        $differentTimeEntry = factory(TimeEntry::class)->create();

        $input = [
            'start_date' => $timeEntry->start_time,
            'end_date'   => $timeEntry->end_time,
            'user_id'    => $timeEntry->user_id,
        ];

        $workReport = $this->dashboardRepo->getWorkReport($input);

        $starDate = date('d-M', strtotime($input['start_date']));
        $this->assertEquals($starDate, $workReport['date'][0]);

        $projectName = $timeEntry->task->project->name;
        $this->assertCount(1, $workReport['projects']);
        $this->assertEquals($projectName, $workReport['projects'][0]);
        $this->assertEquals($projectName, $workReport['data'][0]->label);

        $duration = round($timeEntry->duration / 60, 2);
        $this->assertEquals($duration, $workReport['data'][0]->data[0]);
        $this->assertEquals($timeEntry->duration, $workReport['totalRecords']);

        $datePeriod = Carbon::parse($input['start_date'])
                ->format('d M, Y').' - '.Carbon::parse($input['end_date'])
                ->format('d M, Y');
        $this->assertEquals($datePeriod, $workReport['label']);
    }

    /** @test */
    public function test_can_get_developer_daily_work_report_of_all_users_between_given_date()
    {
        /** @var TimeEntry $firstTimeEntry */
        $firstTimeEntry = factory(TimeEntry::class)->create();
        $secondTimeEntry = factory(TimeEntry::class)->create();

        $input = [
            'start_date' => $firstTimeEntry->start_time,
            'end_date'   => $secondTimeEntry->end_time,
        ];

        $workReport = $this->dashboardRepo->getDeveloperWorkReport($input);

        $this->assertCount(3, $workReport['result']);

        $firstEntryHours = round($firstTimeEntry->duration / 60, 2);
        $secondEntryHours = round($secondTimeEntry->duration / 60, 2);
        $totalHours = $firstEntryHours + $secondEntryHours;
        $this->assertEquals($firstEntryHours, $workReport['result'][1]->total_hours);
        $this->assertEquals($firstTimeEntry->user->name, $workReport['result'][1]->name);

        $hours = Arr::pluck($workReport['result'], 'total_hours');
        $this->assertEquals($hours[1], $workReport['data']['data'][1]);
        $this->assertEquals($totalHours, $workReport['totalRecords']);

        $day = Carbon::parse($input['start_date'])->startOfDay()->format('dS M, Y').' Report';
        $this->assertEquals($day, $workReport['label']);
    }

    /** @test */
    public function test_can_get_logged_in_developer_daily_work_report_between_given_date()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->actingAs($user);

        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create(['user_id' => $user->id]);

        $input = [
            'start_date' => $timeEntry->start_time,
            'end_date'   => $timeEntry->end_time,
        ];

        $workReport = $this->dashboardRepo->getDeveloperWorkReport($input);

        $totalHours = round($timeEntry->duration / 60, 2);
        $this->assertEquals($totalHours, $workReport['result'][0]->total_hours);
        $this->assertEquals($timeEntry->user->name, $workReport['result'][0]->name);

        $hours = Arr::pluck($workReport['result'], 'total_hours');
        $this->assertEquals($hours[0], $workReport['data']['data'][0]);
        $this->assertEquals($totalHours, $workReport['totalRecords']);

        $day = Carbon::parse($input['start_date'])->startOfDay()->format('dS M, Y').' Report';
        $this->assertEquals($day, $workReport['label']);
    }
}
