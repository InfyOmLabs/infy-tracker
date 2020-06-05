<?php

namespace App\Repositories;

use App\Events\AddComment;
use App\Events\DeleteComment;
use App\Events\UpdateComment;
use App\Models\ActivityType;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskAttachment;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class TaskRepository.
 */
class TaskRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'status',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Task::class;
    }

    /**
     * @param int   $id
     * @param array $columns
     *
     * @return Task
     */
    public function find($id, $columns = ['*'])
    {
        return $this->findOrFail($id, ['tags', 'project', 'taskAssignee', 'attachments']);
    }

    /**
     * @param array $input
     *
     * @throws Exception
     *
     * @return Task|Collection
     */
    public function store($input)
    {
        $uniqueTaskNumber = $this->getUniqueTaskNumber($input['project_id']);
        $input['task_number'] = $uniqueTaskNumber;
        $this->validateTaskData($input);

        try {
            DB::beginTransaction();
            $input['created_by'] = getLoggedInUserId();
            $input['description'] = htmlentities($input['description']);
            $task = Task::create($input);

            if (isset($input['tags']) && !empty($input['tags'])) {
                $this->attachTags($task, $input['tags']);
            }

            if (isset($input['assignees']) && !empty($input['assignees'])) {
                array_push($input['assignees'], getLoggedInUserId());
                $task->taskAssignee()->sync($input['assignees']);
            } else {
                $task->taskAssignee()->sync(getLoggedInUserId());
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new BadRequestHttpException($e->getMessage());
        }

        return $task;
    }

    /**
     * @param array $input
     * @param int   $id
     *
     * @throws Exception
     *
     * @return true
     */
    public function update($input, $id)
    {
        $task = $this->findOrFail($id);
        $this->validateTaskData($input, $task);

        if ($task->project_id != $input['project_id']) {
            $uniqueTaskNumber = $this->getUniqueTaskNumber($input['project_id']);
            $input['task_number'] = $uniqueTaskNumber;
        }

        try {
            DB::beginTransaction();
            $input['description'] = htmlentities($input['description']);
            $task->update($input);

            $tags = !empty($input['tags']) ? $input['tags'] : [];
            $this->attachTags($task, $tags);

            $assignees = !empty($input['assignees']) ? $input['assignees'] : $input['assignees'] = getLoggedInUserId();
            $task->taskAssignee()->sync($assignees);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw new BadRequestHttpException($e->getMessage());
        }

        return true;
    }

    /**
     * @param array     $input
     * @param Task|null $task
     *
     * @return bool
     */
    public function validateTaskData($input, $task = null)
    {
        Project::findOrFail($input['project_id']);

        if (!empty($task) && $input['due_date'] == $task->due_date) {
            return true;
        }

        if (Carbon::parse($input['due_date'])->toDateString() < Carbon::now()->toDateString()) {
            throw new BadRequestHttpException('due_date must be greater than today\'s date.');
        }

        return true;
    }

    /**
     * @return array
     */
    public function getTaskData()
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $loginUserProjects = $projectRepo->getLoginUserAssignedProjectsArr();
        $data['projects'] = $loginUserProjects;

        /** @var TagRepository $tagRepo */
        $tagRepo = app(TagRepository::class);
        $data['tags'] = $tagRepo->getTagList();

        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        // return all users who has manage task permission otherwise return only logged in user
        if (getLoggedInUser()->hasPermissionTo('manage_all_tasks')) {
            $data['assignees'] = $userRepo->getUserList();
        } else {
            $data['assignees'] = $userRepo->getUserList()->only(getLoggedInUserId());
        }

        /** @var ActivityTypeRepository $activityTypeRepo */
        $activityTypeRepo = app(ActivityTypeRepository::class);
        $data['activityTypes'] = $activityTypeRepo->getActivityTypeList();

        $statusArr = Task::STATUS_ARR;
        asort($statusArr);
        $data['status'] = $statusArr;
        unset($statusArr[Task::STATUS_ALL]);
        $data['taskStatus'] = $statusArr;
        $data['tasks'] = $this->getTaskList(array_keys($loginUserProjects));
        $data['priority'] = Task::PRIORITY;
        $data['taskBadges'] = $this->getStatusBadge();

        return $data;
    }

    /**
     * @return array
     */
    public function getStatusBadge()
    {
        return [
            Task::STATUS_ACTIVE    => 'badge-light',
            Task::STATUS_COMPLETED => 'badge-success',
        ];
    }

    /**
     * @param array $projectIds
     *
     * @return mixed
     */
    public function getTaskList($projectIds = [])
    {
        $query = Task::orderBy('title');
        if (!empty($projectIds)) {
            $query = $query->whereIn('project_id', $projectIds);
        }

        return $query->pluck('title', 'id');
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function updateStatus($id)
    {
        $task = Task::findOrFail($id);
        $status = ($task->status == Task::STATUS_COMPLETED) ? Task::STATUS_ACTIVE : Task::STATUS_COMPLETED;
        $task->update(['status' => $status]);

        return true;
    }

    /**
     * @param Task  $task
     * @param array $tags
     *
     * @return bool|void
     */
    public function attachTags($task, $tags)
    {
        $newTags = collect($tags)->filter(function ($field) {
            return !is_numeric($field);
        });

        if (!count($newTags)) {
            $task->tags()->sync($tags);

            return;
        }

        $existingTags = collect($tags)->filter(function ($field) {
            return is_numeric($field);
        });
        $task->tags()->sync($existingTags);

        $tagIds = [];
        foreach ($newTags as $tag) {
            $tagIds[] = Tag::create([
                'name'       => $tag,
                'created_by' => getLoggedInUserId(),
            ])->id;
        }
        $task->tags()->attach($tagIds);

        return true;
    }

    /**
     * @param int   $id
     * @param array $input
     *
     * @return Task
     */
    public function getTaskDetails($id, $input = [])
    {
        $task = Task::with([
            'timeEntries' => function (HasMany $query) use ($input) {
                if (isset($input['user_id']) && $input['user_id'] > 0) {
                    $query->where('time_entries.user_id', '=', $input['user_id']);
                }

                if (!empty($input['start_time']) && !empty($input['end_time'])) {
                    $query->whereBetween('start_time', [$input['start_time'], $input['end_time']]);
                }
                $query->with('user');
            }, 'project',
        ])->findOrFail($id);

        $minutes = $task->timeEntries->pluck('duration')->sum();
        $totalDuration = 0;
        if ($minutes > 1) {
            $totalDuration = sprintf('%02d Hours and %02d Minutes', floor($minutes / 60), $minutes % 60);
        }
        $task->totalDuration = $totalDuration;
        $task->totalDurationMin = $minutes;

        return $task;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    public function myTasks($input = [])
    {
        $user = getLoggedInUser();
        /** @var Builder|Task $query */
        $query = Task::whereNotIn('status', [Task::STATUS_COMPLETED]);
        if (!$user->can('manage_projects')) {
            $query = $query->whereHas('taskAssignee', function (Builder $query) {
                $query->where('user_id', getLoggedInUserId());
            });
        }

        if (!empty($input['project_id'])) {
            $query->ofProject($input['project_id']);
        }

        $assignedTasks = $query->orderBy('title')->get(['title', 'id']);

        return [
            'activities' => ActivityType::orderBy('name')->get(['name', 'id']),
            'tasks'      => $assignedTasks,
        ];
    }

    /**
     * @param int $projectId
     *
     * @return int|string|null
     */
    public function getUniqueTaskNumber($projectId)
    {
        /** @var Task $task */
        $task = Task::withTrashed()->ofProject($projectId)->where('task_number', '!=', '')->orderByDesc('task_number')
            ->first();

        $uniqueNumber = (empty($task)) ? 1 : $task->task_number + 1;
        $isUnique = false;
        while (!$isUnique) {
            $task = Task::ofProject($projectId)->where('task_number', '=', $uniqueNumber)->first();
            if (empty($task)) {
                $isUnique = true;
            } else {
                $uniqueNumber++;
            }
        }

        return $uniqueNumber;
    }

    /**
     * @param string $projectPrefix
     * @param string $taskNumber
     *
     * @return Task|void
     */
    public function show($projectPrefix, $taskNumber)
    {
        /** @var Project $project */
        $project = Project::wherePrefix($projectPrefix)->first();
        if (empty($project)) {
            return;
        }

        /** @var Task $task */
        $task = Task::ofProject($project->id)->whereTaskNumber($taskNumber)
            ->with([
                'tags',
                'project',
                'taskAssignee',
                'attachments',
                'comments',
                'comments.createdUser',
                'timeEntries',
            ])->first();

        if (!empty($task)) {
            return $task;
        }
    }

    /**
     * @param int          $id
     * @param UploadedFile $file
     *
     * @throws Exception
     *
     * @return TaskAttachment
     */
    public function uploadFile($id, $file)
    {
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['xls', 'pdf', 'doc', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'])) {
            throw new UnprocessableEntityHttpException('You can not upload this file.');
        }

        $destinationPath = public_path(Task::PATH);
        $task = $this->findOrFail($id);

        $fileName = TaskAttachment::makeAttachment($file, TaskAttachment::PATH);
        $attachment = new TaskAttachment(['task_id' => $task->id, 'file' => $fileName]);

        try {
            $task->attachments()->save($attachment);

            return $attachment;
        } catch (Exception $e) {
            if (file_exists($destinationPath.'/'.$fileName)) {
                unlink($destinationPath.'/'.$fileName);
            }

            throw new UploadException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $id
     *
     * @throws Exception
     *
     * @return bool
     */
    public function deleteFile($id)
    {
        /** @var TaskAttachment $attachment */
        $attachment = TaskAttachment::find($id);
        if (empty($attachment)) {
            throw new BadRequestHttpException('File not found.');
        }

        $attachment->deleteAttachment();
        $attachment->delete();
        if (file_exists($attachment->file_url)) {
            unlink($attachment->file_url);
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getAttachments($id)
    {
        /** @var Task $task */
        $task = $this->find($id);
        $attachments = $task->attachments;

        $result = [];

        foreach ($attachments as $attachment) {
            $obj['id'] = $attachment->id;
            $obj['name'] = $attachment->file;
            $obj['size'] = filesize($attachment->file_path);
            $obj['url'] = $attachment->file_url;
            $result[] = $obj;
        }

        return $result;
    }

    /**
     * @param array $input
     *
     * @return Comment
     */
    public function addComment($input)
    {
        $input['created_by'] = Auth::id();
        $comment = Comment::create($input);

        return Comment::with('createdUser')->findOrFail($comment->id);
    }

    /**
     * @param Comment $comment
     */
    public function addCommentBroadCast($comment)
    {
        broadcast(new AddComment($comment))->toOthers();
    }

    /**
     * @param Comment $comment
     */
    public function deleteCommentBroadCast($comment)
    {
        broadcast(new DeleteComment($comment))->toOthers();
    }

    /**
     * @param Comment $comment
     */
    public function editCommentBroadCast($comment)
    {
        broadcast(new UpdateComment($comment))->toOthers();
    }
}
