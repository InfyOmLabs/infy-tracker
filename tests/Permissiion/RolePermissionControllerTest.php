<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RolePermissionControllerTest extends TestCase
{
    use DatabaseTransactions;

    public $user;

    /** @var user */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function test_can_get_roles_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_roles']);
        $response = $this->getJson(route('roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('roles.index');
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_roles_without_permission()
    {
        $response = $this->get(route('roles.index'));
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_create_role_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_roles']);

        /** @var Role $role */
        $role = factory(Role::class)->raw();

        $response = $this->postJson(route('roles.store'), $role);

        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function test_not_allow_to_create_role_without_permission()
    {
        /** @var Role $role */
        $role = factory(Role::class)->raw();

        $response = $this->post(route('roles.store'), $role);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_update_role_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_roles']);

        /** @var Role $role */
        $role = factory(Role::class)->create();
        $updateRole = factory(Role::class)->raw(['id' => $role->id]);

        $response = $this->putJson(route('roles.update', $role->id), $updateRole);

        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_role_without_permission()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();
        $updateRole = factory(Role::class)->raw(['id' => $role->id]);

        $response = $this->put(route('roles.update', $role->id), $updateRole);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_delete_role_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_roles']);

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
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->delete(route('roles.destroy', $role->id));

        $response->assertStatus(403);
    }
}
