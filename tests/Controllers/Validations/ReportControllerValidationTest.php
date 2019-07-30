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
        $this->post('reports', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_report_fails_when_start_date_is_not_passed()
    {
        $this->post('reports', ['start_date' => ''])->assertSessionHasErrors([
            'start_date' => 'The start date field is required.',
        ]);
    }

    /** @test */
    public function test_create_report_fails_when_end_date_is_not_passed()
    {
        $this->post('reports', ['end_date' => ''])->assertSessionHasErrors([
            'end_date' => 'The end date field is required.',
        ]);
    }

    /** @test */
    public function it_can_create_report()
    {
        $this->post('reports', [
            'name'       => 'random string',
            'start_date' => $this->faker->dateTime,
            'end_date'   => $this->faker->dateTime,
        ])->assertSessionHasNoErrors();

        $report = Report::whereName('random string')->first();

        $this->assertNotEmpty($report);
        $this->assertEquals('random string', $report->name);
    }

    /** @test */
    public function test_update_report_fails_when_name_is_not_passed()
    {
        $report = factory(Report::class)->create();

        $this->put('reports/'.$report->id, ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_report_fails_when_start_date_is_not_passed()
    {
        $report = factory(Report::class)->create();

        $this->put('reports/'.$report->id, ['start_date' => ''])
            ->assertSessionHasErrors(['start_date' => 'The start date field is required.']);
    }

    /** @test */
    public function test_update_report_fails_when_end_date_is_not_passed()
    {
        $report = factory(Report::class)->create();

        $this->put('reports/'.$report->id, ['end_date' => ''])
            ->assertSessionHasErrors(['end_date' => 'The end date field is required.']);
    }

    /** @test */
    public function it_can_update_report_with_valid_input()
    {
        /** @var Report $report */
        $report = factory(Report::class)->create();

        $this->put('reports/'.$report->id, [
            'name'       => 'Any Dummy Name',
            'start_date' => $this->faker->dateTime,
            'end_date'   => $this->faker->dateTime,
        ])->assertSessionHasNoErrors();

        $this->assertEquals('Any Dummy Name', $report->fresh()->name);
    }
}
