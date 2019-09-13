<?php

namespace App\Repositories;

use App\Events\StartTimer;
use App\Events\StopWatchStop;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        'entry_type',
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
        $timeEntry = TimeEntry::ofCurrentUser()->latest()->first();
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
        $user = getLoggedInUser();
        /** @var Builder $query */
        $query = Task::ofProject($projectId)
            ->where('status', '=', Task::STATUS_ACTIVE);
        if (!$user->can('manage_projects')) {
            $query = $query->whereHas('taskAssignee', function (Builder $query) {
                $query->where('user_id', getLoggedInUserId());
            });
        }

        if (!empty($taskId)) {
            $query->orWhere('id', $taskId);
        }

        $result = $query->pluck('title', 'id');

        return $result;
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getTimeEntryDetail($id)
    {
        $result = TimeEntry::leftJoin('tasks as t', 't.id', '=', 'time_entries.task_id')
            ->where('time_entries.id', '=', $id)
            ->select('time_entries.*', 't.project_id')
            ->first();

        return $result;
    }

    /**
     * @param array $input
     * @param int   $id
     *
     * @return bool
     */
    public function updateTimeEntry($input, $id)
    {
        /** @var TimeEntry $timeEntry */
        $timeEntry = $this->find($id);
        $timeEntryType = ($timeEntry->entry_type == TimeEntry::STOPWATCH) ? $this->checkTimeUpdated($timeEntry,
            $input) : $timeEntry->entry_type;
        $input['entry_type'] = $timeEntryType;
        if ((isset($input['duration']) && !empty($input['duration'])) && (!isset($input['start_time']) || empty($input['start_time']) || !isset($input['end_time']) || empty($input['end_time']))) {
            if ($timeEntry->duration != $input['duration']) {
                $input['start_time'] = '';
                $input['end_time'] = '';
            }
        }
        $this->update($input, $id);

        return true;
    }

    /**
     * @param $timeEntry
     * @param $input
     *
     * @return int
     */
    public function checkTimeUpdated($timeEntry, $input)
    {
        if ($input['start_time'] != $timeEntry->start_time || $input['end_time'] != $timeEntry->end_time) {
            return TimeEntry::VIA_FORM;
        }

        return TimeEntry::STOPWATCH;
    }

    /**
     * @param $input
     * @param null $id
     */
    public function checkDuplicateEntry($input, $id = null)
    {
        $timeArr = [$input['start_time'], $input['end_time']];
        $query = TimeEntry::whereUserId(getLoggedInUserId())
            ->where(function ($q) use ($timeArr) {
                $q->whereBetween('start_time', $timeArr)
                    ->orWhereBetween('end_time', $timeArr)
                    ->orWhereRaw("('$timeArr[0]' between start_time and end_time or '$timeArr[1]' between start_time and end_time)");
            });

        if (!empty($id) && $id > 0) {
            $query->where('id', '!=', $id);
        }

        $timeEntry = $query->first();
        if (!empty($timeEntry)) {
            throw new BadRequestHttpException('Time entry between this duration already exist.');
        }
    }

    public function broadcastStartTimerEvent($input)
    {
        broadcast(new StartTimer($input))->toOthers();
    }

    public function broadcastStopTimerEvent()
    {
        broadcast(new StopWatchStop())->toOthers();
    }
}
