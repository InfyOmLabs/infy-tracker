<?php

namespace App\Repositories;

use App\User;
use Exception;
use Hash;

/**
 * Class UserRepository
 * @package App\Repositories
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
        'phone'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getUserList()
    {
        return User::pluck('name', 'id');
    }

    /**
     * @param  array  $input
     * @return bool
     * @throws Exception
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
}
