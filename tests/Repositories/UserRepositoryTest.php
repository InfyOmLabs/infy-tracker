<?php

namespace Tests\Repositories;

use App\Models\Project;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Class UserRepositoryTest.
 */
class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var UserRepository */
    protected $userRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepo = app(UserRepository::class);
    }

    /** @test */
    public function it_can_get_users_of_given_projects()
    {
        /** @var Collection $users */
        $users = factory(User::class)->times(2)->create();

        $project1 = factory(Project::class)->create();
        $project2 = factory(Project::class)->create();

        $users[0]->projects()->attach($project1->id);
        $users[1]->projects()->attach($project2->id);

        $getUsers = $this->userRepo->getUserList([$project1->id, $project2->id]);

        $this->assertCount(2, $getUsers);
        $this->assertContains($users[0]->name, $getUsers);
        $this->assertContains($users[1]->name, $getUsers);
    }

    /** @test */
    public function it_can_get_all_users()
    {
        /** @var Collection $users */
        $users = factory(User::class)->times(2)->create();

        $getUsers = $this->userRepo->getUserList();

        // +1 default user
        $this->assertCount(3, $getUsers);
        $this->assertContains($users[0]->name, $getUsers);
        $this->assertContains($users[1]->name, $getUsers);
    }

    /** @test */
    public function test_can_activate_user()
    {
        /** @var Collection $user */
        $user = factory(User::class)->create(['is_active' => false]);

        $getUser = $this->userRepo->activeDeActiveUser($user->id);

        $this->assertEquals($user->id, $getUser->id);
        $this->assertTrue($getUser->is_active);
    }

    /** @test */
    public function test_can_de_activate_user()
    {
        /** @var Collection $user */
        $user = factory(User::class)->create(['is_active' => true]);

        $getUser = $this->userRepo->activeDeActiveUser($user->id);

        $this->assertEquals($user->id, $getUser->id);
        $this->assertFalse($getUser->is_active);
    }

    /** @test */
    public function test_can_set_user_password()
    {
        /** @var User $farhan */
        $farhan = factory(User::class)->create();

        $password = 12345678;
        $response = $this->userRepo->setUserPassword([
            'password' => $password,
            'user_id'  => $farhan->id,
        ]);

        $this->assertTrue($response);

        $user = User::findOrFail($farhan->id);
        $this->assertTrue($user->set_password);
        $this->assertTrue(Hash::check($password, $user->password));
    }
}
