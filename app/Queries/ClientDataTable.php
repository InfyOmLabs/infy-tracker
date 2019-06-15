<?php namespace App\Queries;

use App\Models\Client;
use Illuminate\Database\Query\Builder;


/**
 * Class ClientDataTable
 * @package App\Queries
 */
class ClientDataTable
{
    /**
     * @return Client|Builder
     */
    public function get()
    {
        /** @var Client $query */
        $query = Client::query();

        return $query;
    }
}