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
        $this->post(route('roles.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_role_fails_when_name_is_duplicate()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $this->post(route('roles.store'), ['name' => $role->name])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    /** @test */
    public function it_can_create_role()
    {
        $fakeRole = factory(Role::class)->raw();

        $this->post(route('roles.store'), $fakeRole)->assertSessionHasNoErrors();

        $role = Role::whereName($fakeRole['name'])->first();
        $this->assertNotEmpty($role);
        $this->assertEquals($fakeRole['name'], $role->name);
    }

    /** @test */
    public function test_update_role_fails_when_name_is_not_passed()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $this->put(route('roles.update', $role->id), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_role_fails_when_name_is_duplicate()
    {
        $role1 = factory(Role::class)->create();
        $role2 = factory(Role::class)->create();

        $this->put(route('roles.update', $role2->id), ['name' => $role1->name])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    /** @test */
    public function test_can_update_role_with_valid_input()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();
        $fakeRole = factory(Role::class)->raw();

        $this->put(route('roles.update', $role->id), $fakeRole)
            ->assertSessionHasNoErrors();

        $this->assertEquals($fakeRole['name'], $role->fresh()->name);
    }
}
