<?php

namespace Tests\Controllers\Validations;

use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class RoleControllerValidationTest.
 */
class RoleControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_create_role_fails_when_name_is_not_passed()
    {
        $this->post('roles', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_role_fails_when_name_is_duplicated()
    {
        $role = factory(Role::class)->create();

        $this->post('roles', ['name' => $role->name])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    /** @test */
    public function it_can_create_role()
    {
        $this->post('roles', ['name' => 'any role'])
            ->assertSessionHasNoErrors();
        $role = Role::whereName('any role')->first();
        $this->assertNotEmpty($role);
        $this->assertEquals('any role', $role->name);
    }

    /** @test */
    public function test_update_role_fails_when_name_is_not_passed()
    {
        $role = factory(Role::class)->create();

        $this->put('roles/'.$role->id, ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_role_fails_when_name_is_duplicate()
    {
        $role1 = factory(Role::class)->create();
        $role2 = factory(Role::class)->create();

        $this->put('roles/'.$role2->id, ['name' => $role1->name])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    /** @test */
    public function allow_update_role_with_valid_input()
    {
        $role = factory(Role::class)->create();
        $inputs = array_merge($role->toArray(), ['name' => 'Any Role Name']);

        $this->put('roles/'.$role->id, $inputs)
            ->assertSessionHasNoErrors();

        $this->assertEquals('Any Role Name', $role->fresh()->name);
    }
}
