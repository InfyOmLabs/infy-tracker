<?php

namespace App\Repositories;

use App\Models\ActivityType;
use App\Repositories\BaseRepository;

/**
 * Class ActivityTypeRepository
 * @package App\Repositories
 * @version May 2, 2019, 10:52 am UTC
*/

class ActivityTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Return searchable fields
     *
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
        return ActivityType::class;
    }

    /**
     * get activity types
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActivityTypeList()
    {
        return ActivityType::orderBy('name')->pluck('name', 'id');
    }
}
