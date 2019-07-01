<?php

namespace App\Models;

use Zizaco\Entrust\EntrustPermission;


class Permission extends EntrustPermission
{
    public $table = 'permissions';

    public $fillable = [
        'name',
        'display_name',
        'description',
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'display_name' => 'string',
        'description' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:permissions,name',
        'display_name' => 'required',
    ];
}
