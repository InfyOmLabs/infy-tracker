<?php

namespace App\Repositories;

use App\Models\User;
use App\Traits\ImageTrait;
use Auth;
use Crypt;
use Exception;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class UserRepository.
 *
 * @version May 2, 2019, 12:42 pm UTC
 */
class UserRepository extends BaseRepository
{
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
                $input['image_path'] = ImageTrait::makeImage($input['photo'], User::IMAGE_PATH,
                    ['width' => 150, 'height' => 150]);
                $imagePath = $user->image_path;
            }

            if (!empty($imagePath)) {
                $user->deleteImage();
            }

            $user->update($input);
        } catch (Exception $e) {
            if (!empty($input['image_path'])) {
                unlink(User::IMAGE_PATH.DIRECTORY_SEPARATOR.$input['image_url']);
            }
        }
    }
}
