<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Queries\UserDataTable;
use App\Repositories\AccountRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Crypt;
use DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;
    /** @var ProjectRepository */
    private $projectRepository;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository    $userRepo
     * @param AccountRepository $accountRepository
     * @param ProjectRepository $projectRepository
     */
    public function __construct(
        UserRepository $userRepo,
        AccountRepository $accountRepository,
        ProjectRepository $projectRepository
    ) {
        $this->userRepository = $userRepo;
        $this->accountRepository = $accountRepository;
        $this->projectRepository = $projectRepository;
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
            return Datatables::of((new UserDataTable())->get())->make(true);
        }
        $projects = $this->projectRepository->getProjectsList();

        return view('users.index')->with('projects', $projects);
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
        /** @var User $user */
        $user = $this->userRepository->findOrFail($id);

        $projectIds = [];
        $input = $request->all();

        $input['is_active'] = (isset($input['is_active']) && !empty($input['is_active'])) ? 1 : 0;

        $user = $this->userRepository->update($input, $id);
        if (isset($input['project_ids']) && !empty($input['project_ids'])) {
            $projectIds = $input['project_ids'];
        }
        $user->projects()->sync($projectIds);
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
        $input = $request->all();
        if (isset($input['password']) && !empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        $this->userRepository->update($input, Auth::user()->id);

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
