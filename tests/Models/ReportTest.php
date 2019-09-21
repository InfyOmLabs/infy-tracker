<?php

namespace Tests\Models;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_return_report_formatted_date_with_same_start_date_and_end_date()
    {
        $date = date('Y-m-d H:i:s', strtotime('-1 day'));
        factory(Report::class)->create([
            'start_date' => $date,
            'end_date'   => $date,
        ]);

        $report = Report::first();

        $this->assertNotEmpty($report->formatted_date);
        $formattedDate = Carbon::parse($date)->format('jS M Y');
        $this->assertEquals($formattedDate, $report->formatted_date);
    }

    /** @test */
    public function test_return_report_formatted_date_return_month_with_year()
    {
        $startOfMonth = Carbon::parse(now())->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::parse(now())->endOfMonth()->format('Y-m-d');
        factory(Report::class)->create([
            'start_date' => $startOfMonth,
            'end_date'   => $endOfMonth,
        ]);

        $report = Report::first();

        $this->assertNotEmpty($report->formatted_date);
        $formattedDate = Carbon::parse($startOfMonth)->format('M Y');
        $this->assertEquals($formattedDate, $report->formatted_date);
    }

    /** @test */
    public function test_return_report_formatted_date_return_between_two_same_date_of_month()
    {
        $startDate = Carbon::now()->subDays(1);
        $endDate = Carbon::parse($startDate)->subDays(1);
        factory(Report::class)->create([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $report = Report::first();

        $this->assertNotEmpty($report->formatted_date);
        $formattedDate = Carbon::parse($startDate)->format('jS').' - '.Carbon::parse($endDate)->format('jS M Y');
        $this->assertEquals($formattedDate, $report->formatted_date);
    }

    /** @test */
    public function test_return_report_formatted_date_with_month_name()
    {
        $startDate = Carbon::now()->subDays(1);
        $endDate = Carbon::parse($startDate)->months(1);
        factory(Report::class)->create([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $report = Report::first();

        $this->assertNotEmpty($report->formatted_date);
        $formattedDate = Carbon::parse($startDate)->format('jS M').' - '.Carbon::parse($endDate)->format('jS M Y');
        $this->assertEquals($formattedDate, $report->formatted_date);
    }
}
