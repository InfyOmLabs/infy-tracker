<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTimeEntryRequest;
use App\Http\Requests\UpdateTimeEntryRequest;
use App\Models\TimeEntry;
use App\Queries\TimeEntryDataTable;
use App\Repositories\TimeEntryRepository;
use Auth;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\View\View;

/**
 * Class TimeEntryController.
 */
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
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new TimeEntryDataTable())->get(
                $request->only('filter_activity', 'filter_user', 'filter_project'))
            )->editColumn('title', function (TimeEntry $timeEntry) {
                return $timeEntry->task->prefix_task_number.' '.$timeEntry->task->title;
            })->filterColumn('title', function (Builder $query, $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhereRaw("concat(ifnull(p.prefix,''),'-',ifnull(t.task_number,'')) LIKE ?",
                            ["%$search%"]);
                });
            })->make(true);
        }

        $entryData = $this->timeEntryRepository->getEntryData();

        return view('time_entries.index')->with($entryData);
    }

    /**
     * Store a newly created TimeEntry in storage.
     *
     * @param CreateTimeEntryRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateTimeEntryRequest $request)
    {
        $this->timeEntryRepository->store($request->all());
        $this->timeEntryRepository->broadcastStopTimerEvent();

        return $this->sendSuccess('Time Entry created successfully.');
    }

    /**
     * Show the form for editing the specified TimeEntry.
     *
     * @param TimeEntry $timeEntry
     *
     * @return JsonResponse
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
     * @return JsonResponse
     */
    public function update(TimeEntry $timeEntry, UpdateTimeEntryRequest $request)
    {
        $user = getLoggedInUser();
        if (!$user->can('manage_projects')) {
            $timeEntry = TimeEntry::ofCurrentUser()->find($timeEntry->id);
        }
        if (empty($timeEntry)) {
            return $this->sendError('Time Entry not found.', Response::HTTP_NOT_FOUND);
        }

        $input = $request->all();
        $this->timeEntryRepository->updateTimeEntry($input, $timeEntry->id);

        return $this->sendSuccess('Time Entry updated successfully.');
    }

    /**
     * @param TimeEntry $timeEntry
     *
     * @throws Exception
     *
     * @return JsonResponse
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
     * @return JsonResponse
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
     * @return JsonResponse
     */
    public function getTasks($projectId, Request $request)
    {
        $taskId = (!is_null($request->get('task_id', null))) ? $request->get('task_id') : null;
        $result = $this->timeEntryRepository->getTasksByProject($projectId, $taskId);

        return $this->sendResponse($result, 'Project Tasks retrieved successfully.');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getStartTimer(Request $request)
    {
        $this->timeEntryRepository->broadcastStartTimerEvent($request->all());

        return $this->sendSuccess('Start timer broadcasts successfully.');
    }
}
