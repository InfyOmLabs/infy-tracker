<?php

namespace Tests\Controllers\Validations;

use App\Models\Report;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ReportControllerValidationTest.
 */
class ReportControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_create_report_fails_when_name_is_not_passed()
    {
        $this->post(route('reports.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_report_fails_when_start_date_is_not_passed()
    {
        $this->post(route('reports.store'), ['start_date' => ''])->assertSessionHasErrors([
            'start_date' => 'The start date field is required.',
        ]);
    }

    /** @test */
    public function test_create_report_fails_when_end_date_is_not_passed()
    {
        $this->post(route('reports.store'), ['end_date' => ''])->assertSessionHasErrors([
            'end_date' => 'The end date field is required.',
        ]);
    }

    /** @test */
    public function test_can_create_report()
    {
        $fakeReport = factory(Report::class)->raw();

        $this->post(route('reports.store'), $fakeReport)->assertSessionHasNoErrors();

        $report = Report::whereName($fakeReport['name'])->first();

        $this->assertNotEmpty($report);
        $this->assertEquals($fakeReport['name'], $report->name);
    }

    /** @test */
    public function test_update_report_fails_when_name_is_not_passed()
    {
        /** @var Report $report */
        $report = factory(Report::class)->create();

        $this->put(route('reports.update', $report->id), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_report_fails_when_start_date_is_not_passed()
    {
        /** @var Report $report */
        $report = factory(Report::class)->create();

        $this->put(route('reports.update', $report->id), ['start_date' => ''])
            ->assertSessionHasErrors(['start_date' => 'The start date field is required.']);
    }

    /** @test */
    public function test_update_report_fails_when_end_date_is_not_passed()
    {
        /** @var Report $report */
        $report = factory(Report::class)->create();

        $this->put(route('reports.update', $report->id), ['end_date' => ''])
            ->assertSessionHasErrors(['end_date' => 'The end date field is required.']);
    }

    /** @test */
    public function test_can_update_report_with_valid_input()
    {
        /** @var Report $report */
        $report = factory(Report::class)->create();
        $fakeReport = factory(Report::class)->raw();

        $this->put(route('reports.update', $report->id), $fakeReport)->assertSessionHasNoErrors();

        $this->assertEquals($fakeReport['name'], $report->fresh()->name);
    }
}
