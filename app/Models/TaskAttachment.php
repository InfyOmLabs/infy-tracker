<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskAttachment.
 *
 * @property int $id
 * @property int $task_id
 * @property \Illuminate\Contracts\Routing\UrlGenerator|string $file
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskAttachment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaskAttachment extends Model
{
    public $table = 'task_attachments';

    public $fillable = [
        'task_id',
        'file',
    ];

    /**
     * @param $value
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getFileAttribute($value)
    {
        if (isset($value) && !empty($value)) {
            return url('attachments').'/'.$value;
        }

        return url('assets').'/img/default_image.png';
    }
}
