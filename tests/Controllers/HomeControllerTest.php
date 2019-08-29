<?php

namespace Tests\Controllers;

use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

class HomeControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_shows_dashboard()
    {
        $this->mockRepo(self::$user);

        $mockedResponse = [['id' => 1, 'name' => 'Dummy User']];
        $this->userRepository->expects('getUserList')
            ->andReturn($mockedResponse);

        $response = $this->get(route('home'));

        $response->assertStatus(200)
            ->assertViewIs('dashboard.index')
            ->assertViewHas('users', $mockedResponse)
            ->assertSeeText('Dashboard')
            ->assertSeeText('Custom Report')
            ->assertSeeText('Daily Work Report');
    }

    /** @test */
    public function test_can_retrieve_report_of_given_user()
    {
        $this->mockRepo(self::$dashboard);

        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $startTime = $timeEntry->start_time;
        $endTime = $timeEntry->end_time;
        $userId = $timeEntry->user_id;

        $mockResponse = ['projects' => [$timeEntry->task->project->name]];
        $this->dashboardRepository->expects('getWorkReport')
            ->with([
                'start_date' => $startTime,
                'end_date'   => $endTime,
                'user_id'    => $userId,
            ])->andReturn($mockResponse);

        $response = $this->getJson(route('users-work-report', [
            'start_date' => $startTime,
            'end_date'   => $endTime,
            'user_id'    => $userId,
        ]));

        $this->assertSuccessDataResponse($response, $mockResponse, 'Custom Report retrieved successfully.');
    }

    /** @test */
    public function test_can_retrieve_developer_work_report()
    {
        $this->mockRepo(self::$dashboard);

        /** @var TimeEntry $timeEntry */
        $timeEntry = factory(TimeEntry::class)->create();

        $startTime = $timeEntry->start_time;
        $endTime = $timeEntry->end_time;

        $mockResponse = ['projects' => [$timeEntry->task->project->name]];
        $this->dashboardRepository->expects('getDeveloperWorkReport')
            ->with([
                'start_date' => $startTime,
                'end_date'   => $endTime,
            ])->andReturn($mockResponse);

        $response = $this->getJson(route('developers-work-report', [
            'start_date' => $startTime,
            'end_date'   => $endTime,
        ]));

        $this->assertSuccessDataResponse(
            $response,
            $mockResponse,
            'Daily Work Report retrieved successfully.'
        );
    }
}
