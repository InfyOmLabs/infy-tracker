<?php

namespace Tests\Repositories;

use App\Models\Permission;
use App\Repositories\PermissionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class PermissionRepositoryTest.
 */
class PermissionRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var PermissionRepository */
    protected $permissionRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->permissionRepo = app(PermissionRepository::class);
    }

    /** @test */
    public function it_can_retrieve_permissions_list()
    {
        factory(Permission::class)->create();

        $permissions = $this->permissionRepo->permissionList();

        $this->assertCount(11, $permissions, ' 11 default permission');

        $allPermissions = Permission::all();
        $allPermissions->map(function (Permission $allPermissions) use ($permissions) {
            $this->assertContains($allPermissions->display_name, $permissions);
        });
    }
}
