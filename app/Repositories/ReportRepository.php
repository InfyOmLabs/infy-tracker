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
            $report->projects()->wherePivot('param_type', Project::class)->sync($input['projectIds']);
//            foreach ($input['projectIds'] as $projectId) {
//                $result[] = $this->createFilter($report->id, $projectId, Project::class);
//            }
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
     * @return \Illuminate\Support\Collection
     */
    public function getProjectIds($reportId)
    {
        return ReportFilter::whereParamType(Project::class)->whereReportId($reportId)->pluck('param_id');
    }

    /**
     * @param $reportId
     * @return \Illuminate\Support\Collection
     */
    public function getTagIds($reportId)
    {
        return ReportFilter::whereParamType(Tag::class)->whereReportId($reportId)->pluck('param_id');
    }

    /**
     * @param $reportId
     * @return \Illuminate\Support\Collection
     */
    public function getUserIds($reportId)
    {
        return ReportFilter::whereParamType(User::class)->whereReportId($reportId)->pluck('param_id');
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
