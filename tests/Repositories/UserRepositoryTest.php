<?php

namespace Tests\Repositories;

use App\Models\Project;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var UserRepositoryTest */
    protected $userRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepo = app(UserRepository::class);
    }

    /** @test */
    public function test_can_retrieve_users()
    {
        /** @var Collection $users */
        $users = factory(User::class)->times(2)->create();

        $project1 = factory(Project::class)->create(['created_by' => $users[0]->id]);
        $project2 = factory(Project::class)->create(['created_by' => $users[1]->id]);

        DB::table('project_user')->insert([
            ['project_id' => $project1->id, 'user_id' => $users[0]->id],
            ['project_id' => $project2->id, 'user_id' => $users[1]->id],
        ]);

        $getUsers = $this->userRepo->getUserList([$project1->id, $project2->id]);

        $this->assertCount(2, $getUsers);
        $this->assertContains($users[0]->name, $getUsers);
        $this->assertContains($users[1]->name, $getUsers);
    }

    /** @test */
    public function test_can_active_user()
    {
        /** @var Collection $user */
        $user = factory(User::class)->create(['is_active' => false]);

        $getUser = $this->userRepo->activeDeActiveUser($user->id);

        $this->assertEquals($user->id, $getUser->id);
        $this->assertTrue($getUser->is_active);
    }

    /** @test */
    public function test_can_de_active_user()
    {
        /** @var Collection $user */
        $user = factory(User::class)->create(['is_active' => true]);

        $getUser = $this->userRepo->activeDeActiveUser($user->id);

        $this->assertEquals($user->id, $getUser->id);
        $this->assertFalse($getUser->is_active);
    }
}
