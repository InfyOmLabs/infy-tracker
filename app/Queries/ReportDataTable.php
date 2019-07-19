<?php

namespace App\Queries;

use App\Models\Report;

/**
 * Class ReportDataTable.
 */
class ReportDataTable
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function get()
    {
        return Report::with('user')->select('reports.*');
    }
}
