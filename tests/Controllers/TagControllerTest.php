<?php

namespace Tests\Controllers;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $tagRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function mockRepository()
    {
        $this->tagRepository = \Mockery::mock(TagRepository::class);
        app()->instance(TagRepository::class, $this->tagRepository);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    /** @test */
    public function it_can_store_tag()
    {
        $this->mockRepository();

        $tag = factory(Tag::class)->raw();

        $this->tagRepository->shouldReceive('store')
            ->once()
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
        $this->mockRepository();

        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $this->tagRepository->shouldReceive('update')
            ->once()
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
