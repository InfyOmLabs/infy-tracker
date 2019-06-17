<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateActivityTypeRequest;
use App\Http\Requests\UpdateActivityTypeRequest;
use App\Queries\ActivityTypeDataTable;
use App\Repositories\ActivityTypeRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityTypeController extends AppBaseController
{
    /** @var  ActivityTypeRepository */
    private $activityTypeRepository;

    public function __construct(ActivityTypeRepository $activityTypeRepo)
    {
        $this->activityTypeRepository = $activityTypeRepo;
    }

    /**
     * Display a listing of the ActivityType.
     *
     * @param Request $request
     *
     * @return Factory|View
     *
     * @throws Exception
     *
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ActivityTypeDataTable())->get($request->only(['name'])))->make(true);
        }
        return view('activity_types.index');
    }

    /**
     * Store a newly created ActivityType in storage.
     *
     * @param CreateActivityTypeRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreateActivityTypeRequest $request)
    {
        $input = $request->all();
        $input['created_by'] = getLoggedInUserId();
        $this->activityTypeRepository->create($input);

        return $this->sendSuccess('Activity Type created successfully.');
    }

    /**
     * Show the form for editing the specified ActivityType.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function edit($id)
    {
        $activityType = $this->activityTypeRepository->findOrFail($id);

        return $this->sendResponse($activityType, 'Activity Type retrieved successfully.');
    }

    /**
     * Update the specified ActivityType in storage.
     *
     * @param int $id
     * @param UpdateActivityTypeRequest $request
     *
     * @return JsonResponse
     */
    public function update($id, UpdateActivityTypeRequest $request)
    {
        $this->activityTypeRepository->findOrFail($id);

        $this->activityTypeRepository->update($request->all(), $id);

        return $this->sendSuccess('Activity Type updated successfully.');
    }

    /**
     * Remove the specified ActivityType from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws Exception
     *
     */
    public function destroy($id)
    {
        $this->activityTypeRepository->findOrFail($id);

        $this->activityTypeRepository->delete($id);

        return $this->sendSuccess('Activity Type deleted successfully.');
    }
}
