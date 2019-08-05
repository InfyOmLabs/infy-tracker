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

        $this->taskRepository->shouldReceive('addComment')
            ->once()
            ->with(['comment' => $comment->comment, 'task_id' => $comment->task_id]);

        $response = $this->postJson("tasks/{$comment->task_id}/comments", [
            'comment' => $comment->comment,
        ]);

        $this->assertSuccessMessageResponse($response, 'Comment has been added successfully.');
    }
}
