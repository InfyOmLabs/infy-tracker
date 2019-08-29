<?php

namespace Tests\Controllers;

use App\Models\Comment;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @var MockInterface */
    protected $taskRepository;

    public function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    private function mockRepository()
    {
        $this->taskRepository = \Mockery::mock(TaskRepository::class);
        app()->instance(TaskRepository::class, $this->taskRepository);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->signInWithDefaultAdminUser();
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest']);
    }

    /** @test */
    public function test_can_add_comment()
    {
        $this->mockRepository();

        $comment = factory(Comment::class)->make();

        $this->taskRepository->expects('addComment')
            ->with(['comment' => $comment->comment, 'task_id' => $comment->task_id])
            ->andReturn($comment);

        $this->taskRepository->expects('addCommentBroadCast')
            ->with($comment);

        $response = $this->postJson("tasks/{$comment->task_id}/comments", [
            'comment' => $comment->comment,
        ]);

        $this->assertSuccessMessageResponse($response, 'Comment has been added successfully.');
    }

    /** @test */
    public function test_can_update_comment_with_valid_input()
    {
        $this->mockRepository();

        $comment = factory(Comment::class)->create(['created_by' => $this->loggedInUserId]);
        $newText = $this->faker->text;

        $this->taskRepository->expects('editCommentBroadCast');

        $result = $this->post('tasks/'.$comment->task_id.'/comments/'.$comment->id.'/update', [
            'comment' => $newText,
        ]);

        $this->assertSuccessMessageResponse($result, 'Comment has been updated successfully.');
        $this->assertEquals($newText, $comment->fresh()->comment);
    }

    /** @test */
    public function test_can_delete_given_comment()
    {
        $this->mockRepository();

        $comment = factory(Comment::class)->create(['created_by' => $this->loggedInUserId]);

        $this->taskRepository->expects('deleteCommentBroadCast');

        $result = $this->delete('tasks/'.$comment->task_id.'/comments/'.$comment->id);

        $this->assertSuccessMessageResponse($result, 'Comment has been deleted successfully.');
        $this->assertEmpty(Comment::find($comment->id));
    }
}
