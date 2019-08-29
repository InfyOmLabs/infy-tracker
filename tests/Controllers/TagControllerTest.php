<?php

namespace Tests\Controllers;

use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

class TagControllerTest extends TestCase
{
    use DatabaseTransactions, MockRepositories;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    /** @test */
    public function it_can_store_tag()
    {
        $this->mockRepo([self::$tag]);

        $tag = factory(Tag::class)->raw();

        $this->tagRepository->expects('store')
            ->with($tag);

        $response = $this->postJson('tags', $tag);

        $this->assertSuccessMessageResponse($response, 'Tag created successfully.');
    }

    /** @test */
    public function it_can_retrieve_tag()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $response = $this->getJson('tags/'.$tag->id.'/edit');

        $this->assertSuccessDataResponse($response, $tag->toArray(), 'Tag retrieved successfully.');
    }

    /** @test */
    public function it_can_update_tag()
    {
        $this->mockRepo([self::$tag]);

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $this->tagRepository->expects('update')
            ->withArgs([['name' => 'Dummy Tag'], $tag->id]);

        $response = $this->putJson(
            'tags/'.$tag->id,
            ['name' => 'Dummy Tag']
        );

        $this->assertSuccessMessageResponse($response, 'Tag updated successfully.');
    }

    /** @test */
    public function it_can_delete_tag()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $response = $this->deleteJson('tags/'.$tag->id);

        $this->assertSuccessMessageResponse($response, 'Tag deleted successfully.');

        $response = $this->getJson('tags/'.$tag->id.'/edit');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Tag not found.',
        ]);
    }
}
