<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class ClientRepository.
 */
class ClientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'website',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Client::class;
    }

    /**
     * get clients.
     *
     * @param null $departmentId
     *
     * @return Collection
     */
    public function getClientList($departmentId = null)
    {
        $query = Client::orderBy('name');

        if (!empty($departmentId)) {
            $query->where('department_id', '=', $departmentId);
        }

        return $query->pluck('name', 'id');
    }

    /**
     * @param int $clientId
     *
     * @throws Exception
     *
     * @return bool|mixed|void|null
     */
    public function delete($clientId)
    {
        /** @var Client $client */
        $client = $this->find($clientId);

        $projectIds = Project::where('client_id', '=', $client->id)->get()->pluck('id');
        $taskIds = Task::whereIn('project_id', $projectIds)->get()->pluck('id');

        TimeEntry::whereIn('task_id', $taskIds)->update(['deleted_by' => getLoggedInUserId()]);
        TimeEntry::whereIn('task_id', $taskIds)->delete();

        Task::whereIn('project_id', $projectIds)->update(['deleted_by' => getLoggedInUserId()]);
        Task::whereIn('project_id', $projectIds)->delete();

        $client->projects()->update(['deleted_by' => getLoggedInUserId()]);
        $client->projects()->delete();

        $client->update(['deleted_by' => getLoggedInUserId()]);
        $client->delete();
    }
}
