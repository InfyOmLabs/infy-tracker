<?php

namespace App\Queries;

use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ReportDataTable.
 */
class ReportDataTable
{
    /**
     * @param array $input
     *
     * @return Report|Builder|\Illuminate\Database\Query\Builder
     */
    public function get($input = [])
    {
        $query = Report::with('user')->select('reports.*');

        $query->when(!empty($input['filter_created_by']), function (Builder $query) use ($input) {
            $query->where('owner_id', $input['filter_created_by']);
        });

        return $query;
    }
}
