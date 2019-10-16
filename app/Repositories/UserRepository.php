<?php

namespace App\Repositories;

use App\Models\User;
use App\Traits\ImageTrait;
use Auth;
use Crypt;
use Exception;
use Hash;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class UserRepository.
 *
 * @version May 2, 2019, 12:42 pm UTC
 */
class UserRepository extends BaseRepository
{
    private $accountRepo;

    public function __construct(Application $app, AccountRepository $accountRepo)
    {
        parent::__construct($app);
        $this->accountRepo = $accountRepo;
    }

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'phone',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return User::class;
    }

    /**
     * @param array $projectIds
     *
     * @return Collection
     */
    public function getUserList($projectIds = [])
    {
        /** @var Builder $query */
        $query = User::orderBy('name');
        if (!empty($projectIds)) {
            $query = $query->whereHas('projects', function (Builder $query) use ($projectIds) {
                $query->whereIn('projects.id', $projectIds);
            });
        }

        return $query->pluck('name', 'id');
    }

    /**
     * @param array $input
     *
     * @throws Exception
     *
     * @return bool
     */
    public function setUserPassword($input)
    {
        $password = Hash::make($input['password']);
        /** @var User $user */
        $user = User::findOrFail($input['user_id']);

        $user->password = $password;
        $user->set_password = true;
        $user->save();

        Auth::login($user);

        return true;
    }

    /**
     * @param int $id
     *
     * @throws Exception
     *
     * @return bool
     */
    public function resendEmailVerification($id)
    {
        /** @var AccountRepository $accountRepository */
        $accountRepository = new AccountRepository();
        $activation_code = uniqid();

        /** @var User $user */
        $user = $this->find($id);
        $user->activation_code = $activation_code;
        $user->save();

        $key = $user->id.'|'.$activation_code;
        $code = Crypt::encrypt($key);
        $accountRepository->sendConfirmEmail(
            $user->name,
            $user->email,
            $code
        );

        return true;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function activeDeActiveUser($id)
    {
        /** @var User $user */
        $user = $this->findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return $user;
    }

    /**
     * @param array $input
     *
     * @return true
     */
    public function profileUpdate($input)
    {
        /** @var User $user */
        $user = $this->findOrFail(Auth::id());

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        try {
            if (isset($input['photo']) && !empty($input['photo'])) {
                $input['image_path'] = ImageTrait::makeImage(
                    $input['photo'], User::IMAGE_PATH, ['width' => 150, 'height' => 150]
                );

                $imagePath = $user->image_path;
                if (!empty($imagePath)) {
                    $user->deleteImage();
                }
            }

            $user->update($input);
        } catch (Exception $e) {
            if (!empty($input['image_path'])) {
                unlink(User::IMAGE_PATH.DIRECTORY_SEPARATOR.$input['image_url']);
            }
        }

        return true;
    }

    /**
     * @param array $input
     *
     * @throws Exception
     *
     * @return User|null
     */
    public function store($input)
    {
        $input = $this->validateInput($input);
        $input['created_by'] = getLoggedInUserId();
        $input['activation_code'] = uniqid();

        /** @var User $user */
        $user = User::create($input);
        $this->assignRolesAndProjects($user, $input);

        if ($input['is_active']) {
            $key = $user->id.'|'.$user->activation_code;
            $code = Crypt::encrypt($key);
            /* Send confirmation email */
            $this->accountRepo->sendConfirmEmail(
                $user->name,
                $user->email,
                $code
            );
        }

        return $user->fresh();
    }

    /**
     * @param array $input
     * @param int   $id
     *
     * @throws Exception
     *
     * @return User
     */
    public function update($input, $id)
    {
        $input = $this->validateInput($input);

        /** @var User $user */
        $user = User::findOrFail($id);
        $user = $user->update($input);
        $this->assignRolesAndProjects($user, $input);

        if ($input['is_active'] && !$user->is_email_verified) {
            $key = $user->id.'|'.$user->activation_code;
            $code = Crypt::encrypt($key);
            $this->accountRepo->sendConfirmEmail(
                $user->name,
                $user->email,
                $code
            );
        }

        return $user->fresh();
    }

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function validateInput($input)
    {
        if (!empty($input['password']) && Auth::user()->can('manage_users')) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['is_active'] = (!empty($input['is_active'])) ? 1 : 0;

        return $input;
    }

    /**
     * @param User  $user
     * @param array $input
     *
     * @return bool
     */
    public function assignRolesAndProjects($user, $input)
    {
        $projectIds = !empty($input['project_ids']) ? $input['project_ids'] : [];
        $user->projects()->sync($projectIds);

        $roles = !empty($input['role_id']) ? $input['role_id'] : [];
        $user->roles()->sync($roles);

        return true;
    }
}
