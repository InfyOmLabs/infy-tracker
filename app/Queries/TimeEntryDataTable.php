<?php

namespace App\Queries;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ClientDataTable.
 */
class TimeEntryDataTable
{
    /**
     * @param array $input
     *
     * @return TimeEntry|Builder
     */
    public function get($input)
    {
        /** @var TimeEntry $query */
        $query = TimeEntry::with(['task.project', 'user', 'activityType'])->select('time_entries.*');

        /** @var User $user */
        $user = Auth::user();

        $query->when(isset($input['filter_activity']) && !empty($input['filter_activity']),
            function (Builder $q) use ($input) {
                $q->where('activity_type_id', $input['filter_activity']);
            });
        $query->when(isset($input['filter_project']) && !empty($input['filter_project']),
            function (Builder $q) use ($input) {
                $filterUserId = (isset($input['filter_user']) && !empty($input['filter_user'])) ? $input['filter_user'] : getLoggedInUser()->id;
                $taskIds = Task::whereProjectId($input['filter_project'])
                    ->where(function ($q) use ($filterUserId) {
                        $q->whereHas('taskAssignee', function ($q) use ($filterUserId) {
                            $q->where('user_id', $filterUserId);
                        });
                    })->get()->pluck('id')->toArray();
                $q->whereIn('task_id', $taskIds);
            });
        if (!$user->can('manage_time_entries')) {
            return $query->OfCurrentUser();
        }
        $query->when(isset($input['filter_user']) && !empty($input['filter_user']),
            function (Builder $q) use ($input) {
                $q->where('user_id', $input['filter_user']);
            });

        return $query;
    }
}
