<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;

class CommentController extends AppBaseController
{
    /** @var TaskRepository */
    private $taskRepository;

    public function __construct(TaskRepository $taskRepo)
    {
        $this->taskRepository = $taskRepo;
    }

    /**
     * @param Task $task
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(Task $task, Request $request)
    {
        $input = $request->only(['comment']);
        $input['task_id'] = $task->id;
        $comment = $this->taskRepository->addComment($input);

        return $this->sendResponse(['comment' => $comment], 'Comment has been added successfully.');
    }

    /**
     * @param Task $task
     * @param Comment $comment
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment(Task $task, Comment $comment)
    {
        $comment->delete();

        return $this->sendSuccess('Comment has been deleted successfully.');
    }

    /**
     * @param Task $task
     * @param Comment $comment
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editComment(Task $task, Comment $comment, Request $request)
    {
        $comment->comment = htmlentities($request->get('comment'));
        $comment->save();

        return $this->sendSuccess('Comment has been updated successfully.');
    }
}
