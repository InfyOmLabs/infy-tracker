<?php

namespace Tests\Repositories;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TagRepositoryTest
 */
class TagRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @var TagRepository */
    protected $tagRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->tagRepo = app(TagRepository::class);
    }

    /** @test */
    public function it_can_retrieve_tags_list()
    {
        /** @var Collection $tags */
        $tags = factory(Tag::class)->times(3)->create();

        $tagResult = $this->tagRepo->getTagList();

        $this->assertCount(3, $tagResult);

        $tags->map(function (Tag $tag) use ($tagResult) {
            $this->assertContains($tag->name, $tagResult);
        });
    }

    /** @test */
    public function it_can_store_bulk_tags()
    {
        $tags = [
            'name'      => 'random,string',
            'bulk_tags' => true,
        ];

        $tagResult = $this->tagRepo->store($tags);

        $this->assertTrue($tagResult);

        $tags = Tag::get();
        $this->assertCount(2, $tags);

        $this->assertEquals('random', $tags[0]['name']);
        $this->assertEquals('string', $tags[1]['name']);
    }
}
