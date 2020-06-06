<?php
/**
 * Created by PhpStorm.
 * User: Shailesh Ladumor
 * email: shaileshmladumor@gmail.com
 * Date: 08-06-2019
 * Time: 01:22 PM.
 */

namespace App\Repositories;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Arr;
use Auth;
use Carbon\Carbon;

/**
 * Class DashboardRepository.
 */
class DashboardRepository
{
    /**
     * @param array $input
     *
     * @return array
     */
    public function getWorkReport($input)
    {
        $dates = $this->getDate($input['start_date'], $input['end_date']);

        $timeEntry = TimeEntry::with('task.project')
            ->ofUser($input['user_id'])
            ->whereBetween('start_time', [$dates['startDate'], $dates['endDate']])
            ->get();

        $projects = [];
        $totalHrs = [];
        /** @var TimeEntry $entry */
        foreach ($timeEntry as $entry) {
            $date = Carbon::parse($entry->start_time)->startOfDay()->format('Y-m-d');
            $name = $entry->task->project->name;
            $id = $entry->task->project->id;
            if (!isset($projects[$name])) {
                $projects[$name]['name'] = $name;
                $projects[$name]['id'] = $id;
            }
            if (!isset($projects[$name][$date])) {
                $projects[$name][$date] = 0;
            }
            $oldDuration = $projects[$name][$date];
            $projects[$name][$date] = $oldDuration + $entry->duration;
            if (!isset($totalHrs[$date])) {
                $totalHrs[$date] = 0;
            }

            $totalHrs[$date] += $entry->duration;
        }

        $data = [];
        $totalRecords = 0;
        $index = 0;
        /** @var TimeEntry $entry */
        foreach ($projects as $key => $entry) {
            $item['label'] = $entry['name'];
            $item['data'] = [];
            foreach ($dates['dateArr'] as $date) {
                $duration = isset($entry[$date]) ? $entry[$date] : 0;
                $item['data'][] = round($duration / 60, 2);
                $totalRecords = $totalRecords + $duration;
                $item['backgroundColor'] = getColor(0.7, getColorRGBCode($entry['id']));
            }
            $data[] = (object) $item;
            $index++;
        }

        $result = [];
        // preparing a date array for displaying a labels
        foreach ($dates['dateArr'] as $date) {
            $formattedDate = date('jS M', strtotime($date));
            if (isset($totalHrs[$date])) {
                $totalHrs[$formattedDate] = round($totalHrs[$date] / 60, 2);
                unset($totalHrs[$date]);
            } else {
                $totalHrs[$formattedDate] = 0;
            }
            $result['date'][] = $formattedDate;
        }
        $result['projects'] = array_keys($projects);
        $result['data'] = $data;
        $result['totalRecords'] = $totalRecords;
        $result['totalHrs'] = $totalHrs;
        $result['label'] = Carbon::parse($input['start_date'])->format('d M, Y').' - '.Carbon::parse($input['end_date'])->format('d M, Y');

        return $result;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     *
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
            /** @var \Illuminate\Support\Carbon $startDate */
            $startDate = Carbon::createFromFormat('Y-m-d', $start);
            /** @var \Illuminate\Support\Carbon $endDate */
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
            'dateArr'   => $dateArr,
            'startDate' => $subStartDate,
            'endDate'   => $subEndDate,
        ];

        return $data;
    }

    /**
     * @param array $input
     *
     * @return mixed
     */
    public function getDeveloperWorkReport($input)
    {
        $startDate = Carbon::parse($input['start_date'])->startOfDay()->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($input['start_date'])->endOfDay()->format('Y-m-d H:i:s');
        $timeEntry = TimeEntry::whereBetween('start_time', [$startDate, $endDate])->get();
        if (!authUserHasPermission('manage_users')) {
            $users = User::whereId(Auth::id())->get();
        } else {
            $users = User::active()->get();
        }
        $data['result'] = [];
        foreach ($users as $user) {
            $totalDuration = 0;

            /** @var TimeEntry $entry */
            foreach ($timeEntry as $entry) {
                if ($entry->user_id === $user->id) {
                    $totalDuration = $totalDuration + $entry->duration;
                }
            }

            $data['result'][] = (object) [
                'name'        => ucfirst($user->name),
                'total_hours' => round($totalDuration / 60, 2),
            ];
            $color = getColorRGBCode($user->id);
            $data['data']['backgroundColor'][] = getColor(0.3, $color);
            $data['data']['borderColor'][] = getColor(1, $color);
        }
        $data['totalRecords'] = 0;
        foreach ($data['result'] as $item) {
            $data['totalRecords'] = $data['totalRecords'] + $item->total_hours;
        }
        $data['label'] = Carbon::parse($input['start_date'])->startOfDay()->format('dS M, Y').' Report';
        $data['data']['labels'] = Arr::pluck($data['result'], 'name');
        $data['data']['data'] = Arr::pluck($data['result'], 'total_hours');

        return $data;
    }

    /**
     * @return mixed
     */
    public function getUserOpenTasks()
    {
        $tasks = Task::with(['project', 'taskAssignee'])->whereStatus(Task::STATUS_ACTIVE)->get();
        $result['name'] = [];
        $projects = [];
        /** @var TimeEntry $entry */
        foreach ($tasks as $task) {
            $name = $task->project->name;
            $id = $task->project->id;

            if (!isset($projects[$name])) {
                $projects[$name]['name'] = $name;
                $projects[$name]['id'] = $id;
            }
            $taskAssignees = $task->taskAssignee;
            /** @var User $taskAssignee */
            foreach ($taskAssignees as $taskAssignee) {
                $userName = $taskAssignee->name;
                if (!in_array($userName, $result['name'])) {
                    $result['name'][] = $userName;
                }

                if (!isset($projects[$name][$userName])) {
                    $projects[$name][$userName] = 0;
                }
                $projects[$name][$userName] = $projects[$name][$userName] + 1;
            }
        }

        $data = [];
        $totalRecords = 0;
        foreach ($projects as $key => $project) {
            $item['label'] = $project['name'];
            $item['data'] = [];
            foreach ($result['name'] as $userName) {
                $item['data'][] = isset($project[$userName]) ? $project[$userName] : 0;
                $totalRecords = $totalRecords + 1;
                $item['backgroundColor'] = getColor(0.7, getColorRGBCode($project['id']));
            }

            $data[] = (object) $item;
        }
        $result['data'] = $data;
        $result['totalRecords'] = $totalRecords;

        return $result;
    }
}
