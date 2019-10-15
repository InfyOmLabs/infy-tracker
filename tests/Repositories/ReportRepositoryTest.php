<?php

namespace Tests\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ReportRepository;
use Exception;
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
        $projects = factory(Project::class, 2)->create();

        $this->generateReportFilter($report->id, $projects[0]->id, Project::class);
        $this->generateReportFilter($report->id, $projects[1]->id, Project::class);
        $this->generateReportFilter($report->id, $user->id, User::class);

        $projectIds = $this->reportRepo->getProjectIds($report->id);

        $this->assertCount(2, $projectIds);
        $this->assertEquals($projects[0]->id, $projectIds[0]);
        $this->assertEquals($projects[1]->id, $projectIds[1]);
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

    /** @test */
    public function it_can_retrieve_tag_ids_of_report()
    {
        $report = factory(Report::class)->create();
        $tags = factory(Tag::class, 2)->create();
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
        /** @var User[] $users */
        $users = factory(User::class, 2)->create();
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
        $reports = factory(Report::class, 2)->create();
        $clients = factory(Client::class, 2)->create();
        $this->generateReportFilter($reports[0]->id, $clients[0]->id, Client::class);
        $this->generateReportFilter($reports[1]->id, $clients[1]->id, Client::class);

        $clientId = $this->reportRepo->getClientId($reports[0]->id);

        $this->assertNotEmpty($clientId);
        $this->assertEquals($clients[0]->id, $clientId);
    }

    /** @test */
    public function it_will_return_empty_when_client_filter_not_exist_on_given_report()
    {
        /** @var Report[] $reports */
        $reports = factory(Report::class, 2)->create();
        $vishal = factory(Client::class)->create();

        $this->generateReportFilter($reports[0]->id, $vishal->id, Client::class); // client filter on another report
        $this->generateReportFilter($reports[1]->id, $this->defaultUserId, User::class);

        $clientId = $this->reportRepo->getClientId($reports[1]->id);

        $this->assertEmpty($clientId);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_can_delete_report_filter_of_given_report()
    {
        /** @var ReportFilter $reportFilter */
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

    /**
     * @test
     *
     * @throws Exception
     */
    public function test_create_new_project_filters_by_deleting_old_filters()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $projects */
        $projects = factory(Project::class, 3)->create();

        $this->generateReportFilter($report->id, $projects[0]->id, Project::class);
        $this->generateReportFilter($report->id, $projects[1]->id, Project::class);

        $input = [
            'projectIds' => [$projects[2]->id], // project ids that not passed here, its report filter should be deleted
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertCount(1, $updatedReportFilter);
        $reportFilter = ReportFilter::ofParamType(Project::class)->get();
        $this->assertCount(1, $reportFilter, 'Remaining 2 should be deleted.');
        $this->assertEquals($projects[2]->id, $reportFilter[0]->param_id);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function test_create_new_user_filters_by_deleting_old_filters()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $users */
        $users = factory(User::class, 3)->create();

        $this->generateReportFilter($report->id, $users[0]->id, User::class);
        $this->generateReportFilter($report->id, $users[1]->id, User::class);

        $input = [
            'userIds' => [$users[2]->id],
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertCount(1, $updatedReportFilter);
        $reportFilter = ReportFilter::ofParamType(User::class)->get();
        $this->assertCount(1, $reportFilter, 'Remaining 2 should be deleted.');
        $this->assertEquals($users[2]->id, $reportFilter[0]->param_id);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function test_create_new_tag_filters_by_deleting_old_filters()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $tags */
        $tags = factory(Tag::class, 3)->create();

        $this->generateReportFilter($report->id, $tags[0]->id, Tag::class);
        $this->generateReportFilter($report->id, $tags[1]->id, Tag::class);

        $input = [
            'tagIds' => [$tags[2]->id],
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertCount(1, $updatedReportFilter);
        $reportFilter = ReportFilter::ofParamType(Tag::class)->get();
        $this->assertCount(1, $reportFilter, 'Remaining 2 should be deleted.');
        $this->assertEquals($tags[2]->id, $reportFilter[0]->param_id);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function test_create_new_client_filter_by_deleting_old_filter()
    {
        $report = factory(Report::class)->create();

        /** @var Collection $clients */
        $clients = factory(Client::class, 2)->create();

        $this->generateReportFilter($report->id, $clients[1]->id, Client::class);

        $input = [
            'client_id' => $clients[0]->id,
        ];
        $updatedReportFilter = $this->reportRepo->updateReportFilter($input, $report);

        $this->assertCount(1, $updatedReportFilter);
        $reportFilter = ReportFilter::ofParamType(Client::class)->get();
        $this->assertCount(1, $reportFilter, 'Remaining 2 should be deleted.');
        $this->assertEquals($clients[0]->id, $reportFilter[0]->param_id);
    }

    /** @test */
    public function it_can_create_report_filter()
    {
        $report = factory(Report::class)->create();

        $client = factory(Client::class)->create();
        $user = factory(User::class)->create();
        $project = factory(Project::class)->create();
        $tag = factory(Tag::class)->create();

        $projectReportFilter = $this->reportRepo->createReportFilter(['projectIds' => [$project->id]], $report);
        $userReportFilter = $this->reportRepo->createReportFilter(['userIds' => [$user->id]], $report);
        $tagReportFilter = $this->reportRepo->createReportFilter(['tagIds' => [$tag->id]], $report);
        $clientReportFilter = $this->reportRepo->createReportFilter(['client_id' => $client->id], $report);

        $this->assertNotEmpty($projectReportFilter);
        $this->assertNotEmpty($userReportFilter);
        $this->assertNotEmpty($tagReportFilter);
        $this->assertNotEmpty($clientReportFilter);

        $this->assertEquals(Project::class, $projectReportFilter[0]->param_type);
        $this->assertEquals(User::class, $userReportFilter[0]->param_type);
        $this->assertEquals(Tag::class, $tagReportFilter[0]->param_type);
        $this->assertEquals(Client::class, $clientReportFilter[0]->param_type);
    }
}
