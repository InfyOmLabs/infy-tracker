<?php

namespace Tests\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $roleRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->roleRepository = \Mockery::mock(RoleRepository::class);
        app()->instance(RoleRepository::class, $this->roleRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function it_can_store_role()
    {
        $this->mockRepository();

        /** @var Role $role */
        $role = factory(Role::class)->make()->toArray();

        $this->roleRepository->shouldReceive('create')
            ->once()
            ->with($role)
            ->andReturn([]);

        $response = $this->postJson('roles', $role);
        $response->assertStatus(302);
    }

    /** @test */
    public function it_can_retrieve_role()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->getJson('roles/'.$role->id.'/edit');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_delete_role()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->deleteJson('roles/'.$role->id);

        $this->assertSuccessMessageResponse($response, 'Role deleted successfully.');

        $response = $this->getJson('roles/'.$role->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Role not found.',
        ]);
    }

    /** @test */
    public function it_can_not_delete_role_when_role_assigned_to_user()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();
        $user = factory(User::class)->create();
        $role->users()->sync([$user->id]);

        $response = $this->deleteJson('roles/'.$role->id);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'This user role could not be deleted, because it’s assigned to a user.',
        ]);
    }
}
