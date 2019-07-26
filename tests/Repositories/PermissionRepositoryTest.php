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

        // 9 default permission
        $this->assertCount(10, $permissions);

        $permissions->map(function ($permission) use ($permissions) {
            $this->assertContains($permission, $permissions);
        });
    }
}
