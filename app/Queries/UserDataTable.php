<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 *
 * User: Ajay Makwana
 * Email: ajay.makwana@infyom.com
 * Date: 05/02/2019
 * Time: 06:14 PM
 */

namespace App\Queries;

use App\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SettingDataTable
 * @package App\Queries
 */
class UserDataTable
{
    /**
     * @return User|Builder
     */
    public function get()
    {
        /** @var User $query */
        $query = User::query();

        return $query;
    }
}