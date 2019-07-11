<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Report;
use App\Models\ReportFilter;
use App\Models\Tag;
use App\Models\User;

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

    public function updateReportFilter($input, $report)
    {
        $result = [];
        if (isset($input['projectIds'])) {
            $projectIds = $this->getProjectIds($report->id);
            $ids = array_diff($input['projectIds'], (array)$projectIds);
            foreach ($ids as $projectId) {
                $result[] = $this->createFilter($report->id, $projectId, Project::class);
            }
            $deleteProjects = array_diff((array)$projectIds, $input['projectIds']);
            if (!empty($deleteProjects)) {
                ReportFilter::whereParamType(Project::class)->whereParamId($deleteProjects)->delete();
            }
        }

        if (isset($input['userIds'])) {
            $userIds = $this->getUserIds($report->id);
            $ids = array_diff($input['userIds'], (array)$userIds);
            foreach ($ids as $userId) {
                $result[] = $this->createFilter($report->id, $userId, User::class);
            }
            $deleteUsers = array_diff((array)$userIds, $input['userIds']);
            if (!empty($deleteUsers)) {
                ReportFilter::whereParamType(User::class)->whereParamId($deleteUsers)->delete();
            }
        }

        if (isset($input['tagIds'])) {
            $tagIds = $this->getTagIds($report->id)->toArray();
            $ids = array_diff($input['tagIds'], (array)$tagIds);
            foreach ($ids as $tagId) {
                $result[] = $this->createFilter($report->id, $tagId, Tag::class);
            }
            $deleteTags = array_diff((array)$tagIds, $input['tagIds']);
            if (!empty($deleteTags)) {
                ReportFilter::whereParamType(Tag::class)->whereParamId($deleteTags)->delete();
            }
        }

        if (isset($input['client_id'])) {
            $clientId = $this->getClientId($report->id);
            if ($input['client_id'] != 0) {
                if ($input['client_id'] !== $clientId) {
                    $result[] = $this->createFilter($report->id, $input['client_id'], Client::class);
                }
            }

            if (!empty($clientId) && $input['client_id'] !== $clientId) {
                ReportFilter::whereParamType(Client::class)->whereParamId($clientId)->delete();
            }
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
}
