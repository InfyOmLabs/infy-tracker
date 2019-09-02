<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
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
    public function test_can_delete_role_with_manage_role_permission()
    {
        $this->makeUserWithPermissions(['manage_roles'], ['name' => 'librarian'])->id;

        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->deleteJson(route('roles.destroy', $role->id));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_unable_to_delete_role_without_manage_role_permission()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->deleteJson(route('roles.destroy', $role->id));

        $this->assertExceptionMessage($response, 'Unauthenticated.');
    }

    /**
     * @param array $permissions
     * @param array $roleFields
     *
     * @return User
     */
    public function makeUserWithPermissions($permissions = [], $roleFields = [])
    {
        /** @var UserRepository $userRepo */
        $userRepo = \App::make(UserRepository::class);

        $user = factory(User::class)->raw([
            'is_active'         => true,
            'is_email_verified' => true,
        ]);
        $createdUser = $userRepo->create($user);

        $this->actingAs($createdUser);

        /** @var RoleRepository $roleRepo */
        $roleRepo = app(RoleRepository::class);

        $permissionIds = Permission::whereIn('name', $permissions)->get()->pluck('id');

        /** @var Role $role */
        $role = $roleRepo->create($roleFields);
        $role->perms()->sync($permissionIds);

        /** @var User $user_record */
        $user_record = User::find($createdUser->id);

        /** @var Role $roleRecord */
        $roleRecord = Role::find($role->id);
        $user_record->attachRole($roleRecord);

        return $user_record;
    }

    public function assertSuccessResponse($response, $message)
    {
        $this->assertTrue($response->success);
        $this->assertEquals($message, $response->message);
    }

    public function assertExceptionMessage($response, $message)
    {
        $this->assertEquals($message, $response->exception->getMessage());
    }
}
