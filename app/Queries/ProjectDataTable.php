<?php

namespace App\Queries;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProjectDataTable.
 */
class ProjectDataTable
{
    /**
     * @param array $input
     *
     * @return Project|Builder
     */
    public function get($input)
    {
        /** @var Project $query */
        $query = Project::with(['client'])->select('projects.*');

        $query->when(isset($input['filter_client']) && !empty($input['filter_client']),
            function (Builder $q) use ($input) {
                $q->where('client_id', $input['filter_client']);
            });

        $query->when(isset($input['filter_team']) && !empty($input['filter_team']),
            function (Builder $q) use ($input) {
                $q->where('team', $input['filter_team']);
            });

        return $query;
    }
}
