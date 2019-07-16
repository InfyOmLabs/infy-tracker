<?php

namespace App\Http\Controllers;

use App\Models\Comment;
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
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(Request $request)
    {
        $comment = $this->taskRepository->addComment($request->all());

        return $this->sendResponse(['comment' => $comment], 'Comment has been added successfully.');
    }

    /**
     * @param Comment $comment
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment(Comment $comment)
    {
        $comment->delete();

        return $this->sendSuccess('Comment has been deleted successfully.');
    }

    /**
     * @param Comment $comment
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editComment(Comment $comment, Request $request)
    {
        $comment->comment = htmlentities($request->get('comment'));
        $comment->save();

        return $this->sendSuccess('Comment has been updated successfully.');
    }
}
