<?php

namespace App\Queries;

use App\Models\Client;
use Illuminate\Database\Query\Builder;

/**
 * Class ClientDataTable.
 */
class ClientDataTable
{
    /**
     * @param array $input
     *
     * @return Client|Builder
     */
    public function get($input = [])
    {
        /** @var Client $query */
        $query = Client::with('department')->select('clients.*');

        $query->when(
            isset($input['filter_department']) && ! empty($input['filter_department']),
            function (\Illuminate\Database\Eloquent\Builder $q) use ($input) {
                $q->where('department_id', '=', $input['filter_department']);
            }
        );

        return $query;
    }
}
