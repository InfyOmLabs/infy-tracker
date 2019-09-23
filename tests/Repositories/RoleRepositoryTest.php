<?php

namespace Tests\Repositories;

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class RoleRepositoryTest.
 */
class RoleRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var RoleRepository */
    protected $roleRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepo = app(RoleRepository::class);
    }

    /** @test */
    public function it_can_retrieve_roles_list()
    {
        factory(Role::class, 3)->create();

        $roles = $this->roleRepo->getRolesList();

        $this->assertCount(6, $roles, '3 default role');

        $allRoles = Role::all();
        $allRoles->map(function (Role $allRoles) use ($roles) {
            $this->assertContains($allRoles->name, $roles);
        });
    }
}
