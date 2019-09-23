<?php

namespace Tests\Models;

use App\Models\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_return_user_avatar()
    {
        factory(Comment::class)->create();

        $comment = Comment::first();

        $this->assertNotEmpty($comment->user_avatar);
    }
}
