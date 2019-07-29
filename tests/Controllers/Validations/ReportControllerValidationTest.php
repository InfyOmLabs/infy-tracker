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
        $input = [
            'name'       => 'random string',
            'start_date' => '',
        ];

        $this->post('reports', $input)->assertSessionHasErrors([
            'start_date' => 'The start date field is required.',
        ]);
    }

    /** @test */
    public function test_create_report_fails_when_end_date_is_not_passed()
    {
        $input = [
            'name'       => 'random string',
            'start_date' => '2019-07-19 03:53:07',
            'end_date'   => '',
        ];

        $this->post('reports', $input)->assertSessionHasErrors([
            'end_date' => 'The end date field is required.',
        ]);
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
        $input = [
            'name'       => 'random string',
            'start_date' => '',
        ];

        $this->put('reports/'.$report->id, $input)
            ->assertSessionHasErrors(['start_date' => 'The start date field is required.']);
    }

    /** @test */
    public function test_update_report_fails_when_end_date_is_not_passed()
    {
        $report = factory(Report::class)->create();
        $input = [
            'name'       => 'random string',
            'start_date' => '2019-07-19 03:53:07',
            'end_date'   => '',
        ];

        $this->put('reports/'.$report->id, $input)
            ->assertSessionHasErrors(['end_date' => 'The end date field is required.']);
    }
}
