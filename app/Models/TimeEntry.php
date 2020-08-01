<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TimeEntry.
 *
 * @property int                             $id
 * @property int                             $task_id
 * @property int                             $activity_type_id
 * @property int                             $user_id
 * @property string|null                     $start_time
 * @property string|null                     $end_time
 * @property int                             $duration
 * @property string                          $note
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ActivityType $activityType
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\User $user
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TimeEntry onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereActivityTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TimeEntry withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TimeEntry withoutTrashed()
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry ofUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry ofCurrentUser()
 *
 * @property int $entry_type
 * @property-read string $entry_type_string
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TimeEntry whereEntryType($value)
 */
class TimeEntry extends Model
{
    use SoftDeletes;

    public $table = 'time_entries';
    public $appends = ['entry_type_string'];

    const STOPWATCH = 1;
    const VIA_FORM = 2;

    public $fillable = [
        'task_id',
        'activity_type_id',
        'user_id',
        'start_time',
        'end_time',
        'duration',
        'entry_type',
        'note',
        'deleted_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'task_id'          => 'integer',
        'activity_type_id' => 'integer',
        'user_id'          => 'integer',
        'start_time'       => 'string',
        'end_time'         => 'string',
        'duration'         => 'integer',
        'note'             => 'string',
        'deleted_by'       => 'integer',
        'entry_type'       => 'integer',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'user_id'          => 'integer',
        'task_id'          => 'required',
        'activity_type_id' => 'required',
        'start_time'       => 'date|date_format:Y-m-d H:i:s',
        'end_time'         => 'date|date_format:Y-m-d H:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * @return BelongsTo
     */
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param Builder $query
     * @param int     $userId
     *
     * @return Builder
     */
    public function scopeOfUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeOfCurrentUser(Builder $query)
    {
        return $query->ofUser(getLoggedInUserId());
    }

    /**
     * @return string
     */
    public function getEntryTypeStringAttribute()
    {
        if ($this->entry_type == self::STOPWATCH) {
            return 'Stopwatch';
        }

        return 'Via Form';
    }
}
