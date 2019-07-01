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
use Flash;
use Illuminate\Http\Request;
use Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RoleController extends AppBaseController
{
    /** @var  RoleRepository */
    private $rolesRepository;
    /** @var PermissionRepository */
    private $permissionRepository;

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
     * @return Response
     * @throws \Exception
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

        Flash::success('Roles saved successfully.');

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
            Flash::error('Roles not found');

            return redirect(route('roles.index'));
        }
        /** @var Permission $permissions */
        $permissions = $this->permissionRepository->permissionList();
        return view('roles.edit')->with(['roles' => $roles, 'permissions' => $permissions]);
    }


    /**
     * @param $id
     * @param Request $request
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
        Flash::success('Roles updated successfully.');

        return redirect(route('roles.index'));
    }

    /**
     * Remove the specified Roles from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     *
     */
    public function destroy($id)
    {
        /** @var Role $roles */
        $roles = $this->rolesRepository->find($id);

        if (empty($roles)) {
            Flash::error('Roles not found');

            return redirect(route('roles.index'));
        }
        if ($roles->users()->count() > 0) {
            throw new BadRequestHttpException('This user role could not be deleted, because it’s assigned to a user.', null, \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        $this->rolesRepository->delete($id);

        Flash::success('Roles deleted successfully.');

        return response()->json(['success' => true], 200);
    }
}