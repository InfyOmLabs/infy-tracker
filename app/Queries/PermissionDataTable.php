<?php
namespace App\Queries;
use App\Models\Permission;

/**
 * Class PermissionDataTable
 * @package App\Queries
 */
class PermissionDataTable
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function get()
    {
        return Permission::query();
    }
}
