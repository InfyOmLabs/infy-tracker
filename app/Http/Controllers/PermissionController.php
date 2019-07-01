<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Queries\PermissionDataTable;
use App\Repositories\PermissionRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends AppBaseController
{
    /** @var PermissionRepository */
    private $permissionRepository;


    /**
     * PermissionController constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param Request $request
     *
     * @return Factory|View
     * @throws Exception
     *
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new PermissionDataTable())->get())->make(true);
        }

        return view('permissions.index');
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param CreatePermissionRequest $request
     *
     * @return JsonResponse
     */
    public function store(CreatePermissionRequest $request)
    {
        $input = $request->all();
        $this->permissionRepository->create($input);

        return $this->sendSuccess('Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function edit($id)
    {
        $permission = $this->permissionRepository->findOrFail($id);

        return $this->sendResponse($permission, 'Permission retrieved successfully.');
    }

    /**
     * Update the specified permission in storage.
     *
     * @param int $id
     * @param UpdatePermissionRequest $request
     * @return JsonResponse
     */
    public function update($id, UpdatePermissionRequest $request)
    {
        $this->permissionRepository->findOrFail($id);

        $this->permissionRepository->update($request->all(), $id);

        return $this->sendSuccess('Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy($id)
    {
        $this->permissionRepository->findOrFail($id);

        $this->permissionRepository->delete($id);

        return $this->sendSuccess('Permission deleted successfully.');
    }
}
