<?php

namespace Tests\Unit\Helpers;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function explode_trim_remove_empty_values_from_array()
    {
        $result = explode_trim_remove_empty_values_from_array(' hello   ');
        $this->assertEquals(['hello'], $result);

        $result = explode_trim_remove_empty_values_from_array(' hello,test   ');
        $this->assertEquals(['hello', 'test'], $result);

        $result = explode_trim_remove_empty_values_from_array(' hello , test   ');
        $this->assertEquals(['hello', 'test'], $result);

        $result = explode_trim_remove_empty_values_from_array(' hello , ,, test   ');
        $this->assertEquals(['hello', 'test'], $result);

        $result = explode_trim_remove_empty_values_from_array(' hello ,, ,0, test   ');
        $this->assertEquals(['hello', '0', 'test'], $result);
    }

    /** @test */
    public function test_return_user_avatar_url()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = getUserImageInitial($user->id, $user->name);

        $this->assertNotEmpty($response);
        $this->assertStringContainsString($user->name, $response);
    }

    /** @test */
    public function test_return_true_when_user_has_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $this->attachPermissions($user->id, ['manage_users']);

        $response = authUserHasPermission('manage_users');

        $this->assertTrue($response);
    }

    /** @test */
    public function test_return_false_when_user_has_no_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = authUserHasPermission('manage_users');

        $this->assertFalse($response);
    }
}
