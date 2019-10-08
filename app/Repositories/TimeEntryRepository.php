<?php

namespace App\Repositories;

use App\Events\StartTimer;
use App\Events\StopWatchStop;
use App\Models\Task;
use App\Models\TimeEntry;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Log;
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

        $data['tasks'] = Task::whereHas('taskAssignee', function (Builder $query) {
            $query->where('user_id', getLoggedInUserId());
        })->orderBy('title')->pluck('title', 'id');

        return $data;
    }

    /**
     * @return array|null|void
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
     * @param  int  $projectId
     * @param  int|null  $taskId
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
     * @param  int  $id
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
     * @param  array  $input
     *
     * @return TimeEntry
     */
    public function store($input)
    {
        $input = $this->validateInput($input);

        $this->assignTaskToAdmin($input);

        $timeEntry = TimeEntry::create($input);

        return $timeEntry;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     *
     * @return bool
     */
    public function updateTimeEntry($input, $id)
    {
        $input = $this->validateInput($input, $id);

        /** @var TimeEntry $timeEntry */
        $timeEntry = TimeEntry::findOrFail($id);

        $existEntry = $timeEntry->only([
            'id',
            'task_id',
            'activity_type_id',
            'user_id',
            'start_time',
            'end_time',
            'duration',
            'note',
        ]);

        $inputDiff = array_diff($existEntry, $input);
        if (!empty($inputDiff)) {
            Log::info('Entry Id: '.$timeEntry->id);
            Log::info('Task Id: '.$timeEntry->task_id);
            Log::info('fields changed: ', $inputDiff);
            Log::info('Entry updated by: '.Auth::user()->name);
        }


        $timeEntryType = ($timeEntry->entry_type == TimeEntry::STOPWATCH) ?
            $this->checkTimeUpdated($timeEntry, $input) :
            $timeEntry->entry_type;
        $input['entry_type'] = $timeEntryType;
        if (!empty($input['duration']) && empty($input['start_time']) || empty($input['end_time'])) {
            if ($timeEntry->duration != $input['duration']) {
                $input['start_time'] = '';
                $input['end_time'] = '';
            }
        }
        $this->update($input, $id);

        return true;
    }

    /**
     * @param  array  $input
     * @param  null  $id
     *
     * @return array|JsonResponse
     */
    public function validateInput($input, $id = null)
    {
        $startTime = Carbon::parse($input['start_time']);
        $endTime = Carbon::parse($input['end_time']);
        $input['duration'] = $endTime->diffInMinutes($startTime);
        if ($startTime > $endTime) {
            throw new BadRequestHttpException('Invalid start time and end time.');
        }

        $now = Carbon::now()->format('Y-m-d');
        if ($startTime->format('Y-m-d') > $now) {
            throw new BadRequestHttpException('Start time must be less than or equal to current time.');
        }

        if ($endTime->format('Y-m-d') > $now) {
            throw new BadRequestHttpException('End time must be less than or equal to current time.');
        }

        if ($input['duration'] > 720) {
            throw new BadRequestHttpException('Time Entry must be less than 12 hours.');
        }

        if ($input['duration'] < 1) {
            throw new BadRequestHttpException('Minimum Entry time should be 1 minute.');
        }

        $this->checkDuplicateEntry($input, $id);

        $input['user_id'] = getLoggedInUserId();
        if (!isset($input['note']) || empty($input['note'])) {
            $input['note'] = 'N/A';
        }

        return $input;
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
     * @param  array  $input
     * @param  int|null  $id
     *
     * @return bool
     */
    public function checkDuplicateEntry($input, $id = null)
    {
        $timeArr = [$input['start_time'], $input['end_time']];
        $query = TimeEntry::whereUserId(getLoggedInUserId())
            ->where(function (Builder $q) use ($timeArr) {
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

        return true;
    }


    /**
     * Start timer broadcast event
     *
     * @param  array  $input
     */
    public function broadcastStartTimerEvent($input)
    {
        broadcast(new StartTimer($input))->toOthers();
    }

    /**
     * Stop timer broadcast event
     */
    public function broadcastStopTimerEvent()
    {
        broadcast(new StopWatchStop())->toOthers();
    }

    /**
     * @param  array  $input
     *
     * @return bool
     */
    public function assignTaskToAdmin($input)
    {
        $task = Task::find($input['task_id']);
        $taskAssignees = $task->taskAssignee->pluck('id')->toArray();

        if (!in_array(getLoggedInUserId(), $taskAssignees)) {
            array_push($taskAssignees, getLoggedInUserId());
            $task->taskAssignee()->sync($taskAssignees);
        }

        return true;
    }
}
