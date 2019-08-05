<?php

namespace Tests\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ReportRepository;
use Illuminate\Database\Eloquent\Collection;
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

    private $defaultUserId = 1;

    public function setUp(): void
    {
        parent::setUp();
        $this->reportRepo = app(ReportRepository::class);
    }

    /** @test */
    public function it_can_retrieve_project_ids_of_report()
    {
        $report = factory(Report::class)->create();
        $user = factory(User::class)->create();
        $projects = factory(Project::class)->times(2)->create();

        $this->generateReportFilter($report->id, $projects[0]->id, Project::class);
        $this->generateReportFilter($report->id, $projects[1]->id, Project::class);
        $this->generateReportFilter($report->id, $user->id, User::class);

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
        $user = factory(User::class)->create();
        $this->generateReportFilter($report->id, $tags[0]->id, Tag::class);
        $this->generateReportFilter($report->id, $tags[1]->id, Tag::class);
        $this->generateReportFilter($report->id, $user->id, User::class);

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
        $project = factory(Project::class)->create();
        $this->generateReportFilter($report->id, $users[0]->id, User::class);
        $this->generateReportFilter($report->id, $users[1]->id, User::class);
        $this->generateReportFilter($report->id, $project->id, Project::class);

        $userIds = $this->reportRepo->getUserIds($report->id);

        $this->assertCount(2, $userIds);
        $this->assertEquals($users[0]->id, $userIds[0]);
        $this->assertEquals($users[1]->id, $userIds[1]);
    }

    /** @test */
    public function it_can_retrieve_client_ids_of_report()
    {
        $reports = factory(Report::class)->times(2)->create();
        $clients = factory(Client::class)->times(2)->create();
        $this->generateReportFilter($reports[0]->id, $clients[0]->id, Client::class);
        $this->generateReportFilter($reports[1]->id, $clients[1]->id, Client::class);

        $clientId = $this->reportRepo->getClientId($reports[0]->id);

        $this->assertNotEmpty($clientId);
        $this->assertEquals($clients[0]->id, $clientId);
    }

    /** @test */
    public function it_will_return_empty_when_client_filter_not_exist_on_given_report()
    {
        $reports = factory(Report::class)->times(2)->create();
        $vishal = factory(Client::class)->create();

        $this->generateReportFilter($reports[0]->id, $vishal->id, Client::class); // client filter on another report
        $this->generateReportFilter($reports[1]->id, $this->defaultUserId, User::class);

        $clientId = $this->reportRepo->getClientId($reports[1]->id);

        $this->assertEmpty($clientId);
    }

    /** @test */
    public function it_can_delete_report_filter_of_given_report()
    {
        $reportFilter = factory(ReportFilter::class)->create();

        $this->reportRepo->deleteFilter($reportFilter->report_id);

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
    public function it_can_return_only_hours_when_no_remaining_minutes_are_left()
    {
        $duration = $this->reportRepo->getDurationTime(120);

        $this->assertNotEmpty($duration);
        $this->assertEquals('2 hr', $duration);
    }

    /** @test */
    public function test_can_update_report_filter_for_given_project_ids()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $projects */
        $projects = factory(Project::class)->times(3)->create();

        $this->generateReportFilter($report->id, $projects[0]->id, Project::class);
        $this->generateReportFilter($report->id, $projects[1]->id, Project::class);

        $input = [
            'projectIds' => [$projects[0]->id, $projects[2]->id],
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertNotEmpty($updatedReportFilter);
        $this->assertEquals($projects[2]->id, $updatedReportFilter[0]->param_id);
        $this->assertEquals(Project::class, $updatedReportFilter[0]->param_type);

        $reportFilter = ReportFilter::ofParamType(Project::class)
            ->where('param_id', $projects[1]->id)
            ->first();
        $this->assertEmpty($reportFilter);
    }

    /** @test */
    public function test_can_update_report_filter_for_given_user_ids()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $users */
        $users = factory(User::class)->times(3)->create();

        $this->generateReportFilter($report->id, $users[0]->id, User::class);
        $this->generateReportFilter($report->id, $users[1]->id, User::class);

        $input = [
            'userIds' => [$users[0]->id, $users[2]->id],
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertNotEmpty($updatedReportFilter);
        $this->assertEquals($users[2]->id, $updatedReportFilter[0]->param_id);
        $this->assertEquals(User::class, $updatedReportFilter[0]->param_type);

        $reportFilter = ReportFilter::ofParamType(User::class)
            ->where('param_id', $users[1]->id)
            ->first();
        $this->assertEmpty($reportFilter);
    }

    /** @test */
    public function test_can_update_report_filter_for_given_tag_ids()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $tags */
        $tags = factory(Tag::class)->times(3)->create();

        $this->generateReportFilter($report->id, $tags[0]->id, Tag::class);
        $this->generateReportFilter($report->id, $tags[1]->id, Tag::class);

        $input = [
            'tagIds' => [$tags[0]->id, $tags[2]->id],
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertNotEmpty($updatedReportFilter);
        $this->assertEquals($tags[2]->id, $updatedReportFilter[0]->param_id);
        $this->assertEquals(Tag::class, $updatedReportFilter[0]->param_type);

        $reportFilter = ReportFilter::ofParamType(Tag::class)
            ->where('param_id', $tags[1]->id)
            ->first();
        $this->assertEmpty($reportFilter);
    }

    /** @test */
    public function test_can_update_report_filter_for_given_client_id()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $clients */
        $clients = factory(Client::class)->times(2)->create();

        $this->generateReportFilter($report->id, $clients[1]->id, Client::class);

        $input = [
            'client_id' => $clients[0]->id,
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertNotEmpty($updatedReportFilter);
        $this->assertEquals($clients[0]->id, $updatedReportFilter[0]->param_id);
        $this->assertEquals(Client::class, $updatedReportFilter[0]->param_type);
    }

    /**
     * @param int    $reportId
     * @param int    $paramId
     * @param string $type
     *
     * @return array
     */
    public function generateReportFilter($reportId, $paramId, $type)
    {
        return factory(ReportFilter::class)->create([
            'report_id'  => $reportId,
            'param_id'   => $paramId,
            'param_type' => $type,
        ]);
    }
}
