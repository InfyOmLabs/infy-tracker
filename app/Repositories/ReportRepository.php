<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 *
 * User: Ajay Makwana
 * Email: ajay.makwana@infyom.com
 * Date: 5/8/2019
 * Time: 11:25 AM
 */

namespace App\Repositories;

class ReportRepository
{
    /**
     * @return array
     */
    public function getReportData()
    {
        /** @var UserRepository $userRepo */
        $userRepo = app(UserRepository::class);
        $data['users'] = $userRepo->getUserList();

        /** @var ActivityTypeRepository $activityTypeRepo */
        $activityTypeRepo = app(ActivityTypeRepository::class);
        $data['activityTypes'] = $activityTypeRepo->getActivityTypeList();

        /** @var TaskRepository $taskRepo */
        $taskRepo = app(TaskRepository::class);
        $data['tasks'] = $taskRepo->getTaskList();

        /** @var ProjectRepository $projectRepo */
        $projectRepo = app(ProjectRepository::class);
        $data['projects'] = $projectRepo->getLoginUserAssignProjectsArr();

        return $data;
    }
}
