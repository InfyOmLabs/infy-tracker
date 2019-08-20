<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTimeEntryRequest;
use App\Http\Requests\UpdateTimeEntryRequest;
use App\Models\TimeEntry;
use App\Queries\TimeEntryDataTable;
use App\Repositories\TimeEntryRepository;
use Auth;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TimeEntryController extends AppBaseController
{
    /** @var TimeEntryRepository */
    private $timeEntryRepository;

    public function __construct(TimeEntryRepository $timeEntryRepo)
    {
        $this->timeEntryRepository = $timeEntryRepo;
    }

    /**
     * Display a listing of the TimeEntry.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new TimeEntryDataTable())->get(
                $request->only('filter_activity', 'filter_user', 'filter_project'))
            )->make(true);
        }

        $entryData = $this->timeEntryRepository->getEntryData();

        return view('time_entries.index')->with($entryData);
    }

    /**
     * Store a newly created TimeEntry in storage.
     *
     * @param CreateTimeEntryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateTimeEntryRequest $request)
    {
        $input = $this->validateInput($request->all());

        $this->timeEntryRepository->create($input);
        $this->timeEntryRepository->broadcastStopTimerEvent();

        return $this->sendSuccess('Time Entry created successfully.');
    }

    /**
     * Show the form for editing the specified TimeEntry.
     *
     * @param TimeEntry $timeEntry
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(TimeEntry $timeEntry)
    {
        $timeEntryDetails = $this->timeEntryRepository->getTimeEntryDetail($timeEntry->id);

        return $this->sendResponse($timeEntryDetails, 'Time Entry retrieved successfully.');
    }

    /**
     * Update the specified TimeEntry in storage.
     *
     * @param TimeEntry              $timeEntry
     * @param UpdateTimeEntryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TimeEntry $timeEntry, UpdateTimeEntryRequest $request)
    {
        $entry = TimeEntry::ofCurrentUser()->find($timeEntry->id);
        if (empty($entry)) {
            return $this->sendError('Time Entry not found.', Response::HTTP_NOT_FOUND);
        }
        $input = $this->validateInput($request->all(), $timeEntry->id);
        $existEntry = $entry->only([
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
            Log::info('Entry Id: '.$entry->id);
            Log::info('Task Id: '.$entry->task_id);
            Log::info('fields changed: ', $inputDiff);
            Log::info('Entry updated by: '.Auth::user()->name);
        }
        $this->timeEntryRepository->updateTimeEntry($input, $timeEntry->id);

        return $this->sendSuccess('Time Entry updated successfully.');
    }

    /**
     * @param TimeEntry $timeEntry
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $user = Auth::user();
        if (!$user->can('manage_time_entries') && $timeEntry->user_id != getLoggedInUserId()) {
            throw new UnauthorizedException('You are not allow to delete this entry.', 402);
        }
        $timeEntry->update(['deleted_by' => getLoggedInUserId()]);
        $timeEntry->delete();

        return $this->sendSuccess('TimeEntry deleted successfully.');
    }

    /**
     * @param array $input
     *
     * @return array|\Illuminate\Http\JsonResponse
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

        $this->timeEntryRepository->checkDuplicateEntry($input, $id);

        $input['user_id'] = getLoggedInUserId();
        if (!isset($input['note']) || empty($input['note'])) {
            $input['note'] = 'N/A';
        }

        return $input;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserLastTask()
    {
        $result = $this->timeEntryRepository->myLastTask();

        return $this->sendResponse($result, 'User Task retrieved successfully.');
    }

    /**
     * @param int     $projectId
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTasks($projectId, Request $request)
    {
        $taskId = (!is_null($request->get('task_id', null))) ? $request->get('task_id') : null;
        $result = $this->timeEntryRepository->getTasksByProject($projectId, $taskId);

        return $this->sendResponse($result, 'Project Tasks retrieved successfully.');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStartTimer() {
       $this->timeEntryRepository->broadcastStartTimerEvent();
        return $this->sendSuccess('Start timer broadcasted successfully.');
    }
}
