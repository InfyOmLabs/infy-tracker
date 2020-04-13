<?php

namespace App\Queries;

use App\Models\Department;
use Illuminate\Database\Query\Builder;

/**
 * Class DepartmentDataTable.
 */
class DepartmentDataTable
{
    /**
     * @return Department|Builder
     */
    public function get()
    {
        /** @var Department $query */
        $query = Department::query();

        return $query;
    }
}
