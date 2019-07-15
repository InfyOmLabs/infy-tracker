<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Queries\TimeEntryDataTable;
use App\Repositories\TimeEntryRepository;
use Auth;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
                $request->only('filter_activity', 'filter_user'))
            )->make(true);
        }

        $entryData = $this->timeEntryRepository->getEntryData();

        return view('time_entries.index')->with($entryData);
    }

    /**
     * Store a newly created TimeEntry in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $this->validateInput($request->all());

        $this->timeEntryRepository->create($input);

        return $this->sendSuccess('Time Entry created successfully.');
    }

    /**
     * Show the form for editing the specified TimeEntry.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $this->timeEntryRepository->findOrFail($id);
        $timeEntryDetails = $this->timeEntryRepository->getTimeEntryDetail($id);

        return $this->sendResponse($timeEntryDetails, 'Time Entry retrieved successfully.');
    }

    /**
     * Update the specified TimeEntry in storage.
     *
     * @param int     $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $entry = TimeEntry::ofUser(getLoggedInUserId())->find($id);
        if (empty($entry)) {
            return $this->sendError('Time Entry not found.', Response::HTTP_NOT_FOUND);
        }
        $input = $this->validateInput($request->all());
        $existEntry = $entry->only(['id', 'task_id', 'activity_type_id', 'user_id', 'start_time', 'end_time', 'duration', 'note']);
        $inputDiff = array_diff($existEntry, $input);
        if (!empty($inputDiff)) {
            Log::info('Entry Id: '.$entry->id);
            Log::info('Task Id: '.$entry->task_id);
            Log::info('fields changed: ', $inputDiff);
            Log::info('Entry updated by: '.Auth::user()->name);
        }
        $this->timeEntryRepository->updateTimeEntry($input, $id);

        return $this->sendSuccess('Time Entry updated successfully.');
    }

    /**
     * @param $id
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $timeEntry = $this->timeEntryRepository->findOrFail($id);

        $timeEntry->update(['deleted_by' => getLoggedInUserId()]);
        $this->timeEntryRepository->delete($id);

        return response()->json(['success' => true], 200);
    }

    /**
     * @param array $input
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function validateInput($input)
    {
        if (empty($input['duration']) && (empty($input['start_time']) || empty($input['end_time']))) {
            throw new BadRequestHttpException('duration  OR start time & end time required');
        }

        if (!empty($input['start_time']) && !empty($input['end_time'])) {
            if (Carbon::parse($input['start_time']) > Carbon::parse($input['end_time'])) {
                throw new BadRequestHttpException('Invalid start time and end time.');
            }
            $input['duration'] = Carbon::parse($input['end_time'])->diffInMinutes($input['start_time']);
        }

        if ($input['duration'] > 720) {
            throw new BadRequestHttpException('Time Entry must be less than 12 hours.');
        }

        if ($input['duration'] < 1) {
            throw new BadRequestHttpException('Minimum Entry time should be 1 minute.');
        }

        $input['user_id'] = getLoggedInUserId();
        $message = $this->validateRules($input, TimeEntry::$rules);
        if (!empty($message)) {
            throw new BadRequestHttpException($message);
        }

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
}
