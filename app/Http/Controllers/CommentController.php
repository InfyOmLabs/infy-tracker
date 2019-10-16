<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class CommentController.
 */
class CommentController extends AppBaseController
{
    /** @var TaskRepository */
    private $taskRepository;

    public function __construct(TaskRepository $taskRepo)
    {
        $this->taskRepository = $taskRepo;
    }

    /**
     * @param Task    $task
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addComment(Task $task, Request $request)
    {
        $input = $request->only(['comment']);
        $input['task_id'] = $task->id;

        $comment = $this->taskRepository->addComment($input);
        $this->taskRepository->addCommentBroadCast($comment);

        return $this->sendResponse(['comment' => $comment], 'Comment has been added successfully.');
    }

    /**
     * @param Task    $task
     * @param Comment $comment
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function deleteComment(Task $task, Comment $comment)
    {
        if ($comment->task_id != $task->id || $comment->created_by != Auth::user()->id) {
            throw new UnprocessableEntityHttpException('Unable to delete comment.');
        }

        $this->taskRepository->deleteCommentBroadCast($comment);
        $comment->delete();

        return $this->sendSuccess('Comment has been deleted successfully.');
    }

    /**
     * @param Task    $task
     * @param Comment $comment
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editComment(Task $task, Comment $comment, Request $request)
    {
        if ($comment->task_id != $task->id || $comment->created_by != Auth::user()->id) {
            throw new UnprocessableEntityHttpException('Unable to update comment.');
        }

        $comment->comment = $request->get('comment');
        $comment->save();
        $this->taskRepository->editCommentBroadCast($comment);

        return $this->sendSuccess('Comment has been updated successfully.');
    }
}
