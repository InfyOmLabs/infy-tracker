<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateActivityTypeRequest;
use App\Http\Requests\UpdateActivityTypeRequest;
use App\Models\ActivityType;
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
    /** @var ActivityTypeRepository */
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
     * @throws Exception
     *
     * @return Factory|View
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
     * @param ActivityType $activityType
     *
     * @return JsonResponse
     */
    public function edit(ActivityType $activityType)
    {
        return $this->sendResponse($activityType, 'Activity Type retrieved successfully.');
    }

    /**
     * Update the specified ActivityType in storage.
     *
     * @param ActivityType $activityType
     * @param UpdateActivityTypeRequest $request
     *
     * @return JsonResponse
     */
    public function update(ActivityType $activityType, UpdateActivityTypeRequest $request)
    {
        $this->activityTypeRepository->update($request->all(), $activityType->id);

        return $this->sendSuccess('Activity Type updated successfully.');
    }

    /**
     * Remove the specified ActivityType from storage.
     *
     * @param ActivityType $activityType
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(ActivityType $activityType)
    {
        $activityType->delete();

        return $this->sendSuccess('Activity Type deleted successfully.');
    }
}
