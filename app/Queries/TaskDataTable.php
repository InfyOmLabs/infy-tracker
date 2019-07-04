<?php

namespace App\Queries;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class TaskDataTable
 * @package App\Queries
 */
class TaskDataTable
{
    /**
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function get($input = [])
    {
        $loginUserProjects = Auth::user()->projects()->get()->pluck('name', 'id')->toArray();
        $query = Task::whereIn('project_id', array_keys($loginUserProjects))
            ->with(['project', 'taskAssignee', 'createdUser'])
            ->select(['tasks.*']);

        $query->when(isset($input['filter_project']) && !empty($input['filter_project']),
            function (Builder $q) use ($input) {
                $q->where('project_id', $input['filter_project']);
            });

        $query->when(isset($input['filter_status']) && $input['filter_status'] != 2,
            function (Builder $q) use ($input) {
                $q->where('status', $input['filter_status']);
            });

        $query->when(isset($input['filter_user']),
            function (Builder $q) use ($input) {
                $q->whereHas('taskAssignee', function (Builder $q) use ($input) {
                    $q->where('user_id', $input['filter_user']);
                });
            });

        return $query;
    }
}
