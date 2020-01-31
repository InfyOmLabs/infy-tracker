<?php

namespace Tests\Controllers;

use App\Models\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MockRepositories;

/**
 * Class CommentControllerTest.
 */
class CommentControllerTest extends TestCase
{
    use DatabaseTransactions;
    use MockRepositories;
    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_can_add_comment()
    {
        $this->mockRepo(self::$task);

        $comment = factory(Comment::class)->make();

        $this->taskRepository->expects('addComment')
            ->with(['comment' => $comment->comment, 'task_id' => $comment->task_id])
            ->andReturn($comment);

        $this->taskRepository->expects('addCommentBroadCast')
            ->with($comment);

        $response = $this->postJson(route('task.comments', $comment->task_id), [
            'comment' => $comment->comment,
        ]);

        $this->assertSuccessMessageResponse($response, 'Comment has been added successfully.');
    }

    /** @test */
    public function test_can_update_comment_with_valid_input()
    {
        $this->mockRepo(self::$task);

        /** @var Comment $comment */
        $comment = factory(Comment::class)->create(['created_by' => $this->loggedInUserId]);
        $newText = $this->faker->text;

        $this->taskRepository->expects('editCommentBroadCast');

        $result = $this->postJson(route('task.update-comment', [$comment->task_id, $comment->id]), [
            'comment' => $newText,
        ]);

        $this->assertSuccessMessageResponse($result, 'Comment has been updated successfully.');
        $this->assertEquals($newText, $comment->fresh()->comment);
    }

    /** @test */
    public function test_can_delete_given_comment()
    {
        $this->mockRepo(self::$task);

        /** @var Comment $comment */
        $comment = factory(Comment::class)->create(['created_by' => $this->loggedInUserId]);

        $this->taskRepository->expects('deleteCommentBroadCast');

        $result = $this->delete(route('task.delete-comment', [$comment->task_id, $comment->id]));

        $this->assertSuccessMessageResponse($result, 'Comment has been deleted successfully.');
        $this->assertEmpty(Comment::find($comment->id));
    }
}
