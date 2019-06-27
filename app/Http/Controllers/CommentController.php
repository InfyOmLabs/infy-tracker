<?php

namespace App\Http\Controllers;

use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends AppBaseController
{
    /** @var  TaskRepository */
    private $taskRepository;

    public function __construct(TaskRepository $taskRepo)
    {
        $this->taskRepository = $taskRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(Request $request) {
        $comment = $this->taskRepository->addComment($request->all());
        return $this->sendResponse(['comment' => $comment], 'Comment has been added successfully.');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteComment($id){
        Comment::findOrFail($id)->delete();
        return $this->sendSuccess('Comment has been deleted successfully.');
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editComment($id, Request $request){
        $comment = Comment::findOrFail($id);
        $comment->comment = htmlentities($request->get('comment'));
        $comment->save();
        return $this->sendSuccess('Comment has been updated successfully.');
    }
}
