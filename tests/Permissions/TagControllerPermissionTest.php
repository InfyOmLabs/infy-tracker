<?php

namespace Tests\Permissions;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TagControllerPermissionTest
 */
class TagControllerPermissionTest extends TestCase
{
    use DatabaseTransactions;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     */
    public function test_can_get_tags_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_tags']);

        $response = $this->getJson(route('tags.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_not_allow_to_get_tags_without_permission()
    {
        $response = $this->get(route('tags.index'));

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_create_tag_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_tags']);

        $tag = factory(Tag::class)->raw();

        $response = $this->postJson(route('tags.store'), $tag);

        $this->assertSuccessMessageResponse($response, 'Tag created successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_create_tag_without_permission()
    {
        $tag = factory(Tag::class)->raw();

        $response = $this->post(route('tags.store'), $tag);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_update_tag_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_tags']);

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();
        $updateTag = factory(Tag::class)->raw(['id' => $tag->id]);

        $response = $this->putJson(route('tags.update', $tag->id), $updateTag);

        $this->assertSuccessMessageResponse($response, 'Tag updated successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_update_tag_without_permission()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();
        $updateTag = factory(Tag::class)->raw(['id' => $tag->id]);

        $response = $this->put(route('tags.update', $tag->id), $updateTag);

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function test_can_delete_tag_with_valid_permission()
    {
        $this->attachPermissions($this->user->id, ['manage_tags']);

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $response = $this->deleteJson(route('tags.destroy', $tag->id));

        $this->assertSuccessMessageResponse($response, 'Tag deleted successfully.');
    }

    /**
     * @test
     */
    public function test_not_allow_to_delete_tag_without_permission()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $response = $this->delete(route('tags.destroy', $tag->id));

        $response->assertStatus(403);
    }
}
