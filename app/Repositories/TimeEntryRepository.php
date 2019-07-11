<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class TimeEntryRepository.
 *
 * @version May 3, 2019, 9:46 am UTC
 */
class TimeEntryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'task_id',
        'activity_type_id',
        'user_id',
        'start_time',
        'end_time',
        'duration',
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
        return TimeEntry::class;
    }

    /**
     * @return array
     */
    public function getEntryData()
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $data['projects'] = $projectRepo->getLoginUserAssignProjectsArr();

        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $data['users'] = $userRepo->getUserList();

        /** @var ActivityTypeRepository $activityTypeRepo */
        $activityTypeRepo = app(ActivityTypeRepository::class);
        $data['activityTypes'] = $activityTypeRepo->getActivityTypeList();

        $data['tasks'] = Task::orderBy('title')->whereHas('taskAssignee', function (Builder $query) {
            $query->where('user_id', getLoggedInUserId());
        })->pluck('title', 'id');

        return $data;
    }

    /**
     * @return array|null
     */
    public function myLastTask()
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = TimeEntry::whereUserId(getLoggedInUserId())->latest()->first();
        if (empty($timeEntry)) {
            return;
        }

        return [
            'task_id'     => $timeEntry->task_id,
            'activity_id' => $timeEntry->activity_type_id,
            'project_id'  => $timeEntry->task->project_id,
        ];
    }

    /**
     * @param int      $projectId
     * @param int|null $taskId
     *
     * @return Collection
     */
    public function getTasksByProject($projectId, $taskId = null)
    {
        /** @var Builder $query */
        $query = Task::whereProjectId($projectId)
            ->where('status', '=', Task::STATUS_ACTIVE)
            ->whereHas('taskAssignee', function (Builder $query) {
                $query->where('user_id', getLoggedInUserId());
            });

        if (!empty($taskId)) {
            $query->orWhere('id', $taskId);
        }

        $result = $query->pluck('title', 'id');

        return $result;
    }

    public function getTimeEntryDetail($id)
    {
        $result = TimeEntry::leftJoin('tasks as t', 't.id', '=', 'time_entries.task_id')
            ->where('time_entries.id', '=', $id)
            ->select('time_entries.*', 't.project_id')
            ->first();

        return $result;
    }

    public function updateTimeEntry($input, $id)
    {
        $timeEntry = $this->find($id);
        if ((isset($input['duration']) && !empty($input['duration'])) && (!isset($input['start_time']) || empty($input['start_time']) || !isset($input['end_time']) || empty($input['end_time']))) {
            if ($timeEntry->duration != $input['duration']) {
                $input['start_time'] = '';
                $input['end_time'] = '';
            }
        }
        $this->update($input, $id);

        return true;
    }
}
