<?php

namespace Tests\Repositories;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
        /** @var Collection $tag */
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

        $getTags = Tag::get();
        $this->assertCount(2, $getTags);

        $explodeTags = explode(',', $tags['name']);

        $this->assertEquals($explodeTags[0], $getTags[0]['name']);
        $this->assertEquals($explodeTags[1], $getTags[1]['name']);
    }
}
