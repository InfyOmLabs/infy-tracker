<?php

namespace Tests\Controllers\Validations;

use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
        $this->post('tags', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_tag_fails_when_same_name_is_already_exist()
    {
        $tag = factory(Tag::class)->create();

        $this->post('tags/'.$tag->id, ['name' => 'Tag'])
            ->assertSessionHasErrors(['name' => 'Tag with same name already exist']);
    }

    /** @test */
    public function test_update_tag_fails_when_name_is_not_passed()
    {
        $tag = factory(Tag::class)->create();

        $this->post('tags/'.$tag->id.'/update', ['name' => ''])
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function test_update_tag_fails_when_same_name_is_already_exist()
    {
        $tag1 = factory(Tag::class)->create();
        $tag2 = factory(Tag::class)->create();

        $this->post('tags/'.$tag2->id.'/update', ['name' => $tag1->name])
            ->assertSessionHasErrors(['name' => 'Tag with same name already exist']);
    }

    /** @test */
    public function it_can_update_tag_with_valid_input()
    {
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create();

        $inputs = array_merge($tag->toArray(), ['name' => 'Any Dummy Name']);

        $this->post('tags/'.$tag->id.'/update', $inputs)
            ->assertSessionHasNoErrors();

        $this->assertEquals('Any Dummy Name', $tag->fresh()->name);
    }
}
