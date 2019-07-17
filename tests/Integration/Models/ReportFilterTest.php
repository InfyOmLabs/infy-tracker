<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 17-07-2019
 * Time: 02:44 PM
 */

namespace Tests\Integration\Models;

use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
class ReportFilterTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function get_filters_of_specific_report()
    {
        $report1 = factory(Report::class)->create();
        $report2 = factory(Report::class)->create();

        $reportFilter1 = factory(ReportFilter::class)->create(['report_id' => $report1->id]);
        $reportFilter2 = factory(ReportFilter::class)->create(['report_id' => $report2->id]);

        $reportFilters = ReportFilter::ofReport($report2->id)->get();
        $this->assertCount(1, $reportFilters);

        /** @var ReportFilter $firstReportFilter */
        $firstReportFilter = $reportFilters->first();
        $this->assertEquals($reportFilter2->id, $firstReportFilter->id);
        $this->assertEquals($report2->id, $firstReportFilter->report_id);
    }

    /** @test */
    public function return_empty_when_no_filters_available_for_specific_report()
    {
        $report = factory(Report::class)->create();

        factory(ReportFilter::class)->create();
        factory(ReportFilter::class)->create();

        $reportFilter = ReportFilter::ofReport($report->id)->get();
        $this->assertEmpty($reportFilter);
    }

    /** @test */
    public function get_filters_of_specific_param_type()
    {
        $reportFilter1 = factory(ReportFilter::class)->create(['param_type' => Tag::class]);
        $reportFilter2 = factory(ReportFilter::class)->create(['param_type' => User::class]);

        $reportFilters = ReportFilter::ofParamType(Tag::class)->get();
        $this->assertCount(1, $reportFilters);

        /** @var ReportFilter $firstReportFilter */
        $firstReportFilter = $reportFilters->first();
        $this->assertEquals($reportFilter1->id, $firstReportFilter->id);
        $this->assertEquals($reportFilter1->param_type, $firstReportFilter->param_type);
    }

    /** @test */
    public function return_empty_when_no_filters_available_for_specific_type()
    {
        factory(ReportFilter::class)->create(['param_type' => Tag::class]);
        factory(ReportFilter::class)->create(['param_type' => User::class]);

        $reportFilters = ReportFilter::ofParamType(Project::class)->get();
        $this->assertEmpty($reportFilters);
    }
}