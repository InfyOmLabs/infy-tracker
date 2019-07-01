<?php

namespace App\Repositories;

use App\Models\Role;


class RoleRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'display_name',
        'description'
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
        return Role::class;
    }


    /**
     * @return mixed
     */
    public function getRolesList()
    {
        return Role::orderBy('name')->pluck('name', 'id');
    }
}
