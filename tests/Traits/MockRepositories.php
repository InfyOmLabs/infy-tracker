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
 * Trait MockRepositories.
 */
trait MockRepositories
{
    static $user = 'userRepository';
    static $project = 'projectRepository';
    static $role = 'roleRepository';
    static $permission = 'permissionRepository';
    static $tag = 'tagRepository';
    static $client = 'clientRepository';
    static $dashboard = 'dashboardRepository';
    static $timeEntry = 'timeEntryRepository';
    static $task = 'taskRepository';
    static $activityType = 'activityTypeRepository';

    /** @var MockInterface */
    public $userRepository, $projectRepository, $roleRepository, $permissionRepository, $tagRepository,
        $clientRepository, $taskRepository, $dashboardRepository, $timeEntryRepository, $activityTypeRepository;

    public function mockRepo($repoNames)
    {
        if (!is_array($repoNames)) {
            $repoNames = [$repoNames];
        }

        foreach ($repoNames as $repoName) {
            $repoInstance = null;
            switch ($repoName) {
                case self::$user:
                    $repoInstance = UserRepository::class;
                    break;
                case self::$role:
                    $repoInstance = RoleRepository::class;
                    break;
                case self::$permission:
                    $repoInstance = PermissionRepository::class;
                    break;
                case self::$task:
                    $repoInstance = TaskRepository::class;
                    break;
                case self::$tag:
                    $repoInstance = TagRepository::class;
                    break;
                case self::$project:
                    $repoInstance = ProjectRepository::class;
                    break;
                case self::$activityType:
                    $repoInstance = ActivityTypeRepository::class;
                    break;
                case self::$client:
                    $repoInstance = ClientRepository::class;
                    break;
                case self::$dashboard:
                    $repoInstance = DashboardRepository::class;
                    break;
            }

            $this->$repoName = \Mockery::mock($repoInstance);
            app()->instance($repoInstance, $this->$repoName);
        }
    }
}
