<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 * Author: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 03-05-2019
 * Time: 12:59 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskTag
 *
 * @property int $id
 * @property int $task_id
 * @property string $tag_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskTag whereTaskId($value)
 * @mixin \Eloquent
 */
class TaskTag extends Model
{
    public $table = 'task_tags';

    public $timestamps = false;

    public $fillable = [
        'task_id',
        'tag_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'task_id' => 'integer',
        'tag_id'  => 'string',
    ];
}