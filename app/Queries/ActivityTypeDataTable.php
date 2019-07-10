<?php

namespace App\Queries;

use App\Models\ActivityType;

/**
 * Class ActivityTypeDataTable.
 */
class ActivityTypeDataTable
{
    public function get($input = null)
    {
        /** @var ActivityType $query */
        $query = ActivityType::query();

        return $query;
    }
}
