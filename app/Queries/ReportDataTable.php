<?php

namespace App\Queries;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TaskDataTable
 * @package App\Queries
 */
class ReportDataTable
{
    /**
     * @param array $input
     *
     * @return Builder
     */
    public function get($input = [])
    {
        if (empty($input['filter_start_date']) && empty($input['filter_end_date'])) {
            $input['filter_start_date'] = Carbon::now()->startOfDay();
            $input['filter_end_date'] = Carbon::now()->endOfDay();
        }

        $query = TimeEntry::with(['task', 'activityType', 'user', 'task.project'])
            ->whereBetween('time_entries.created_at', [$input['filter_start_date'], $input['filter_end_date']])
            ->select(['time_entries.*']);

        $query->when(isset($input['filter_task']) && !empty($input['filter_task']),
            function (Builder $q) use ($input) {
                $q->where('task_id', $input['filter_task']);
            });

        $query->when(isset($input['filter_activity']) && !empty($input['filter_activity']),
            function (Builder $q) use ($input) {
                $q->where('activity_type_id', $input['filter_activity']);
            });

        $query->when(isset($input['filter_user']) && !empty($input['filter_user']),
            function (Builder $q) use ($input) {
                $q->where('user_id', $input['filter_user']);
            });

        $query->when(isset($input['filter_project']) && !empty($input['filter_project']),
            function (Builder $q) use ($input) {
                $q->whereHas('task', function (Builder $query) use ($input) {
                    $query->where('project_id', $input['filter_project']);
                });
            });

        return $query;
    }
}
