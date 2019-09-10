<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;

/**
 * Class ClientRepository.
 *
 * @version May 2, 2019, 10:16 am UTC
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
     * @return \Illuminate\Support\Collection
     */
    public function getClientList()
    {
        return Client::orderBy('name')->pluck('name', 'id');
    }

    /**
     * @param int $clientId
     *
     * @throws \Exception
     *
     * @return bool|mixed|void|null
     */
    public function delete($clientId)
    {
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
