<?php

namespace App\Repositories;

use App\Models\Project;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class ProjectRepository.
 *
 * @version May 3, 2019, 5:06 am UTC
 */
class ProjectRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'team',
        'description',
        'client_id',
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
        return Project::class;
    }

    /***
     * @return mixed
     */
    public function getLoginUserAssignProjectsArr()
    {
        return Auth::user()->projects()->orderBy('name')->get()->pluck('name', 'id')->toArray();
    }

    /**
     * @return Project[]
     */
    public function getMyProjects()
    {
        $query = Project::whereHas('users', function (Builder $query) {
            $query->where('user_id', getLoggedInUserId());
        });

        /** @var Project[] $projects */
        $projects = $query->orderBy('name')->get();

        return $projects;
    }

    /**
     * get clients.
     *
     * @param int|null $clientId
     *
     * @return Collection
     */
    public function getProjectsList($clientId = null)
    {
        /** @var Builder|Project $query */
        $query = Project::orderBy('name');
        if (!is_null($clientId)) {
            $query = $query->whereClientId($clientId);
        }

        return $query->pluck('name', 'id');
    }
}
