<?php

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function test_can_delete_role_with_valid_permission()
    {
        $user = $this->makeUserWithPermissions(['manage_roles']);
        $this->actingAs($user);

        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->deleteJson(route('roles.destroy', $role->id));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_not_allow_to_delete_role_without_permission()
    {
        $user = $this->makeUserWithPermissions();
        $this->actingAs($user);

        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->delete(route('roles.destroy', $role->id));

        $response->assertStatus(403);
    }
}
