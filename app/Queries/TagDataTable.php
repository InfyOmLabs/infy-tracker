<?php

namespace App\Queries;

use App\Models\Tag;
use Illuminate\Database\Query\Builder;

/**
 * Class TagDataTable
 * @package App\DataTables
 */
class TagDataTable
{
    /**
     * @param array $input
     *
     * @return Tag|Builder
     */
    public function get($input = [])
    {
        /** @var Tag $query */
        $query = Tag::query();

        return $query;
    }
}
