<?php

namespace App\Queries;

use App\Models\Task;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TaskDataTable.
 */
class TaskDataTable
{
    /**
     * @param array $input
     *
     * @return Task
     */
    public function get($input = [])
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $loginUserProjects = $projectRepo->getLoginUserAssignProjectsArr();

        /** @var Task $query */
        $query = Task::whereIn('project_id', array_keys($loginUserProjects))
            ->leftJoin('projects as p', 'p.id', '=', 'tasks.project_id')
            ->with(['project', 'taskAssignee', 'createdUser'])
            ->select(['tasks.*']);

        $query->when(
            isset($input['filter_project']) && !empty($input['filter_project']),
            function (Builder $q) use ($input) {
                $q->ofProject($input['filter_project']);
            }
        );

        $query->when(
            isset($input['filter_status']) && $input['filter_status'] != Task::STATUS_ALL,
            function (Builder $q) use ($input) {
                $q->where('status', $input['filter_status']);
            }
        );

        $query->when(
            isset($input['due_date_filter']) && !empty($input['due_date_filter']),
            function (Builder $q) use ($input) {
                $q->where('due_date', $input['due_date_filter']);
            }
        );

        $query->when(
            isset($input['filter_user']),
            function (Builder $q) use ($input) {
                $q->whereHas('taskAssignee', function (Builder $q) use ($input) {
                    $q->where('user_id', $input['filter_user']);
                });
            }
        );

        return $query;
    }
}
