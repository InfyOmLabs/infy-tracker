<?php

namespace Tests\Controllers;

use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

class ReportControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_shows_reports()
    {
        factory(User::class)->times(5)->create();

        $response = $this->get(route('reports.index'));

        $userList = User::orderBy('name')->pluck('name', 'id');
        $response->assertStatus(200)
            ->assertViewIs('reports.index')
            ->assertSeeText('Reports')
            ->assertSeeText('New Report')
            ->assertViewHasAll(['users' => $userList]);
    }

    /** @test */
    public function it_can_show_create_reports()
    {
        $this->mockRepo([self::$project, self::$user, self::$client, self::$tag]);

        $mockProjectResponse = [['id' => 1, 'name' => 'Dummy Project']];
        $this->projectRepository->expects('getProjectsList')->andReturn($mockProjectResponse);

        $mockUserResponse = [['id' => 1, 'name' => 'Dummy User']];
        $this->userRepository->expects('getUserList')->andReturn($mockUserResponse);

        $mockClientResponse = [['id' => 1, 'name' => 'Dummy Client']];
        $this->clientRepository->expects('getClientList')->andReturn($mockClientResponse);

        $mockTagResponse = [['id' => 1, 'name' => 'Dummy Tag']];
        $this->tagRepository->expects('getTagList')->andReturn($mockTagResponse);

        $data['tags'] = $mockTagResponse;
        $data['users'] = $mockUserResponse;
        $data['clients'] = $mockClientResponse;
        $data['projects'] = $mockProjectResponse;

        $response = $this->getJson(route('reports.create'), $data);

        $response->assertStatus(200)
            ->assertViewIs('reports.create')
            ->assertSeeText('New Report')
            ->assertViewHasAll([
                'projects' => $mockProjectResponse,
                'users'    => $mockUserResponse,
                'clients'  => $mockClientResponse,
                'tags'     => $mockTagResponse,
            ]);
    }

    /** @test */
    public function it_can_store_reports()
    {
        $this->mockRepo(self::$report);

        $mockReportResponse = factory(Report::class)->raw(['owner_id' => $this->loggedInUserId]);
        $this->reportRepository->expects('create')
            ->with($mockReportResponse)
            ->andReturn($mockReportResponse);

        $mockReportFilterResponse = factory(ReportFilter::class)->raw();
        $this->reportRepository->expects('createReportFilter')->andReturn($mockReportFilterResponse);

        $response = $this->postJson(route('reports.store'), $mockReportResponse);

        $response->assertStatus(302);
    }

    /** @test */
    public function it_can_shows_reports_details()
    {
        $this->mockRepo(self::$report);

        $mockReportResponse = factory(Report::class)->create();

        $preparedReports = $this->prepareReports();
        $this->reportRepository->expects('getReport')->andReturn($preparedReports);
        $this->reportRepository->expects('getDurationTime');

        $duration = array_sum(\Arr::pluck($preparedReports, 'duration'));
        $data = [
            'report'       => $mockReportResponse,
            'reports'      => $preparedReports,
            'totalMinutes' => $duration,
        ];

        $response = $this->getJson(route('reports.show', $data));

        $response->assertStatus(200)
            ->assertViewIs('reports.show')
            ->assertSeeText('View Report')
            ->assertSeeText('Edit')
            ->assertSeeText('Delete')
            ->assertViewHasAll($data);
    }

    /** @test */
    public function it_can_show_update_reports()
    {
        $this->mockRepo([self::$report, self::$project, self::$user, self::$client, self::$tag]);

        $report = factory(Report::class)->create();

        $mockProjectIds = [1, 2];
        $this->reportRepository->expects('getProjectIds')->andReturn($mockProjectIds);

        $mockTagIds = [1, 2];
        $this->reportRepository->expects('getTagIds')->andReturn($mockTagIds);

        $mockUserIds = [1, 2];
        $this->reportRepository->expects('getUserIds')->andReturn($mockUserIds);

        $mockClientId = 1;
        $this->reportRepository->expects('getClientId')->andReturn($mockClientId);

        $mockProjectResponse = [['id' => 1, 'name' => 'Dummy Project']];
        $this->projectRepository->expects('getProjectsList')->andReturn($mockProjectResponse);

        $mockUserResponse = [['id' => 1, 'name' => 'Dummy User']];
        $this->userRepository->expects('getUserList')->andReturn($mockUserResponse);

        $mockClientResponse = [['id' => 1, 'name' => 'Dummy Client']];
        $this->clientRepository->expects('getClientList')->andReturn($mockClientResponse);

        $mockTagResponse = [['id' => 1, 'name' => 'Dummy Tag']];
        $this->tagRepository->expects('getTagList')->andReturn($mockTagResponse);

        $data['report'] = $report;
        $data['projectIds'] = $mockProjectIds;
        $data['tagIds'] = $mockTagIds;
        $data['userIds'] = $mockUserIds;
        $data['clientId'] = $mockClientId;
        $data['tags'] = $mockTagResponse;
        $data['users'] = $mockUserResponse;
        $data['clients'] = $mockClientResponse;
        $data['projects'] = $mockProjectResponse;

        $response = $this->getJson(route('reports.edit', $report->id), $data);

        $response->assertStatus(200)
            ->assertViewIs('reports.edit')
            ->assertSeeText('Edit Report')
            ->assertViewHasAll([
                'report'     => $report,
                'projectIds' => $mockProjectIds,
                'tagIds'     => $mockTagIds,
                'userIds'    => $mockUserIds,
                'clientId'   => $mockClientId,
                'projects'   => $mockProjectResponse,
                'users'      => $mockUserResponse,
                'clients'    => $mockClientResponse,
                'tags'       => $mockTagResponse,
            ]);
    }

    /** @test */
    public function it_can_update_reports()
    {
        $this->mockRepo(self::$report);

        /** @var Report $report */
        $report = factory(Report::class)->create();

        $mockReportResponse = factory(Report::class)->raw(['id' => $report->id]);
        $this->reportRepository->expects('update')
            ->with($mockReportResponse, $report->id)
            ->andReturn($mockReportResponse);

        $mockReportFilterResponse = factory(ReportFilter::class)->raw();
        $this->reportRepository->expects('updateReportFilter')->andReturn($mockReportFilterResponse);

        $response = $this->putJson(route('reports.update', $report->id), $mockReportResponse);

        $response->assertStatus(302);
    }

    /** @test */
    public function it_can_delete_reports()
    {
        $this->mockRepo(self::$report);

        /** @var Report $report */
        $report = factory(Report::class)->create();

        $this->reportRepository->expects('deleteFilter')->with($report->id);

        $response = $this->deleteJson(route('reports.destroy', $report->id));

        $response->assertStatus(302);
    }

    public function prepareReports()
    {
        return [
            [
                'name'     => 'Random Report Name',
                'time'     => 10,
                'duration' => 55,
                'projects' => [
                    [
                        'name'     => 'LMS',
                        'time'     => 10,
                        'duration' => 55,
                        'users'    => [
                            [
                                'name'     => 'farhan',
                                'time'     => 10,
                                'duration' => 55,
                                'tasks'    => [
                                    [
                                        'name' => 'Random Task Name',
                                        'time' => 10,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
