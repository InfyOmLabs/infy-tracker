<?php

namespace Tests\Controllers;

use App\Models\Report;
use App\Repositories\ReportRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $reportRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->reportRepository = \Mockery::mock(ReportRepository::class);
        app()->instance(ReportRepository::class, $this->reportRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function it_can_retrieve_report()
    {
        /** @var Report $report */
        $report = factory(Report::class)->create();

        $response = $this->getJson('reports/'.$report->id.'/edit');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_delete_report()
    {
        $this->mockRepository();

        /** @var Report $report */
        $report = factory(Report::class)->create();

        $this->reportRepository->shouldReceive('deleteFilter')
            ->once()
            ->with($report->id)
            ->andReturn([]);

        $response = $this->deleteJson('reports/'.$report->id);

        $this->assertSuccessMessageResponse($response, 'Report deleted successfully.');

        $response = $this->getJson('reports/'.$report->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Report not found.',
        ]);
    }
}
