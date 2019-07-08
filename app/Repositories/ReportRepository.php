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
     * @param $report
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

        if (isset($input['tagsIds'])) {
            foreach ($input['tagsIds'] as $tagsId) {
                $result[] = $this->createFilter($report->id, $tagsId, Tag::class);
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
}
