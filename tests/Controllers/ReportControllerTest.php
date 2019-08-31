<?php

namespace Tests\Controllers;

use App\Models\Report;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    /** @test */
    public function test_reports_are_filtered_using_created_by()
    {
        /** @var Report $firstReport */
        $firstReport = factory(Report::class)->create();
        $secondReport = factory(Report::class)->create();

        $response = $this->getJson(route('reports.index', [
            'filter_created_by' => $firstReport->owner_id,
        ]));

        $data = $response->original['data'];
        $this->assertCount(1, $data);
        $this->assertEquals($firstReport->id, $data[0]['id']);
        $this->assertEquals($firstReport->owner_id, $data[0]['owner_id']);
    }
}
