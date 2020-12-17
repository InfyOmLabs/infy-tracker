<?php

namespace Tests\Permissions;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class ReportControllerPermissionTest.
 */
class ReportControllerPermissionTest extends TestCase
{
//    use DatabaseTransactions;
//
//    public $user;
//
//    public function setUp(): void
//    {
//        parent::setUp();
//        $this->user = factory(User::class)->create();
//        $this->actingAs($this->user);
//    }
//
//    public function test_can_get_reports_with_valid_permission()
//    {
//        $this->attachPermissions($this->user->id, ['manage_reports']);
//
//        $response = $this->getJson(route('reports.index'));
//
//        $response->assertStatus(200);
//        $response->assertViewIs('reports.index');
//    }
//
//    public function test_not_allow_to_get_reports_without_permission()
//    {
//        $response = $this->get(route('reports.index'));
//
//        $response->assertStatus(403);
//    }
//
//    public function test_can_create_report_with_valid_permission()
//    {
//        $this->attachPermissions($this->user->id, ['manage_reports']);
//
//        $report = factory(Report::class)->raw();
//
//        $response = $this->postJson(route('reports.store'), $report);
//
//        $response->assertStatus(302);
//        $response->assertRedirect('reports');
//    }
//
//    public function test_not_allow_to_create_report_without_permission()
//    {
//        $report = factory(Report::class)->raw();
//
//        $response = $this->post(route('reports.store'), $report);
//
//        $response->assertStatus(403);
//    }
//
//    public function test_can_update_report_with_valid_permission()
//    {
//        $this->attachPermissions($this->user->id, ['manage_reports']);
//
//        /** @var Report $report */
//        $report = factory(Report::class)->create();
//        $updateReport = factory(Report::class)->raw(['id' => $report->id]);
//
//        $response = $this->putJson(route('reports.update', $report->id), $updateReport);
//
//        $response->assertStatus(302);
//    }
//
//   public function test_not_allow_to_update_report_without_permission()
//    {
//        /** @var Report $report */
//        $report = factory(Report::class)->create();
//        $updateReport = factory(Report::class)->raw(['id' => $report->id]);
//
//        $response = $this->put(route('reports.update', $report->id), $updateReport);
//
//        $response->assertStatus(403);
//    }
//
//    public function test_can_delete_report_with_valid_permission()
//    {
//        $this->attachPermissions($this->user->id, ['manage_reports']);
//
//        /** @var Report $report */
//        $report = factory(Report::class)->create();
//
//        $response = $this->deleteJson(route('reports.destroy', $report->id));
//
//        $response->assertStatus(302);
//    }
//
//    public function test_not_allow_to_delete_report_without_permission()
//    {
//        /** @var Report $report */
//        $report = factory(Report::class)->create();
//
//        $response = $this->delete(route('reports.destroy', $report->id));
//
//        $response->assertStatus(403);
//    }
//
//    public function test_can_show_report_with_valid_permission()
//    {
//        $this->attachPermissions($this->user->id, ['manage_reports']);
//
//        /** @var Report $report */
//        $report = factory(Report::class)->create();
//
//        $response = $this->getJson(route('reports.show', $report->id));
//
//        $response->assertStatus(200);
//    }
//
//    public function test_not_allow_to_show_report_without_permission()
//    {
//        /** @var Report $report */
//        $report = factory(Report::class)->create();
//
//        $response = $this->get(route('reports.show', $report->id));
//
//        $response->assertStatus(403);
//    }
//
//    public function test_can_get_users_of_projects_with_valid_permission()
//    {
//        $this->attachPermissions($this->user->id, ['manage_reports']);
//
//        $response = $this->getJson(route('users-of-projects'));
//
//        $this->assertSuccessMessageResponse($response, 'Users Retrieved successfully.');
//    }
//
//    public function test_not_allow_to_get_users_of_projects_without_permission()
//    {
//        $response = $this->get(route('users-of-projects'));
//
//        $response->assertStatus(403);
//    }
//
//    public function test_can_get_projects_of_client_with_valid_permission()
//    {
//        $this->attachPermissions($this->user->id, ['manage_reports']);
//
//        $response = $this->getJson(route('projects-of-client'));
//
//        $this->assertSuccessMessageResponse($response, 'Projects retrieved successfully.');
//    }
//
//    public function test_not_allow_to_get_projects_of_client_without_permission()
//    {
//        $response = $this->get(route('projects-of-client'));
//
//        $response->assertStatus(403);
//    }
}
