<?php

namespace Tests\Controllers;

use App\Models\Report;
use App\Models\TimeEntry;
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
    public function test_can_filter_report_by_created_user()
    {
        /** @var TimeEntry $firstTimeEntry */
        $firstTimeEntry = factory(TimeEntry::class)->create();
        $secondTimeEntry = factory(TimeEntry::class)->create();

        /** @var Report $firstReport */
        $firstReport = factory(Report::class)->create(['owner_id' => $firstTimeEntry->user_id]);
        $secondReport = factory(Report::class)->create();

        $response = $this->getJson(route('reports.index', [
            'filter_created_by' => $firstReport->owner_id,
        ]));

        $data = $response->original['data'];
        $this->assertCount(1, $data);
        $this->assertEquals($firstTimeEntry->id, $data[0]['id']);
        $this->assertEquals($firstReport->owner_id, $data[0]['owner_id']);
    }
}
