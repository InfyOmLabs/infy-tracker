<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Queries\UserDataTable;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class UserController
 */
class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param  UserRepository  $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param  Request  $request
     * @param  RoleRepository  $roleRepository
     * @param  ProjectRepository  $projectRepository
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request, RoleRepository $roleRepository, ProjectRepository $projectRepository)
    {
        if ($request->ajax()) {
            return Datatables::of((new UserDataTable())->get())->addColumn('role_name', function (User $user) {
                return implode(',', $user->roles()->pluck('name')->toArray());
            })->make(true);
        }

        $projects = $projectRepository->getProjectsList();
        $roles = $roleRepository->getRolesList();

        return view('users.index')->with(['projects' => $projects, 'roles' => $roles]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  CreateUserRequest  $request
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();

        $this->userRepository->store($input);

        return $this->sendSuccess('User created successfully.');
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  User  $user
     *
     * @return JsonResponse
     */
    public function edit(User $user)
    {
        $userObj = $user->toArray();
        $userObj['project_ids'] = $user->projects()->pluck('project_id')->toArray();
        $userObj['role_id'] = $user->roles()->pluck('role_id')->toArray();

        return $this->sendResponse($userObj, 'User retrieved successfully.');
    }

    /**
     * Update the specified User in storage.
     *
     * @param  User  $user
     * @param  UpdateUserRequest  $request
     *
     * @throws Exception
     *
     * @return JsonResponse|RedirectResponse
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        $input = $request->all();

        $this->userRepository->update($input, $user->id);

        return $this->sendSuccess('User updated successfully.');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  User  $user
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        $user->deleted_by = getLoggedInUserId();
        $user->save();
        $user->delete();

        return $this->sendSuccess('User deleted successfully.');
    }

    /**
     * @param  User  $user
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function resendEmailVerification(User $user)
    {
        $this->userRepository->resendEmailVerification($user->id);

        return $this->sendSuccess('Verification email has been sent successfully.');
    }

    /**
     * @param  UpdateUserProfileRequest  $request
     *
     * @return JsonResponse
     */
    public function profileUpdate(UpdateUserProfileRequest $request)
    {
        $input = $request->all();

        $this->userRepository->profileUpdate($input);

        return $this->sendSuccess('Profile updated successfully.');
    }

    /**
     * @param  User  $user
     *
     * @return JsonResponse
     */
    public function activeDeActiveUser(User $user)
    {
        $this->userRepository->activeDeActiveUser($user->id);

        return $this->sendSuccess('User updated successfully.');
    }
}
