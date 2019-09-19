<?php

namespace Tests\Controllers\Validations;

use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class TagControllerValidationTest.
 */
class TagControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_create_tag_fails_when_name_is_not_passed()
    {
        $this->post(route('tags.store'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_tag_fails_when_same_name_is_already_exist()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $this->post(route('tags.store'), ['name' => $tag->name])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    /** @test */
    public function it_can_create_tag()
    {
        $this->post(route('tags.store'), ['name' => 'random tag'])
            ->assertSessionHasNoErrors();

        $tag = Tag::whereName('random tag')->first();

        $this->assertNotEmpty($tag);
        $this->assertEquals('random tag', $tag->name);
    }

    /** @test */
    public function test_update_tag_fails_when_name_is_not_passed()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $this->put(route('tags.update', $tag->id), ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_tag_fails_when_same_name_is_already_exist()
    {
        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $this->put(route('tags.update', $tag2->id), ['name' => $tag1->name])
            ->assertSessionHasErrors(['name' => 'The name has already been taken.']);
    }

    /** @test */
    public function it_can_update_tag_with_valid_input()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $this->put(route('tags.update', $tag->id), ['name' => 'Any Dummy Name'])
            ->assertSessionHasNoErrors();

        $this->assertEquals('Any Dummy Name', $tag->fresh()->name);
    }
}
