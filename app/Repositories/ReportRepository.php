<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Tag;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ReportRepository
 * @package App\Repositories
 * @version July 6, 2019, 12:12 pm UTC
 */
class ReportRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'start_date',
        'end_date'
    ];

    /**
     * Return searchable fields
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Report::class;
    }

    /**
     * @param $input
     * @param Report $report
     * @return array
     */
    public function createReportFilter($input, $report)
    {
        $result = [];
        if (isset($input['projectIds'])) {
            foreach ($input['projectIds'] as $projectId) {
                $result[] = $this->createFilter($report->id, $projectId, Project::class);
            }
        }

        if (isset($input['userIds'])) {
            foreach ($input['userIds'] as $userId) {
                $result[] = $this->createFilter($report->id, $userId, User::class);
            }
        }

        if (isset($input['tagIds'])) {
            foreach ($input['tagIds'] as $tagId) {
                $result[] = $this->createFilter($report->id, $tagId, Tag::class);
            }
        }

        if (isset($input['client_id'])) {
            $result[] = $this->createFilter($report->id, $input['client_id'], Client::class);
        }
        return $result;
    }

    private function createFilter($reportId, $paramId, $type)
    {
        $filterInput['report_id'] = $reportId;
        $filterInput['param_id'] = $paramId;
        $filterInput['param_type'] = $type;
        return ReportFilter::create($filterInput);
    }

    /**
     * @param $input
     * @param $report
     * @return array
     * @throws \Exception
     */
    public function updateReportFilter($input, $report)
    {
        $result = [];
        $input['projectIds'] = isset($input['projectIds']) ? $input['projectIds'] : [];
        $input['userIds'] = isset($input['userIds']) ? $input['userIds'] : [];
        $input['tagIds'] = isset($input['tagIds']) ? $input['tagIds'] : [];
        $input['client_id'] = isset($input['client_id']) ? $input['client_id'] : 0;

        $projectIds = $this->getProjectIds($report->id);
        $ids = array_diff($input['projectIds'], (array)$projectIds);
        foreach ($ids as $projectId) {
            $result[] = $this->createFilter($report->id, $projectId, Project::class);
        }
        $deleteProjects = array_diff((array)$projectIds, $input['projectIds']);
        if (!empty($deleteProjects)) {
            ReportFilter::whereParamType(Project::class)->whereParamId($deleteProjects)->delete();
        }

        $userIds = $this->getUserIds($report->id);
        $ids = array_diff($input['userIds'], (array)$userIds);
        foreach ($ids as $userId) {
            $result[] = $this->createFilter($report->id, $userId, User::class);
        }
        $deleteUsers = array_diff((array)$userIds, $input['userIds']);
        if (!empty($deleteUsers)) {
            ReportFilter::whereParamType(User::class)->whereParamId($deleteUsers)->delete();
        }

        $tagIds = $this->getTagIds($report->id);
        $ids = array_diff($input['tagIds'], (array)$tagIds);
        foreach ($ids as $tagId) {
            $result[] = $this->createFilter($report->id, $tagId, Tag::class);
        }
        $deleteTags = array_diff((array)$tagIds, $input['tagIds']);
        if (!empty($deleteTags)) {
            ReportFilter::whereParamType(Tag::class)->whereParamId($deleteTags)->delete();
        }

        $clientId = $this->getClientId($report->id);
        if ($input['client_id'] != 0) {
            if ($input['client_id'] !== $clientId) {
                $result[] = $this->createFilter($report->id, $input['client_id'], Client::class);
            }
        }

        if (!empty($clientId) && $input['client_id'] !== $clientId) {
            ReportFilter::whereParamType(Client::class)->whereParamId($clientId)->delete();
        }
        return $result;
    }

    /**
     * @param $reportId
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function deleteFilter($reportId)
    {
        return ReportFilter::whereReportId($reportId)->delete();
    }

    /**
     * @param $reportId
     * @return array
     */
    public function getProjectIds($reportId)
    {
        return ReportFilter::whereParamType(Project::class)->whereReportId($reportId)->pluck('param_id')->toArray();
    }

    public function getTagIds($reportId)
    {
        return ReportFilter::whereParamType(Tag::class)->whereReportId($reportId)->pluck('param_id')->toArray();
    }

    /**
     * @param $reportId
     * @return array
     */
    public function getUserIds($reportId)
    {
        return ReportFilter::whereParamType(User::class)->whereReportId($reportId)->pluck('param_id')->toArray();
    }

    /**
     * @param $reportId
     * @return \Illuminate\Support\Collection
     */
    public function getClientId($reportId)
    {
        $report = ReportFilter::whereParamType(Client::class)->whereReportId($reportId)->first();
        if (empty($report)) {
            return null;
        }
        return $report->param_id;
    }

    /**
     * @param Report $report
     * @return TimeEntry[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function getReport($report)
    {
        $startDate = $report->start_date->startOfDay();
        $endDate = $report->end_date->endOfDay();
        $id = $report->id;

        $query = TimeEntry::with(['task', 'user', 'task.project.client', 'task.tags'])
            ->whereBetween('time_entries.start_time', [$startDate, $endDate]);

        $projectIds = $this->getProjectIds($id);
        $tagIds = $this->getTagIds($id);
        $userIds = $this->getUserIds($id);
        $clientId = $this->getClientId($id);

        $query->when(!empty($userIds), function (Builder $q) use ($userIds) {
            $q->whereIn('user_id', $userIds);
        });

        $query->when(!empty($projectIds), function (Builder $q) use ($projectIds) {
            $q->whereHas('task', function (Builder $query) use ($projectIds) {
                $query->whereIn('project_id', $projectIds);
            });
        });

        $query->when(!empty($tagIds), function (Builder $q) use ($tagIds) {
            $q->whereHas('task.tags', function (Builder $query) use ($tagIds) {
                $query->whereIn('tag_id', $tagIds);
            });
        });

        $query->when(!empty($clientId), function (Builder $q) use ($clientId) {
            $q->whereHas('task.project', function (Builder $query) use ($clientId) {
                $query->where('client_id', $clientId);
            });
        });

        $entries = $query->get();

        // Prepare report data in proper format
        $result = [];
        /** @var TimeEntry $entry */
        foreach ($entries as $entry) {
            $clientId = $entry->task->project->client_id;
            $project = $entry->task->project;
            $client = $project->client;
            $duration = $entry->duration;

            // prepare client and duration
            $result[$clientId]['name'] = $client->name;
            if (!isset($result[$clientId]['duration'])) {
                $result[$clientId]['duration'] = 0;
                $result[$clientId]['time'] = 0;
            }
            $result[$clientId]['duration'] = $duration + $result[$clientId]['duration'];
            $result[$clientId]['time'] = $this->getDurationTime($result[$clientId]['duration']);

            // prepare projects and duration
            $result[$clientId]['projects'][$project->id]['name'] = $project->name;
            if (!isset($result[$clientId]['projects'][$project->id]['duration'])) {
                $result[$clientId]['projects'][$project->id]['duration'] = 0;
                $result[$clientId]['projects'][$project->id]['time'] = 0;
            }
            $projectDuration = $result[$clientId]['projects'][$project->id]['duration'];
            $result[$clientId]['projects'][$project->id]['duration'] = $duration + $projectDuration;
            $result[$clientId]['projects'][$project->id]['time'] = $this->getDurationTime($duration + $projectDuration);

            // prepare users and duration
            $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['name'] = $entry->user->name;
            if (!isset($result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['duration'])) {
                $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['duration'] = 0;
                $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['time'] = 0;
            }

            $userDuration = $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['duration'];
            $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['duration'] = $duration + $userDuration;
            $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['time'] = $this->getDurationTime($duration + $userDuration);

            // prepare tasks and duration
            $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['tasks'][$entry->task_id]['name'] = $entry->task->title;
            if (!isset($result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['tasks'][$entry->task_id]['duration'])) {
                $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['tasks'][$entry->task_id]['duration'] = 0;
                $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['tasks'][$entry->task_id]['time'] = 0;
            }
            $time = $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['tasks'][$entry->task_id]['duration'] + $entry->duration;
            $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['tasks'][$entry->task_id]['duration'] = $time;
            $result[$clientId]['projects'][$project->id]['users'][$entry->user_id]['tasks'][$entry->task_id]['time'] = $this->getDurationTime($time);
        }
        return $result;
    }

    /**
     * @param int $minitues
     * @return string
     */
    private function getDurationTime($minitues)
    {
        if ($minitues == 0) {
            return '0 hr';
        }

        if ($minitues < 60) {
            return $minitues . ' min';
        }

        $hour = floor($minitues / 60);
        $min = $minitues - $hour * 60;
        if ($min === 0) {
            return $hour . ' hr';
        }
        return $hour . ' hr ' . $min . ' min';
    }
}
