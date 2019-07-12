<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Task.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $project_id
 * @property int $status
 * @property string $due_date
 * @property int|null $created_by
 * @property int|null $deleted_by
 * @property string $time_entries_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdUser
 * @property-read \App\Models\Project $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $taskAssignee
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TimeEntry[] $timeEntries
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Task withoutTrashed()
 * @mixin \Eloquent
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TaskAttachment[] $attachments
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereTaskNumber($value)
 *
 * @property string|null $task_number
 * @property string|null $priority
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task wherePriority($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read mixed $prefix_task_number
 */
class Task extends Model
{
    use SoftDeletes;

    const STATUS_ACTIVE = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_ALL = 2;

    const STATUS_ARR = [
        self::STATUS_ALL => 'All',
        self::STATUS_ACTIVE => 'Pending',
        self::STATUS_COMPLETED => 'Accepted',
    ];
    const PRIORITY = ['highest' => 'HIGHEST', 'high' => 'HIGH', 'medium' => 'MEDIUM', 'low' => 'LOW', 'lowest' => 'LOWEST'];
    const PATH = 'attachments';

    public $table = 'tasks';

    public $fillable = [
        'title',
        'description',
        'project_id',
        'status',
        'due_date',
        'deleted_by',
        'created_by',
        'task_number',
        'priority',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'project_id' => 'integer',
        'status' => 'integer',
        'due_date' => 'date',
        'deleted_by' => 'integer',
        'created_by' => 'integer',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required',
        'project_id' => 'required',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tags', 'task_id', 'tag_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function getDueDateAttribute($value)
    {
        if (!empty($value)) {
            return Carbon::parse($value)->toDateString();
        }
    }

    /**
     * @return string
     */
    public function getPrefixTaskNumberAttribute()
    {
        return '#' . $this->project->prefix . '-' . $this->task_number;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class, 'task_id')->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taskAssignee()
    {
        return $this->belongsToMany(User::class, 'task_assignees', 'task_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class, 'task_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'task_id');
    }
}
