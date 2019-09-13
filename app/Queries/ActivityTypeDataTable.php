<?php

namespace App\Queries;

use App\Models\ActivityType;

/**
 * Class ActivityTypeDataTable.
 */
class ActivityTypeDataTable
{
    /**
     * @param null $input
     *
     * @return ActivityType
     */
    public function get($input = null)
    {
        /** @var ActivityType $query */
        $query = ActivityType::query();

        return $query;
    }
}
