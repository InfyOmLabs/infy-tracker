<?php

namespace Tests\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class RoleControllerTest.
 */
class RoleControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_delete_role()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->deleteJson('roles/'.$role->id);

        $this->assertSuccessMessageResponse($response, 'Role deleted successfully.');

        $response = $this->getJson(route('roles.edit', $role->id));

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
        $farhan = factory(User::class)->create();
        $role->users()->sync([$farhan->id]);

        $response = $this->deleteJson(route('roles.destroy', $role->id));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'This user role could not be deleted, because it’s assigned to a user.',
        ]);
    }
}
