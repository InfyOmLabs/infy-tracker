<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Queries\RoleDataTable;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use DataTables;
use Exception;
use Flash;
use Illuminate\Http\Request;
use Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class RoleController.
 */
class RoleController extends AppBaseController
{
    /** @var RoleRepository */
    private $rolesRepository;
    /** @var PermissionRepository */
    private $permissionRepository;

    /**
     * RoleController constructor.
     *
     * @param RoleRepository       $rolesRepo
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(RoleRepository $rolesRepo, PermissionRepository $permissionRepository)
    {
        $this->rolesRepository = $rolesRepo;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a listing of the Roles.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new RoleDataTable())->get())->make(true);
        }

        return view('roles.index');
    }

    /**
     * Show the form for creating a new Roles.
     *
     * @return Response
     */
    public function create()
    {
        /** @var Permission $permissions */
        $permissions = $this->permissionRepository->permissionList();

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created Roles in storage.
     *
     * @param CreateRoleRequest $request
     *
     * @return Response
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->all();
        /** @var Role $roles */
        $roles = $this->rolesRepository->create($input);
        if (isset($input['permissions']) && !empty($input['permissions'])) {
            $roles->perms()->sync($input['permissions']);
        }
        Flash::success('Role saved successfully.');

        return redirect(route('roles.index'));
    }

    /**
     * Show the form for editing the specified Roles.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $roles = $this->rolesRepository->find($id);
        if (empty($roles)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }
        /** @var Permission $permissions */
        $permissions = $this->permissionRepository->permissionList();

        return view('roles.edit')->with(['roles' => $roles, 'permissions' => $permissions]);
    }

    /**
     * @param $id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $permissions = [];
        $this->rolesRepository->find($id);
        $input = $request->all();
        /** @var Role $roles */
        $roles = $this->rolesRepository->update($input, $id);
        if (isset($input['permissions']) && !empty($input['permissions'])) {
            $permissions = $input['permissions'];
        }
        $roles->perms()->sync($permissions);
        Flash::success('Role updated successfully.');

        return redirect(route('roles.index'));
    }

    /**
     * @param $id
     *
     * @throws Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        /** @var Role $roles */
        $roles = $this->rolesRepository->find($id);
        if (empty($roles)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }
        if ($roles->users()->count() > 0) {
            throw new BadRequestHttpException('This user role could not be deleted, because it’s assigned to a user.', null, \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
        $this->rolesRepository->delete($id);

        return $this->sendSuccess('Role deleted successfully.');
    }
}
