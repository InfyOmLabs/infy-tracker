<?php

namespace Tests\Controllers;

use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

/**
 * Class TagControllerTest.
 */
class TagControllerTest extends TestCase
{
    use DatabaseTransactions;
    use MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function it_can_store_tag()
    {
        $this->mockRepo(self::$tag);

        $tag = factory(Tag::class)->raw();

        $this->tagRepository->expects('store')->with($tag);

        $response = $this->postJson(route('tags.store'), $tag);

        $this->assertSuccessMessageResponse($response, 'Tag created successfully.');
    }

    /** @test */
    public function it_can_retrieve_tag()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $response = $this->getJson(route('tags.edit', $tag->id));

        $this->assertSuccessDataResponse($response, $tag->toArray(), 'Tag retrieved successfully.');
    }

    /** @test */
    public function it_can_update_tag()
    {
        $this->mockRepo(self::$tag);

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $this->tagRepository->expects('update')->withArgs([['name' => 'Dummy Tag'], $tag->id]);

        $response = $this->putJson(route('tags.update', $tag->id), [
            'name' => 'Dummy Tag',
        ]);

        $this->assertSuccessMessageResponse($response, 'Tag updated successfully.');
    }

    /** @test */
    public function it_can_delete_tag()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $response = $this->deleteJson(route('tags.destroy', $tag->id));

        $this->assertSuccessMessageResponse($response, 'Tag deleted successfully.');

        $response = $this->getJson(route('tags.edit', $tag->id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Tag not found.',
        ]);
    }
}
