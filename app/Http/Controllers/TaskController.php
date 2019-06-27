<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Queries\TaskDataTable;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TaskController extends AppBaseController
{
    /** @var  TaskRepository */
    private $taskRepository;
    private $userRepo;

    public function __construct(TaskRepository $taskRepo, UserRepository $userRepository)
    {
        $this->taskRepository = $taskRepo;
        $this->userRepo = $userRepository;
    }

    /**
     * Display a listing of the Task.
     * @param Request $request
     *
     * @return Factory|View
     * @throws Exception
     *
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new TaskDataTable())->get($request->only([
                'name',
                'filter_project',
                'filter_status',
                'filter_user',
            ])))->make(true);
        }
        $taskData = $this->taskRepository->getTaskData();

        return view('tasks.index')->with($taskData);
    }

    /**
     * Store a newly created Task in storage.
     *
     * @param CreateTaskRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(CreateTaskRequest $request)
    {
        $input = $request->all();
        /** @var Task $task */
        $task = $this->taskRepository->store($this->fill($input));
        $indexNumber = $this->taskRepository->getIndex($task->project_id);
        $task->update(['task_number' => $indexNumber]);
        return $this->sendSuccess('Task created successfully.');
    }

    private function fill($input)
    {
        $input['status'] = (isset($input['status']) && !empty($input['status'])) ? 1 : 0;
        $input['description'] = is_null($input['description']) ? '' : $input['description'];
        return $input;
    }

    /**
     * @param $id
     * @return Factory|JsonResponse|View
     */
    public function show($id)
    {
        if(count(explode('-',$id)) != 2){
            return redirect()->back();
        }
        $projectPrefix = explode('-',$id)[0];
        $taskNumber = explode('-',$id)[1];
        /** @var Project $project */
        $project = Project::wherePrefix($projectPrefix)->first();
        /** @var Task $task */
        $task = Task::whereTaskNumber($taskNumber)->whereProjectId($project->id)->with(['tags', 'project', 'taskAssignee', 'attachments', 'comments', 'comments.createdUser','timeEntries'])->first();
        $taskData = $this->taskRepository->getTaskData();
        $attachmentPath = Task::PATH;
        $attachmentUrl = url($attachmentPath);

        return view('tasks.show', compact('task', 'attachmentUrl'))->with($taskData);
    }

    /**
     * Show the form for editing the specified Task.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function edit($id)
    {
        $task = $this->taskRepository->find($id);

        return $this->sendResponse($task, 'Task retrieved successfully.');
    }

    /**
     * Update the specified Task in storage.
     *
     * @param int $id
     * @param UpdateTaskRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function update($id, UpdateTaskRequest $request)
    {
        $input = $request->all();

        $this->taskRepository->update($this->fill($input), $id);

        return $this->sendSuccess('Task updated successfully.');
    }

    /**
     * Remove the specified Task from storage.
     * @param int $id
     *
     * @return JsonResponse
     * @throws Exception
     *
     */
    public function destroy($id)
    {
        /** @var Task $task */
        $task = Task::withCount('timeEntries')->find($id);
        if (empty($task)) {
            return $this->sendError('Task not found.', Response::HTTP_NOT_FOUND);
        }

        if ($task->time_entries_count > 0) {
            return $this->sendError('Task has one or more time entries');
        }

        $task->update(['deleted_by' => getLoggedInUserId()]);
        $this->taskRepository->delete($id);

        return $this->sendSuccess('Task deleted successfully.');
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function updateStatus($id)
    {
        $this->taskRepository->updateStatus($id);

        return $this->sendSuccess('Task status Update successfully.');
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getTaskDetails($id)
    {
        $taskDetails = $this->taskRepository->getTaskDetails($id);

        return $this->sendResponse($taskDetails, 'Task retrieved successfully.');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function myTasks(Request $request)
    {
        $input = $request->only('project_id');
        $timerDetails = $this->taskRepository->myTasks($input);

        return $timerDetails;
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAttachment($id, Request $request)
    {
        $this->taskRepository->deleteFile($id, $request->all());
        return $this->sendSuccess('File has been deleted successfully.');
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addAttachment($id, Request $request)
    {
        $input = $request->all();
        $file = $input['file'];
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['xls', 'pdf', 'doc', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'])) {
            return $this->sendError('You can not upload this file.');
        }
        $fileName = $this->taskRepository->uploadFile($id, $input['file']);
        return $this->sendResponse(['fileName' => $fileName], 'File has been uploaded successfully.');
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getAttachment($id)
    {
        $result = $this->taskRepository->getAttachments($id);
        return $this->sendResponse($result, 'Task retrieved successfully.');
    }

}
