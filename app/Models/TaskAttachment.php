<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Model;
use Storage;

/**
 * App\Models\TaskAttachment.
 *
 * @property int $id
 * @property int $task_id
 * @property string|null $file
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $file_path
 * @property-read mixed $file_url
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
    use ImageTrait;

    const PATH = 'attachments';

    public $table = 'task_attachments';
    protected $appends = ['file_url'];

    public $fillable = [
        'task_id',
        'file',
    ];

    /**
     * @return string
     */
    public function getFilePathAttribute()
    {
        return Storage::path('attachments'.DIRECTORY_SEPARATOR.$this->file);
    }

    /**
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return $this->imageUrl(self::PATH.DIRECTORY_SEPARATOR.$this->file);
    }

    /**
     * @return bool
     */
    public function deleteAttachment()
    {
        $original = $this->getOriginal('file');
        if (!empty($original)) {
            return $this->deleteImage(self::PATH.DIRECTORY_SEPARATOR.$original);
        }
    }
}
