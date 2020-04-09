<?php

namespace App\Repositories;

use App\Models\Department;

/**
 * Class DepartmentRepository.
 *
 * @version April 8, 2020, 10:51 am UTC
 */
class DepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
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
        return Department::class;
    }
}
