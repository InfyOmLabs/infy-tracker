<?php

namespace Tests\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_return_user_image_avatar()
    {
        $user = factory(User::class)->create();

        $user = User::first();

        $this->assertNotEmpty($user->img_avatar);
    }

    /** @test */
    public function test_return_user_image_path()
    {
        $user = factory(User::class)->create();

        $user = User::first();

        $this->assertNotEmpty($user->image_path);
    }
}
