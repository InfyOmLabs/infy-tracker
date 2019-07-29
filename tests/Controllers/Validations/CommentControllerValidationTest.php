<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 27-07-2019
 * Time: 04:53 PM.
 */

namespace Tests\Controllers\Validations;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class CommentControllerValidationTest.
 */
class CommentControllerValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->signInWithDefaultAdminUser();
    }

    /** @test */
    public function test_delete_comment_fails_when_invalid_comment_id_passed()
    {
        $task = factory(Task::class)->create();
        $result = $this->delete('tasks/'.$task->id.'/comments/999');

        $result->assertStatus(404);
    }

    /** @test */
    public function test_unable_to_delete_comment_with_invalid_input()
    {
        $task = factory(Task::class)->create();
        $comment = factory(Comment::class)->create();

        $result = $this->delete('tasks/'.$task->id.'/comments/'.$comment->id);

        $this->assertExceptionMessage($result, 'Unable to delete comment.');
    }

    /** @test */
    public function test_can_delete_given_comment()
    {
        $comment = factory(Comment::class)->create();

        $result = $this->delete('tasks/'.$comment->task_id.'/comments/'.$comment->id);

        $this->assertSuccessMessageResponse($result, 'Comment has been deleted successfully.');
        $this->assertEmpty(Comment::find($comment->id));
    }

    /** @test */
    public function test_unable_to_update_comment_with_invalid_input()
    {
        $task = factory(Task::class)->create();
        $comment = factory(Comment::class)->create();

        $result = $this->post('tasks/'.$task->id.'/comments/'.$comment->id.'/update');

        $this->assertExceptionMessage($result, 'Unable to update comment.');
    }

    /** @test */
    public function test_can_update_comment_with_valid_input()
    {
        $comment = factory(Comment::class)->create();
        $newText = $this->faker->text;

        $result = $this->post('tasks/'.$comment->task_id.'/comments/'.$comment->id.'/update', ['comment' => $newText]);

        $this->assertSuccessMessageResponse($result, 'Comment has been updated successfully.');
        $this->assertEquals($newText, $comment->fresh()->comment);
    }
}
