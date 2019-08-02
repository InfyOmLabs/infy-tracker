<?php

namespace Tests\Repositories;

use App\Models\Project;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

        $userList = $this->userRepo->getUserList([$project1->id, $project2->id]);

        $this->assertCount(2, $userList);
        $this->assertContains($users[0]->name, $userList);
        $this->assertContains($users[1]->name, $userList);
    }

    /** @test */
    public function it_can_get_all_users()
    {
        /** @var Collection $users */
        $users = factory(User::class)->times(2)->create();

        $userList = $this->userRepo->getUserList();

        // +1 default user
        $this->assertCount(3, $userList);
        $this->assertContains($users[0]->name, $userList);
        $this->assertContains($users[1]->name, $userList);
    }

    /** @test */
    public function test_can_activate_user()
    {
        /** @var User $user */
        $user = factory(User::class)->create(['is_active' => false]);

        $user = $this->userRepo->activeDeActiveUser($user->id);

        $this->assertEquals($user->id, $user->id);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function test_can_de_activate_user()
    {
        /** @var User $farhan */
        $farhan = factory(User::class)->create(['is_active' => true]);

        $user = $this->userRepo->activeDeActiveUser($farhan->id);

        $this->assertEquals($user->id, $user->id);
        $this->assertFalse($user->is_active);
    }
}
