<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;


class Role extends EntrustRole
{
    public $table = 'roles';

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
        'name' => 'required|unique:roles,name',
        'display_name' => 'required',
    ];
}
