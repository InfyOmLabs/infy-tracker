<?php

namespace App\Repositories;

use App\Models\ActivityType;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskAttachment;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use League\Container\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class TaskRepository
 * @package App\Repositories
 * @version May 3, 2019, 5:05 am UTC
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
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Task::class;
    }

    /**
     * @param int $id
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
     * @return bool
     */
    public function store($input)
    {
        $this->validateTaskData($input);

        try {
            DB::beginTransaction();
            $input['created_by'] = getLoggedInUserId();
            $task = Task::create($input);

            if (isset($input['tags']) && !empty($input['tags'])) {
                $this->attachTags($task, $input['tags']);
            }

            if (isset($input['assignees']) && !empty($input['assignees'])) {
                $task->taskAssignee()->sync($input['assignees']);
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw new BadRequestHttpException($e->getMessage());
        }

        return true;
    }

    /**
     * @param array $input
     * @param int $id
     *
     * @throws Exception
     *
     * @return true
     */
    public function update($input, $id)
    {
        $this->validateTaskData($input);
        $task = $this->findOrFail($id);

        try {
            DB::beginTransaction();
            $task->update($input);

            if (isset($input['tags']) && !empty($input['tags'])) {
                $this->attachTags($task, $input['tags']);
            } else {
                $this->attachTags($task, []);
            }

            if (isset($input['assignees']) && !empty($input['assignees'])) {
                $task->taskAssignee()->sync($input['assignees']);
            } else {
                $task->taskAssignee()->sync([]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new BadRequestHttpException($e->getMessage());
        }

        return true;
    }

    /**
     * @param array $input
     *
     * @return bool
     */
    public function validateTaskData($input)
    {
        if (Carbon::parse($input['due_date'])->toDateString() < Carbon::now()->toDateString()) {
            throw new BadRequestHttpException('due_date must be greater than today\'s date');
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
        $loginUserProjects = $projectRepo->getLoginUserAssignProjectsArr();
        $data['projects'] = $loginUserProjects;

        /** @var TagRepository $tagRepo */
        $tagRepo = app(TagRepository::class);
        $data['tags'] = $tagRepo->getTagList();

        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $data['assignees'] = $userRepo->getUserList();

        /** @var ActivityTypeRepository $activityTypeRepo */
        $activityTypeRepo = app(ActivityTypeRepository::class);
        $data['activityTypes'] = $activityTypeRepo->getActivityTypeList();

        $data['status'] = Task::STATUS_ARR;
        $data['tasks'] = $this->getTaskList($loginUserProjects);
        $data['priority'] = Task::PRIORITY;
        return $data;
    }

    /**
     * @param $loginUserProjects
     * @return mixed
     */
    public function getTaskList($loginUserProjects = [])
    {
        $query = Task::orderBy('title');
        if (!empty($loginUserProjects)) {
            $query = $query->whereIn('project_id', array_keys($loginUserProjects));
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
     * @param Task $task
     * @param array $tags
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
                'name' => $tag,
                'created_by' => getLoggedInUserId()
            ])->id;
        }
        $task->tags()->attach($tagIds);

        return;
    }

    /**
     * @param int $id
     *
     * @return Task
     */
    public function getTaskDetails($id)
    {
        $task = Task::with('timeEntries.user')->findOrFail($id);

        $minutes = $task->timeEntries->pluck('duration')->sum();
        $totalDuration = 0;
        if ($minutes > 1) {
            $totalDuration = sprintf("%02d Hours and %02d Minutes", floor($minutes / 60), $minutes % 60);
        }
        $task->totalDuration = $totalDuration;

        return $task;
    }

    /**
     * @param $input
     *
     * @return array
     */
    public function myTasks($input = [])
    {
        $query = Task::whereHas('taskAssignee', function (Builder $query) {
            $query->where('user_id', getLoggedInUserId());
        })->whereStatus(Task::STATUS_ACTIVE);

        if (!empty($input['project_id'])) {
            $query->where('project_id', $input['project_id']);
        }

        $assignedTasks = $query->get(['title', 'id']);

        return [
            'activities' => ActivityType::get(['name', 'id']),
            'tasks' => $assignedTasks,
        ];
    }

    /**
     * @param $id
     * @param $file
     * @return string
     * @throws Exception
     */
    public function uploadFile($id, $file){
        $destinationPath = public_path(Task::PATH);
        $task = $this->findOrFail($id);
        try {
            $fileName = time().'_'.uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move($destinationPath, $fileName);
            $attachment = new TaskAttachment(['task_id' => $task->id, 'file' => $fileName]);

            DB::beginTransaction();
            $task->attachments()->save($attachment);
            DB::commit();
            return $fileName;
        } catch (Exception $e) {
            DB::rollBack();
            if (file_exists($destinationPath.'/'.$fileName)) {
                unlink($destinationPath.'/'.$fileName);
            }
            throw new UploadException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $id
     * @param $input
     * @return bool
     */
    public function deleteFile($id, $input){
        $file = $input['filename'];
        $attachment = TaskAttachment::where('file','like',"%$file%")->where('task_id','=',$id)->first();
        if(empty($attachment)){
            throw new NotFoundException('File not found.');
        }
        $fileName = $attachment->getOriginal('file');
        $attachment->delete();
        $destinationPath = public_path(Task::PATH);

        if (!empty($fileName)){
            if (file_exists($destinationPath.'/'.$fileName)) {
                unlink($destinationPath.'/'.$fileName);
            }
        }
        return true;
    }

    /**
     * @param $id
     * @return array
     */
    public function getAttachments($id){
        $task = $this->find($id);
        $attachments = $task->attachments;

        $result  = [];

        $destinationPath = public_path(Task::PATH);
        foreach ($attachments as $attachment){
            $file = $attachment->getOriginal('file');
            $obj['name'] = $file;
            $obj['size'] = filesize($destinationPath.'/'.$file);
            $obj['url'] = $attachment->file;
            $result[] = $obj;
        }
        return $result;
    }
}
