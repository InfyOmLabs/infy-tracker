<?php
/**
 * Created by PhpStorm.
 * User: Shailesh Ladumor
 * email: shaileshmladumor@gmail.com
 * Date: 08-06-2019
 * Time: 01:22 PM
 */

namespace App\Repositories;

use App\Models\TimeEntry;
use App\Models\User;
use Arr;
use Carbon\Carbon;

class DashboardRepository
{
    /**
     * @param $input
     * @return array
     */
    public function getWorkReport($input)
    {
        $dates = $this->getDate($input['start_date'], $input['end_date']);
        $colors = getChartColors();
        $timeEntry = TimeEntry::with('task.project')
            ->whereUserId($input['user_id'])
            ->whereBetween('start_time', [$dates['startDate'], $dates['endDate']])
            ->get();

        $projects = [];
        /** @var TimeEntry $entry */
        foreach ($timeEntry as $entry) {
            $date = Carbon::parse($entry->start_time)->startOfDay()->format('Y-m-d');
            $name = $entry->task->project->name;
            if (!isset($projects[$name])) {
                $projects[$name]['name'] = $name;
            }
            if (!isset($projects[$name][$date])) {
                $projects[$name][$date] = 0;
            }
            $oldDuration = $projects[$name][$date];
            $projects[$name][$date] = $oldDuration + $entry->duration;
        }

        $data = [];
        $totalRecords = 0;
        $index = 0;
        /** @var TimeEntry $entry */
        foreach ($projects as $entry) {
            $item['label'] = $entry['name'];
            $item['backgroundColor'] = $colors[$index];
            $item['data'] = [];
            foreach ($dates['dateArr'] as $date) {
                $duration = isset($entry[$date]) ? $entry[$date] : 0;
                $item['data'][] = $duration;
                $totalRecords = $totalRecords + $duration;
            }
            $data[] = (object)$item;
            $index++;
        }

        $result = [];
        // preparing a date array for displaying a labels
        foreach ($dates['dateArr'] as $date) {
            $date = date("d-M", strtotime($date));
            $result['date'][] = $date;
        }
        $result['projects'] = array_keys($projects);
        $result['data'] = $data;
        $result['totalRecords'] = $totalRecords;
        $result['label'] = Carbon::parse($input['start_date'])->format('d M, Y') . ' - ' . Carbon::parse($input['end_date'])->format('d M, Y');
        return $result;
    }

    /**
     * @param $input
     * @return mixed
     */
    public function getDeveloperWorkReport($input)
    {
        $startDate = Carbon::parse($input['start_date'])->startOfDay()->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($input['start_date'])->endOfDay()->format('Y-m-d H:i:s');
        $timeEntry = TimeEntry::with(['task.project'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->get();
        $users = User::all();
        $data['drilldown'] = [];
        $data['result'] = [];
        foreach ($users as $user) {
            $totalDuration = 0;
            $projectData = [];
            /** @var TimeEntry $entry */
            foreach ($timeEntry as $entry) {
                if ($entry->user_id === $user->id) {
                    $projectId = $entry->task->project_id;
                    $totalDuration = $totalDuration + $entry->duration;
                    if (!isset($projectData[$projectId])) {
                        $projectData[$projectId] = [
                            ucfirst($entry->task->project->name),
                            0
                        ];
                    }
                    $projectData[$projectId][1] = $projectData[$projectId][1] + $entry->duration;
                }
            }

            $proData = [];
            foreach ($projectData as $item) {
                $item[1] = round($item[1] / 60, 2);
                $proData[] = $item;
            }
            if (count($proData) > 0) {
                $data['drilldown'][] =
                    (object)[
                        "name" => ucfirst($user->name),
                        "id" => ucfirst($user->name),
                        "data" => $proData
                    ];
            }

            $data['result'][] = (object)[
                "name" => ucfirst($user->name),
                "total_hours" => round($totalDuration / 60, 2),
                "drilldown" => $totalDuration === 0 ? null : ucfirst($user->name)
            ];
        }
        $data['totalRecords'] = 0;
        foreach ($data['result'] as $item) {
            $data['totalRecords'] = $data['totalRecords'] + $item->total_hours;
        }
        $data['label'] = Carbon::parse($input['start_date'])->startOfDay()->format('dS M, Y') . ' Report';
        $data['data']['labels'] = Arr::pluck($data['result'], 'name');
        $data['data']['data'] = Arr::pluck($data['result'], 'total_hours');
        $data['data']['backgroundColor'] = array_values(getBarChartColors());
        $data['data']['borderColor'] = array_keys(getBarChartColors());
        //unset($data['result']);
        return $data;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getDate($startDate, $endDate)
    {
        $dateArr = [];
        $subStartDate = '';
        $subEndDate = '';
        if ($startDate && $endDate) {
            $end = trim(substr($endDate, 0, 10));
            $start = Carbon::parse($startDate)->toDateString();
            $startDate = Carbon::createFromFormat('Y-m-d', $start);
            $endDate = Carbon::createFromFormat('Y-m-d', $end);

            while ($startDate <= $endDate) {
                $dateArr[] = $startDate->copy()->format('Y-m-d');
                $startDate->addDay();
            }
            $start = current($dateArr);
            $endDate = end($dateArr);
            $subStartDate = Carbon::parse($start)->startOfDay()->format('Y-m-d H:i:s');
            $subEndDate = Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s');
        }
        $data = [
            'dateArr' => $dateArr,
            'startDate' => $subStartDate,
            'endDate' => $subEndDate
        ];
        return $data;
    }
}
