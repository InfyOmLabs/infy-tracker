<?php

namespace Tests\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ReportRepositoryTest.
 */
class ReportRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var ReportRepository */
    protected $reportRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->reportRepo = app(ReportRepository::class);
    }

    /** @test */
    public function it_can_retrieve_project_ids_of_report()
    {
        $report = factory(Report::class)->create();
        $projects = factory(Project::class)->times(2)->create();

        factory(ReportFilter::class)->create([
            'report_id'  => $report->id,
            'param_id'   => $projects[0]->id,
            'param_type' => Project::class,
        ]);
        factory(ReportFilter::class)->create([
            'report_id'  => $report->id,
            'param_id'   => $projects[1]->id,
            'param_type' => Project::class,
        ]);

        $projectIds = $this->reportRepo->getProjectIds($report->id);

        $this->assertCount(2, $projectIds);

        $this->assertEquals($projects[0]->id, $projectIds[0]);
        $this->assertEquals($projects[1]->id, $projectIds[1]);
    }

    /** @test */
    public function it_can_retrieve_tag_ids_of_report()
    {
        $report = factory(Report::class)->create();
        $tags = factory(Tag::class)->times(2)->create();

        factory(ReportFilter::class)->create([
            'report_id'  => $report->id,
            'param_id'   => $tags[0]->id,
            'param_type' => Tag::class,
        ]);
        factory(ReportFilter::class)->create([
            'report_id'  => $report->id,
            'param_id'   => $tags[1]->id,
            'param_type' => Tag::class,
        ]);

        $tagIds = $this->reportRepo->getTagIds($report->id);

        $this->assertCount(2, $tagIds);

        $this->assertEquals($tags[0]->id, $tagIds[0]);
        $this->assertEquals($tags[1]->id, $tagIds[1]);
    }

    /** @test */
    public function it_can_retrieve_user_ids_of_report()
    {
        $report = factory(Report::class)->create();
        $users = factory(User::class)->times(2)->create();

        factory(ReportFilter::class)->create([
            'report_id'  => $report->id,
            'param_id'   => $users[0]->id,
            'param_type' => User::class,
        ]);
        factory(ReportFilter::class)->create([
            'report_id'  => $report->id,
            'param_id'   => $users[1]->id,
            'param_type' => User::class,
        ]);

        $userIds = $this->reportRepo->getUserIds($report->id);

        $this->assertCount(2, $userIds);

        $this->assertEquals($users[0]->id, $userIds[0]);
        $this->assertEquals($users[1]->id, $userIds[1]);
    }

    /** @test */
    public function it_can_retrieve_client_id_of_report()
    {
        $report = factory(Report::class)->create();
        $client = factory(Client::class)->create();

        factory(ReportFilter::class)->create([
            'report_id'  => $report->id,
            'param_id'   => $client->id,
            'param_type' => Client::class,
        ]);

        $clientId = $this->reportRepo->getClientId($report->id);

        $this->assertNotEmpty($clientId);
        $this->assertEquals($client->id, $clientId);
    }

    /** @test */
    public function it_will_return_empty_without_param_type()
    {
        $report = factory(Report::class)->create();
        $client = factory(Client::class)->create();

        factory(ReportFilter::class)->create([
            'report_id' => $report->id,
            'param_id'  => $client->id,
        ]);

        $clientId = $this->reportRepo->getClientId($report->id);

        $this->assertEmpty($clientId);
    }

    /** @test */
    public function it_can_delete_report_filter_for_report()
    {
        $report = factory(Report::class)->create();

        $reportFilter = factory(ReportFilter::class)->create(['report_id' => $report->id]);

        $deleteFilter = $this->reportRepo->deleteFilter($report->id);

        $this->assertEquals($reportFilter->id, $deleteFilter);
        $this->assertNull(ReportFilter::find($reportFilter->id));
    }

    /** @test */
    public function it_can_return_zero_hours_when_minutes_is_zero()
    {
        $duration = $this->reportRepo->getDurationTime(0);

        $this->assertNotEmpty($duration);
        $this->assertEquals('0 hr', $duration);
    }

    /** @test */
    public function it_can_return_minutes()
    {
        $duration = $this->reportRepo->getDurationTime(55);

        $this->assertNotEmpty($duration);
        $this->assertEquals('55 min', $duration);
    }

    /** @test */
    public function it_can_return_hours_from_minutes()
    {
        $duration = $this->reportRepo->getDurationTime(135);

        $this->assertNotEmpty($duration);
        $this->assertEquals('2 hr 15 min', $duration);
    }

    /** @test */
    public function it_can_return_hours_from_minutes_when_calculated_minutes_is_zero()
    {
        $duration = $this->reportRepo->getDurationTime(120);

        $this->assertNotEmpty($duration);
        $this->assertEquals('2 hr', $duration);
    }
}
