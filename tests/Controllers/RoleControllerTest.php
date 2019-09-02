<?php

namespace Tests\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;


class RoleControllerTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
        $this->loggedInUserId = $this->makeUserWithPermissions(['manage_roles'], ['name' => 'admin'])->id;
    }

    /** @test */
    public function it_can_delete_role()
    {
        /** @var Role $role */
        $role = factory(Role::class)->create();

        $response = $this->TestDeleteJson(route('roles.destroy', $role->id));

        $this->assertSuccessResponse($response, 'Role deleted successfully.');
    }

    /** @test */
    public function it_can_not_delete_role_when_role_assigned_to_user()
    {
        $this->markTestSkipped('skip as of now');
        /** @var Role $role */
        $role = factory(Role::class)->create();
        $farhan = factory(User::class)->create();
        $role->users()->sync([$farhan->id]);

        $response = $this->TestDeleteJson(route('roles.destroy', $role->id));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'This user role could not be deleted, because it’s assigned to a user.',
        ]);
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
}
