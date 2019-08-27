<?php

namespace Tests\Traits;

use App\Repositories\ActivityTypeRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\TagRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Mockery\MockInterface;

/**
 * Trait MockRepositories
 * @package Tests\Traits
 */
trait MockRepositories
{
    static $user = 'userRepo';
    static $project = 'projectRepo';
    static $role = 'roleRepo';
    static $permission = 'permissionRepo';
    static $tag = 'tagRepo';
    static $client = 'clientRepo';
    static $dashboard = 'dashboardRepo';
    static $timeEntry = 'timeEntryRepo';
    static $task = 'taskRepo';
    static $activityType = 'activityTypeRepo';

    /** @var MockInterface */
    public $userRepo, $projectRepo, $roleRepo, $permissionRepo, $tagRepo, $clientRepo, $taskRepo, $dashboardRepo,
        $timeEntryRepo, $activityTypeRepo;

    public function mockRepo($repoNames)
    {
        if (!is_array($repoNames)) {
            $repoNames = [$repoNames];
        }

        foreach ($repoNames as $repoName) {
            $repoInstance = null;
            switch ($repoName) {
                case self::$user;
                    $repoInstance = UserRepository::class;
                    break;
                case self::$role;
                    $repoInstance = RoleRepository::class;
                    break;
                case self::$permission;
                    $repoInstance = PermissionRepository::class;
                    break;
                case self::$task;
                    $repoInstance = TaskRepository::class;
                    break;
                case self::$tag;
                    $repoInstance = TagRepository::class;
                    break;
                case self::$project;
                    $repoInstance = ProjectRepository::class;
                    break;
                case self::$activityType;
                    $repoInstance = ActivityTypeRepository::class;
                    break;
                case self::$client;
                    $repoInstance = ClientRepository::class;
                    break;
                case self::$dashboard;
                    $repoInstance = DashboardRepository::class;
                    break;
            }

            $this->$repoName = \Mockery::mock($repoInstance);
            app()->instance($repoInstance, $this->$repoName);
        }
    }
}