<?php

namespace App\Repositories;

use App\Models\User;
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

        \Auth::login($user);

        return true;
    }

    public function resendEmailVerification($id)
    {
        /** @var AccountRepository $accountRepository */
        $accountRepository = new AccountRepository();
        $activation_code = uniqid();

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
     * @param $id
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
}
