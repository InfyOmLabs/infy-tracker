<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Queries\TaskDataTable;
use App\Repositories\TagRepository;
use App\Repositories\TaskRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class TaskController.
 */
class TaskController extends AppBaseController
{
    /** @var TaskRepository */
    private $taskRepository;

    public function __construct(TaskRepository $taskRepo)
    {
        $this->taskRepository = $taskRepo;
    }

    /**
     * Display a listing of the Task.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new TaskDataTable())->get($request->only([
                'name',
                'filter_project',
                'filter_status',
                'filter_user',
                'due_date_filter',
            ])))->editColumn('title', function (Task $task) {
                return $task->prefix_task_number.' '.$task->title;
            })->filterColumn('title', function (Builder $query, $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhereRaw(
                            "concat(ifnull(p.prefix,''),'-',ifnull(tasks.task_number,'')) LIKE ?",
                            ["%$search%"]
                        );
                });
            })
                ->make(true);
        }
        $taskData = $this->taskRepository->getTaskData();

        return view('tasks.index')->with($taskData);
    }

    /**
     * Store a newly created Task in storage.
     *
     * @param CreateTaskRequest $request
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function store(CreateTaskRequest $request)
    {
        $input = $request->all();

        $this->taskRepository->store($this->fill($input));

        return $this->sendSuccess('Task created successfully.');
    }

    private function fill($input)
    {
        $input['status'] = (isset($input['status']) && !empty($input['status'])) ? $input['status'] : Task::STATUS_ACTIVE;
        $input['description'] = is_null($input['description']) ? '' : $input['description'];

        return $input;
    }

    /**
     * @param string $slug
     *
     * @return Factory|JsonResponse|View
     */
    public function show($slug)
    {
        if (count(explode('-', $slug)) != 2) {
            return redirect()->back();
        }

        $projectPrefix = explode('-', $slug)[0];
        $taskNumber = explode('-', $slug)[1];

        $task = $this->taskRepository->show($projectPrefix, $taskNumber);
        if (empty($task)) {
            return redirect()->back();
        }

        $taskData = $this->taskRepository->getTaskData();
        $attachmentUrl = url(Task::PATH);

        return view('tasks.show', compact('task', 'attachmentUrl'))->with($taskData);
    }

    /**
     * Show the form for editing the specified Task.
     *
     * @param Task $task
     *
     * @return JsonResponse
     */
    public function edit(Task $task)
    {
        $task->tags;
        $task->project;
        $task->taskAssignee;
        $task->attachments;

        /** @var TagRepository $tagRepo */
        $tagRepo = app(TagRepository::class);
        $data['tags'] = $tagRepo->getTagList();
        $data['task'] = $task;
        $task->description = htmlspecialchars_decode($task->description);

        return $this->sendResponse($data, 'Task retrieved successfully.');
    }

    /**
     * Update the specified Task in storage.
     *
     * @param Task              $task
     * @param UpdateTaskRequest $request
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function update(Task $task, UpdateTaskRequest $request)
    {
        $input = $request->all();

        $this->taskRepository->update($this->fill($input), $task->id);

        return $this->sendSuccess('Task updated successfully.');
    }

    /**
     * Remove the specified Task from storage.
     *
     * @param Task $task
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(Task $task)
    {
        if ($task->timeEntries()->count() > 0) {
            return $this->sendError('Task has one or more time entries.');
        }

        $task->update(['deleted_by' => getLoggedInUserId()]);
        $task->delete();

        return $this->sendSuccess('Task deleted successfully.');
    }

    /**
     * @param Task $task
     *
     * @return JsonResponse
     */
    public function updateStatus(Task $task)
    {
        $this->taskRepository->updateStatus($task->id);

        return $this->sendSuccess('Task status Update successfully.');
    }

    /**
     * @param Task    $task
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getTaskDetails(Task $task, Request $request)
    {
        $taskDetails = $this->taskRepository->getTaskDetails($task->id, $request->all());

        return $this->sendResponse($taskDetails, 'Task retrieved successfully.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function myTasks(Request $request)
    {
        $input = $request->only('project_id');

        $timerDetails = $this->taskRepository->myTasks($input);

        return $this->sendResponse($timerDetails, 'My tasks retrieved successfully.');
    }

    /**
     * @param TaskAttachment $taskAttachment
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function deleteAttachment(TaskAttachment $taskAttachment)
    {
        $this->taskRepository->deleteFile($taskAttachment->id);

        return $this->sendSuccess('File has been deleted successfully.');
    }

    /**
     * @param Task    $task
     * @param Request $request
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function addAttachment(Task $task, Request $request)
    {
        $input = $request->all();

        try {
            $result = $this->taskRepository->uploadFile($task->id, $input['file']);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }

        return $this->sendResponse($result, 'File has been uploaded successfully.');
    }

    /**
     * @param Task $task
     *
     * @return JsonResponse
     */
    public function getAttachment(Task $task)
    {
        $result = $this->taskRepository->getAttachments($task->id);

        return $this->sendResponse($result, 'Task retrieved successfully.');
    }

    /**
     * @param Task $task
     *
     * @return JsonResponse
     */
    public function getCommentsCount(Task $task)
    {
        return $this->sendResponse($task->comments()->count(), 'Comments count retrieved successfully.');
    }

    /**
     * @param Task $task
     *
     * @return array
     */
    public function getTaskUsers(Task $task)
    {
        return $task->taskAssignee->pluck('name', 'id')->toArray();
    }
}
