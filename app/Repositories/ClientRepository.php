<?php

namespace App\Repositories;

use App\Models\Client;

/**
 * Class ClientRepository
 * @package App\Repositories
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
        return Client::class;
    }

    /**
     * get clients
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClientList()
    {
        return Client::orderBy('name')->pluck('name', 'id');
    }
}
