<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Queries\UserDataTable;
use App\Repositories\AccountRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Traits\ImageTrait;
use Auth;
use Crypt;
use DataTables;
use Exception;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends AppBaseController
{
    use ImageTrait;
    /** @var UserRepository */
    private $userRepository;
    /** @var ProjectRepository */
    private $projectRepository;
    /**
     * @var AccountRepository
     */
    private $accountRepository;
    /** @var RoleRepository */
    private $roleRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository    $userRepo
     * @param AccountRepository $accountRepository
     * @param ProjectRepository $projectRepository
     * @param RoleRepository    $roleRepository
     */
    public function __construct(
        UserRepository $userRepo,
        AccountRepository $accountRepository,
        ProjectRepository $projectRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepo;
        $this->accountRepository = $accountRepository;
        $this->projectRepository = $projectRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the User.
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
            return Datatables::of((new UserDataTable())->get())->addColumn('role_name', function (User $user) {
                return implode(',', $user->roles()->pluck('name')->toArray());
            })->make(true);
        }
        $projects = $this->projectRepository->getProjectsList();
        $roles = $this->roleRepository->getRolesList();

        return view('users.index')->with(['projects' => $projects, 'roles' => $roles]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $input['created_by'] = getLoggedInUserId();
        $input['activation_code'] = uniqid();
        $input['is_active'] = (isset($input['is_active']) && !empty($input['is_active'])) ? 1 : 0;
        /** @var User $user */
        $user = $this->userRepository->create($input);
        if (isset($input['project_ids']) && !empty($input['project_ids'])) {
            $user->projects()->sync($input['project_ids']);
        }
        if (isset($input['role_id']) && !empty($input['role_id'])) {
            $user->roles()->sync($input['role_id']);
        }
        if ($input['is_active']) {
            /*
         * Send confirmation email
         */
            $key = $user->id.'|'.$user->activation_code;
            $code = Crypt::encrypt($key);
            $this->accountRepository->sendConfirmEmail(
                $user->name,
                $user->email,
                $code
            );
        }

        return $this->sendSuccess('User created successfully.');
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function edit($id)
    {
        /** @var User $user */
        $user = $this->userRepository->findOrFail($id);
        $userObj = $user->toArray();
        $userObj['project_ids'] = $user->projects()->pluck('project_id')->toArray();
        $userObj['role_id'] = $user->roles()->pluck('role_id')->toArray();

        return $this->sendResponse($userObj, 'User retrieved successfully.');
    }

    /**
     * Update the specified User in storage.
     *
     * @param int               $id
     * @param UpdateUserRequest $request
     *
     * @throws \Exception
     *
     * @return JsonResponse|RedirectResponse
     */
    public function update($id, UpdateUserRequest $request)
    {
        $this->userRepository->findOrFail($id);

        $projectIds = [];
        $input = $request->all();

        $input['is_active'] = (isset($input['is_active']) && !empty($input['is_active'])) ? 1 : 0;
        /** @var User $user */
        $user = $this->userRepository->update($input, $id);
        if (isset($input['project_ids']) && !empty($input['project_ids'])) {
            $projectIds = $input['project_ids'];
        }
        $user->projects()->sync($projectIds);
        if (isset($input['role_id']) && !empty($input['role_id'])) {
            $user->roles()->sync($input['role_id']);
        }
        if ($input['is_active'] && !$user->is_email_verified) {
            $key = $user->id.'|'.$user->activation_code;
            $code = Crypt::encrypt($key);
            $this->accountRepository->sendConfirmEmail(
                $user->name,
                $user->email,
                $code
            );
        }

        return $this->sendSuccess('User updated successfully.');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->userRepository->findOrFail($id);

        $this->userRepository->delete($id);

        return $this->sendSuccess('User deleted successfully.');
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function resendEmailVerification($id)
    {
        $this->userRepository->resendEmailVerification($id);

        return $this->sendSuccess('Verification email has been sent successfully.');
    }

    /**
     * @param UpdateUserProfileRequest $request
     *
     * @return JsonResponse
     */
    public function profileUpdate(UpdateUserProfileRequest $request)
    {
        /** @var User $user */
        $user = $this->userRepository->findOrFail(Auth::id());
        $input = $request->all();
        if (isset($input['password']) && !empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        try {
            if (isset($input['photo']) && !empty($input['photo'])) {
                $input['image_path'] = ImageTrait::makeImage($input['photo'], User::IMAGE_PATH, ['width' => 150, 'height' => 150]);
                $imagePath = $user->image_path;
            }
            if (!empty($imagePath)) {
                $user->deleteImage();
            }
            $this->userRepository->update($input, Auth::id());
        } catch (Exception $e) {
            if (isset($input['image_url']) && !empty($input['image_url'])) {
                $this->deleteImage(User::IMAGE_PATH.DIRECTORY_SEPARATOR.$input['image_url']);
            }
        }

        return $this->sendSuccess('Profile updated successfully.');
    }

    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function activeDeActiveUser($id)
    {
        $this->userRepository->activeDeActiveUser($id);

        return $this->sendSuccess('User updated successfully.');
    }
}
