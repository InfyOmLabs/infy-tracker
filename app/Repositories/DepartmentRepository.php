<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

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

    /**
     * get Departments.
     *
     * @return Collection
     */
    public function getDepartmentList()
    {
        /** @var Builder|Department $query */
        $query = Department::orderBy('name');
        return $query->pluck('name', 'id');
    }
}
